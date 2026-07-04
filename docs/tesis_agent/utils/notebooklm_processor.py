import json
import re
from typing import Optional
from config import Config
from utils.database import DatabaseManager


class NotebookLMProcessor:
    def __init__(self):
        self.db = DatabaseManager()
        self.client = None
        if Config.OPENAI_API_KEY:
            from openai import OpenAI
            self.client = OpenAI(api_key=Config.OPENAI_API_KEY)

    def process_raw_text(self, raw_text: str) -> dict:
        if not raw_text.strip():
            return {"conceptos_clave": [], "secciones_relacionadas": [], "relevancia": "baja",
                    "citas_encontradas": [], "resumen_estructurado": "", "relaciones": [],
                    "recomendacion_insercion": ""}

        if self.client:
            return self._process_with_ai(raw_text)

        return self._process_heuristic(raw_text)

    def _process_with_ai(self, raw_text: str) -> dict:
        prompt_path = Config.PROMPTS_DIR / "notebooklm_analyzer.txt"
        prompt_template = prompt_path.read_text(encoding="utf-8")
        prompt = prompt_template.replace("{raw_text}", raw_text)

        try:
            response = self.client.chat.completions.create(
                model=Config.OPENAI_MODEL,
                messages=[{"role": "user", "content": prompt}],
                response_format={"type": "json_object"},
                temperature=0.3,
                max_tokens=2000,
            )
            result = json.loads(response.choices[0].message.content)
            self.db.save_notebooklm_entry(
                original_text=raw_text,
                processed_json=json.dumps(result, ensure_ascii=False),
                source_section=result.get("secciones_relacionadas", [""])[0] if result.get("secciones_relacionadas") else "",
                relevance=result.get("relevancia", "media")
            )
            return result
        except Exception as e:
            return self._process_heuristic(raw_text)

    def _process_heuristic(self, raw_text: str) -> dict:
        concepts = self._extract_key_concepts(raw_text)
        sections = self._classify_to_sections(raw_text, concepts)
        citations = self._extract_citations(raw_text)

        summary = raw_text[:500] if len(raw_text) > 500 else raw_text
        summary = summary.strip()

        return {
            "conceptos_clave": concepts,
            "secciones_relacionadas": sections,
            "relevancia": "alta" if len(concepts) > 3 else "media",
            "citas_encontradas": citations,
            "resumen_estructurado": summary,
            "relaciones": [],
            "recomendacion_insercion": f"Esta información es relevante para las secciones {', '.join(sections)}. "
                                        "Se recomienda integrar los conceptos clave en el desarrollo de cada sección correspondiente."
        }

    def _extract_key_concepts(self, text: str) -> list[str]:
        tech_terms = [
            "ecommerce", "inteligencia artificial", "machine learning", "deep learning",
            "visión artificial", "análisis facial", "mediapipe", "laravel", "postgresql",
            "api rest", "recomendación", "montura", "lentes", "óptica", "backend",
            "frontend", "base de datos", "cloud computing", "neon database",
            "experiencia de usuario", "ux/ui", "seguridad web", "autenticación",
            "oauth", "sanctum", "mvc", "arquitectura", "microservicios",
            "sistema de recomendación", "filtrado colaborativo", "contenido híbrido",
            "chatbot", "asistente virtual", "procesamiento de imágenes",
            "red neuronal", "tensorflow", "pytorch", "opencv", "dlib",
        ]

        found = []
        text_lower = text.lower()
        for term in tech_terms:
            if term in text_lower:
                found.append(term)

        return found[:10] if found else ["concepto técnico no identificado"]

    def _classify_to_sections(self, text: str, concepts: list) -> list[str]:
        section_map = {
            "1.1": ["antecedentes", "contexto", "historia", "evolución"],
            "1.2": ["problema", "problemática", "situación", "requerimiento"],
            "1.3": ["objetivo", "meta", "propósito"],
            "1.4": ["variable", "dimensión", "indicador"],
            "2.1": ["ecommerce", "comercio electrónico", "tienda virtual"],
            "2.2": ["recomendación", "sistema de recomendación", "filtrado"],
            "2.3": ["inteligencia artificial", "machine learning", "deep learning"],
            "2.4": ["asistente virtual", "chatbot", "agente inteligente"],
            "2.5": ["api", "servicio web", "integración"],
            "2.6": ["visión artificial", "procesamiento imágenes", "computer vision"],
            "2.7": ["análisis facial", "face detection", "reconocimiento facial"],
            "2.8": ["mediapipe", "google mediapipe"],
            "2.10": ["postgresql", "base de datos relacional"],
            "2.12": ["laravel", "framework php"],
            "2.16": ["ux", "ui", "experiencia de usuario", "interfaz"],
        }

        text_lower = text.lower()
        matched = []
        for section, keywords in section_map.items():
            for kw in keywords:
                if kw in text_lower:
                    if section not in matched:
                        matched.append(section)
                    break

        return matched[:5] if matched else ["1.1"]

    def _extract_citations(self, text: str) -> list[str]:
        patterns = [
            r'\([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+y\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?(?:,\s*[A-ZÁÉÍÓÚÑ]\.)?\s*,\s*\d{4}\)',
            r'[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+\s+\([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+,\s*\d{4}\)',
            r'\([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+,\s*\d{4}\)',
        ]
        citations = []
        for pattern in patterns:
            found = re.findall(pattern, text)
            citations.extend(found)
        return list(set(citations))

    def feed_text(self, text: str):
        result = self.process_raw_text(text)
        print(f"  → Procesado: {len(result.get('conceptos_clave', []))} conceptos, "
              f"{len(result.get('secciones_relacionadas', []))} secciones")
        return result

    def get_relevant_insights(self, section_id: str) -> list[str]:
        insights = self.db._get_conn().__enter__().execute(
            "SELECT processed_json FROM notebooklm_entries WHERE source_section LIKE ? ORDER BY created_at DESC LIMIT 5",
            (f"{section_id.split('.')[0]}%",)
        ).fetchall()

        results = []
        for ins in insights:
            if ins and ins["processed_json"]:
                try:
                    data = json.loads(ins["processed_json"])
                    summary = data.get("resumen_estructurado", "")
                    if summary:
                        results.append(summary[:300])
                except (json.JSONDecodeError, KeyError):
                    pass
        return results
