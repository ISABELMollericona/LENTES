from typing import Optional
from config import Config
from utils.database import DatabaseManager
from utils.document_analyzer import DocumentAnalyzer


class ChapterManager:
    def __init__(self):
        self.db = DatabaseManager()
        self.analyzer = DocumentAnalyzer()

    def initialize_sections(self):
        for chapter_key, chapter_data in Config.SECTIONS.items():
            for section_id, section_title in chapter_data["subsections"].items():
                self.db.upsert_section_state(
                    section_id=section_id,
                    section_title=section_title,
                    chapter=chapter_data["title"],
                    status="pending"
                )

    def get_next_pending_section(self) -> Optional[dict]:
        sections = self.db.get_pending_sections()
        if not sections:
            return None

        def sort_key(s):
            parts = s["section_id"].split(".")
            return tuple(int(p) for p in parts)

        sections.sort(key=sort_key)
        return sections[0]

    def get_section_by_id(self, section_id: str) -> Optional[dict]:
        state = self.db.get_section_state(section_id)
        if state:
            return state
        metadata = self.analyzer.get_section_metadata(section_id)
        if metadata:
            return {
                "section_id": section_id,
                "section_title": metadata["title"],
                "chapter": metadata["chapter"],
                "status": "pending",
                "content": "",
            }
        return None

    def mark_section_completed(self, section_id: str, content: str,
                                word_count: int = 0):
        self.db.upsert_section_state(
            section_id=section_id,
            section_title="",
            chapter="",
            status="completed",
            content=content,
            word_count=word_count or len(content.split()),
        )

    def get_all_sections_status(self) -> list[dict]:
        return self.db.get_pending_sections() + self.db.get_completed_sections()

    def get_progress_summary(self) -> str:
        stats = self.db.get_stats()
        total = stats["total_sections"]
        completed = stats["completed"]
        pending = stats["pending"]
        pct = (completed / total * 100) if total > 0 else 0

        summary = [
            f"Progreso de la tesis: {completed}/{total} secciones ({pct:.1f}%)",
            f"Palabras totales: {stats['total_words']:,}",
            f"Referencias: {stats['total_references']}",
            f"Tokens consumidos: {stats['total_tokens']:,}",
        ]

        if pending > 0:
            next_sec = self.get_next_pending_section()
            if next_sec:
                summary.append(
                    f"Siguiente: {next_sec['section_id']} {next_sec['section_title']}"
                )

        pending_list = self.db.get_pending_sections()
        if pending_list:
            summary.append("\nSecciones pendientes:")
            for sec in pending_list:
                summary.append(f"  - {sec['section_id']} {sec['section_title']}")

        return "\n".join(summary)

    def get_chapter_content(self, chapter_key: str) -> str:
        chapter_data = Config.SECTIONS.get(chapter_key)
        if not chapter_data:
            return ""

        chapter_title = chapter_data["title"]
        content_parts = [f"\n\n{chapter_title}\n"]

        for section_id, section_title in chapter_data["subsections"].items():
            state = self.db.get_section_state(section_id)
            if state and state["content"]:
                content_parts.append(f"\n\n{section_id} {section_title}\n")
                content_parts.append(state["content"])

        return "\n".join(content_parts)

    def assemble_full_thesis(self) -> str:
        parts = [f"{Config.THESIS_TITLE}\n"]

        for chapter_key, chapter_data in Config.SECTIONS.items():
            chapter_content = self.get_chapter_content(chapter_key)
            if chapter_content.strip():
                parts.append(chapter_content)

        refs = self.db.get_references()
        if refs:
            parts.append("\n\nBIBLIOGRAFÍA\n")
            seen = set()
            for r in refs:
                text = r["reference_text"]
                if text not in seen:
                    seen.add(text)
                    parts.append(f"\n{text}")

        return "\n".join(parts)
