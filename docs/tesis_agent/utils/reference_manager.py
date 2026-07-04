import json
from pathlib import Path
from typing import Optional

from config import Config
from utils.database import DatabaseManager


class ReferenceManager:
    def __init__(self):
        self.db = DatabaseManager()

    def add_reference(self, section_id: str, reference_text: str):
        apa_key = self._extract_apa_key(reference_text)
        self.db.save_reference(section_id, reference_text, apa_key)

    def _extract_apa_key(self, reference: str) -> str:
        import re
        match = re.match(r'^([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+(?:\s+y\s+[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)?(?:\s*,\s*[A-ZÁÉÍÓÚÑ]\.)?)', reference)
        if match:
            return match.group(1).strip(",").strip()
        return reference[:30]

    def get_references_for_section(self, section_id: str) -> list[str]:
        refs = self.db.get_references(section_id)
        return [r["reference_text"] for r in refs]

    def get_all_references(self) -> list[str]:
        refs = self.db.get_references()
        seen = set()
        unique = []
        for r in refs:
            text = r["reference_text"]
            if text not in seen:
                seen.add(text)
                unique.append(text)
        return sorted(unique)

    def generate_section_references(self, section_context: str) -> list[str]:
        from openai import OpenAI
        client = OpenAI(api_key=Config.OPENAI_API_KEY)

        prompt_path = Config.PROMPTS_DIR / "generate_references.txt"
        prompt_template = prompt_path.read_text(encoding="utf-8")

        prompt = prompt_template.replace("{thesis_title}", Config.THESIS_TITLE)
        prompt = prompt.replace("{section_context}", section_context)

        try:
            response = client.chat.completions.create(
                model=Config.OPENAI_MODEL,
                messages=[{"role": "user", "content": prompt}],
                temperature=0.3,
                max_tokens=2000,
            )
            ref_text = response.choices[0].message.content.strip()
            refs = [r.strip() for r in ref_text.split("\n") if r.strip() and not r.startswith("#")]
            return refs
        except Exception as e:
            from utils.apa_formatter import APAFormatter
            return APAFormatter.generate_sample_references(section_context, count=5)

    def format_references_section(self) -> str:
        refs = self.get_all_references()
        if not refs:
            return ""

        formatted = "\n\n## Referencias\n\n"
        for i, ref in enumerate(refs, 1):
            formatted += f"{ref}\n\n"
        return formatted

    def export_references_to_bibtex(self) -> str:
        refs = self.get_all_references()
        bibtex = ""
        for i, ref in enumerate(refs):
            key = f"ref{i+1}"
            bibtex += f"@misc{{{key},\n  title = {{{ref}}},\n  year = {{{'2026'}}}\n}}\n\n"
        return bibtex
