import json
import time
import tiktoken
from pathlib import Path
from typing import Optional

from config import Config
from utils.database import DatabaseManager
from utils.google_docs_manager import GoogleDocsManager
from utils.memory_manager import MemoryManager
from utils.chapter_manager import ChapterManager
from utils.document_analyzer import DocumentAnalyzer
from utils.reference_manager import ReferenceManager
from utils.plagiarism_reducer import PlagiarismReducer
from utils.notebooklm_processor import NotebookLMProcessor
from utils.ai_engine import AIEngine, AIProviderError


class ThesisGenerator:
    def __init__(self, use_google_docs: bool = True):
        self.db = DatabaseManager()
        self.chapter_manager = ChapterManager()
        self.memory = MemoryManager()
        self.reference_manager = ReferenceManager()
        self.plagiarism_reducer = PlagiarismReducer()
        self.notebooklm = NotebookLMProcessor()
        self.analyzer = DocumentAnalyzer()
        self.google_docs = GoogleDocsManager() if use_google_docs else None
        self.ai_engine = AIEngine()
        self.stats = {
            "sections_generated": 0,
            "total_tokens": 0,
            "total_words": 0,
            "errors": 0,
            "start_time": None,
        }

    def initialize(self):
        Config.ensure_dirs()
        self.chapter_manager.initialize_sections()
        self.memory.store_section_embedding(
            "global",
            Config.THESIS_TITLE,
            {"type": "thesis_metadata", "title": Config.THESIS_TITLE}
        )
        self.db.set_memory("thesis_title", Config.THESIS_TITLE)
        self.db.set_memory("thesis_career", Config.CAREER)
        self.db.set_memory("thesis_year", Config.YEAR)
        print("✓ Sistema inicializado correctamente")

    def generate_all(self):
        self.stats["start_time"] = time.time()
        self.initialize()

        if not self.ai_engine.is_available():
            print("ERROR: Ningún proveedor de IA configurado en .env")
            print("Configura OPENAI_API_KEY, GEMINI_API_KEY o GROQ_API_KEY")
            return

        providers = self.ai_engine.get_available_providers()
        print(f"\nProveedores IA disponibles: {', '.join(providers)}")

        print(f"\n{'='*60}")
        print(f"Generando tesis: {Config.THESIS_TITLE}")
        print(f"{'='*60}\n")

        while True:
            section = self.chapter_manager.get_next_pending_section()
            if not section:
                print("\n✓ Todas las secciones han sido generadas.")
                break

            self._generate_section(section)

        self._finalize_thesis()

    def generate_section(self, section_id: str):
        if not self.ai_engine.is_available():
            print("ERROR: Ningún proveedor de IA configurado en .env")
            return

        section = self.chapter_manager.get_section_by_id(section_id)
        if not section:
            print(f"Sección {section_id} no encontrada.")
            return

        self.stats["start_time"] = self.stats["start_time"] or time.time()
        self._generate_section(section)

    def _generate_section(self, section: dict):
        section_id = section["section_id"]
        section_title = section["section_title"]
        chapter = section.get("chapter", "")
        chapter_key = self._get_chapter_key(chapter)

        print(f"\n--- Generando {section_id} {section_title} ---")
        print(f"  Capítulo: {chapter}")

        global_context = self.memory.build_global_context(section_id)
        notebooklm_insights = self.notebooklm.get_relevant_insights(section_id)
        reference_material = "\n\n".join(notebooklm_insights)

        prompt_path = Config.PROMPTS_DIR / "generate_section.txt"
        prompt_template = prompt_path.read_text(encoding="utf-8")

        prompt = (prompt_template
                  .replace("{section_number}", section_id)
                  .replace("{section_title}", section_title)
                  .replace("{chapter_title}", chapter)
                  .replace("{thesis_title}", Config.THESIS_TITLE)
                  .replace("{career}", Config.CAREER)
                  .replace("{author}", Config.AUTHOR_NAME or "El autor")
                  .replace("{university}", Config.UNIVERSITY or "la universidad")
                  .replace("{year}", Config.YEAR)
                  .replace("{global_context}", global_context)
                  .replace("{reference_material}", reference_material))

        try:
            content, model_used, usage = self.ai_engine.generate_with_retry(
                prompt=prompt,
                temperature=Config.TEMPERATURE,
                max_tokens=Config.MAX_TOKENS,
            )

            # Parafrasear para reducir similitud
            paraphrased = self.plagiarism_reducer.rewrite_paragraph(content, section_title)
            paraphrased = self.plagiarism_reducer.add_citations(paraphrased, section_title)
            final_content = self._apply_apa_formatting(paraphrased, section)

            word_count = len(final_content.split())

            # Guardar en base de datos
            self.chapter_manager.mark_section_completed(section_id, final_content, word_count)

            # Guardar en memoria vectorial
            self.memory.store_section_embedding(
                section_id,
                final_content,
                {"chapter": chapter, "title": section_title, "type": "section"}
            )

            # Registrar en historial
            self.db.log_generation(
                section_id=section_id,
                action="generate",
                prompt_tokens=usage.get("prompt_tokens", 0),
                completion_tokens=usage.get("completion_tokens", 0),
                model_used=model_used,
            )

            # Generar referencias
            refs = self.reference_manager.generate_section_references(
                f"{section_title} - {chapter}"
            )
            for ref in refs:
                self.reference_manager.add_reference(section_id, ref)

            # Actualizar Google Docs
            if self.google_docs:
                self._update_google_docs(section_id, section_title, final_content)

            self.stats["sections_generated"] += 1
            self.stats["total_tokens"] += usage.get("total_tokens", 0)
            self.stats["total_words"] += word_count

            print(f"  ✓ {word_count} palabras generadas")
            print(f"  ✓ {len(refs)} referencias añadidas")
            print(f"  ✓ Modelo usado: {model_used}")

        except AIProviderError as e:
            self.stats["errors"] += 1
            print(f"  ✗ Todos los proveedores fallaron: {e}")
            self.db.log_generation(section_id, "error", 0, 0, "all_providers_failed")
        except Exception as e:
            self.stats["errors"] += 1
            print(f"  ✗ Error: {e}")
            self.db.log_generation(section_id, "error", 0, 0, "unknown")

    def _apply_apa_formatting(self, content: str, section: dict) -> str:
        section_id = section["section_id"]
        section_title = section["section_title"]

        parts = content.split("\n\n")
        formatted_paragraphs = []

        for i, para in enumerate(parts):
            para = para.strip()
            if not para:
                continue
            if para.startswith("#") or para.startswith("##"):
                formatted_paragraphs.append(para)
            elif para.startswith("- ") or para.startswith("* "):
                formatted_paragraphs.append(para)
            else:
                sentences = para.split(". ")
                justified = ". ".join(s.strip() for s in sentences if s.strip())
                if justified and not justified.endswith("."):
                    justified += "."
                formatted_paragraphs.append(justified)

        return "\n\n".join(formatted_paragraphs)

    def _update_google_docs(self, section_id: str, section_title: str, content: str):
        try:
            heading = f"{section_id} {section_title}"
            full_content = f"\n\n{heading}\n\n{content}"
            self.google_docs.insert_content(heading, content)
        except Exception as e:
            print(f"  ⚠ Google Docs: {e}")

    def _get_chapter_key(self, chapter_title: str) -> str:
        for key, data in Config.SECTIONS.items():
            if data["title"] == chapter_title:
                return key
        return ""

    def feed_notebooklm(self, text: str):
        print("\n--- Procesando información de NotebookLM ---")
        result = self.notebooklm.feed_text(text)
        print(f"  Conceptos clave: {', '.join(result.get('conceptos_clave', []))}")
        print(f"  Secciones relacionadas: {', '.join(result.get('secciones_relacionadas', []))}")
        print(f"  Relevancia: {result.get('relevancia', 'media')}")

        if result.get("resumen_estructurado"):
            for sec_id in result.get("secciones_relacionadas", []):
                section = self.chapter_manager.get_section_by_id(sec_id)
                if section:
                    self.memory.store_section_embedding(
                        f"ref_{sec_id}_{int(time.time())}",
                        result["resumen_estructurado"],
                        {"type": "notebooklm", "section": sec_id, "source": "notebooklm"}
                    )

        return result

    def _finalize_thesis(self):
        print(f"\n{'='*60}")
        print("FINALIZANDO TESIS")
        print(f"{'='*60}")

        full_thesis = self.chapter_manager.assemble_full_thesis()

        output_path = Config.OUTPUT_DIR / "tesis_completa.txt"
        output_path.write_text(full_thesis, encoding="utf-8")
        print(f"  ✓ Tesis guardada en: {output_path}")

        if self.google_docs:
            try:
                self.google_docs._replace_all_content(full_thesis)
                print(f"  ✓ Documento actualizado en Google Docs")
            except Exception as e:
                print(f"  ⚠ Error actualizando Google Docs: {e}")

        elapsed = time.time() - self.stats["start_time"]
        minutes, seconds = divmod(int(elapsed), 60)

        self.db.set_memory("thesis_completed", "true")
        self.db.set_memory("thesis_completion_date", time.strftime("%Y-%m-%d %H:%M:%S"))
        self.db.set_memory("thesis_total_words", str(self.stats["total_words"]))
        self.db.set_memory("thesis_total_tokens", str(self.stats["total_tokens"]))

        print(f"\n{'='*60}")
        print("RESUMEN FINAL")
        print(f"{'='*60}")
        print(f"  Secciones generadas: {self.stats['sections_generated']}")
        print(f"  Palabras totales: {self.stats['total_words']:,}")
        print(f"  Tokens consumidos: {self.stats['total_tokens']:,}")
        print(f"  Errores: {self.stats['errors']}")
        print(f"  Tiempo total: {minutes}m {seconds}s")
        print(f"{'='*60}")

    def get_progress_report(self) -> str:
        return self.chapter_manager.get_progress_summary()

    def get_stats(self) -> dict:
        base_stats = self.db.get_stats()
        base_stats.update(self.stats)
        return base_stats
