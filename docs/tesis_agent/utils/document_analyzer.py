import re
from collections import Counter
from config import Config


class DocumentAnalyzer:
    @staticmethod
    def detect_headings(content: str) -> list[dict]:
        headings = []
        lines = content.split("\n")
        for line in lines:
            line = line.strip()
            if not line:
                continue

            if re.match(r'^CAP[ÍI]TULO\s+\w+\s*[-–—]\s*', line, re.IGNORECASE):
                headings.append({"type": "chapter", "text": line, "level": 1})

            elif re.match(r'^\d+\.\d+(\.\d+)?\s+', line):
                num = line.split()[0]
                level = num.count(".") + 1
                headings.append({"type": "section", "text": line, "level": level, "number": num})

            elif line in ("BIBLIOGRAFÍA", "ANEXOS", "BIBLIOGRAPHY", "APPENDIX"):
                headings.append({"type": "special", "text": line, "level": 1})

        return headings

    @staticmethod
    def find_chapter_for_section(section_number: str) -> str:
        chapter_num = section_number.split(".")[0]
        chapter_map = {
            "1": "CAPÍTULO I - INTRODUCCIÓN",
            "2": "CAPÍTULO II - MARCO TEÓRICO",
            "3": "CAPÍTULO III - MARCO PRÁCTICO",
            "4": "CAPÍTULO IV - ANÁLISIS DE VIABILIDAD",
            "5": "CAPÍTULO V - CONCLUSIONES Y RECOMENDACIONES",
        }
        return chapter_map.get(chapter_num, f"CAPÍTULO {chapter_num}")

    @staticmethod
    def get_section_metadata(section_id: str) -> dict:
        for chapter_key, chapter_data in Config.SECTIONS.items():
            if section_id in chapter_data["subsections"]:
                return {
                    "chapter": chapter_data["title"],
                    "chapter_key": chapter_key,
                    "title": chapter_data["subsections"][section_id],
                    "section_id": section_id,
                }
        return {
            "chapter": "",
            "chapter_key": "",
            "title": section_id,
            "section_id": section_id,
        }

    @staticmethod
    def estimate_word_count(text: str) -> int:
        return len(text.split())

    @staticmethod
    def check_section_completeness(content: str, min_words: int = 100) -> dict:
        words = DocumentAnalyzer.estimate_word_count(content)
        has_references = bool(re.search(r'\([A-ZÁÉÍÓÚÑ][a-záéíóúñ]+,?\s*\d{4}\)', content))
        has_headings = bool(re.search(r'\n\d+\.\d+', content))
        paragraphs = [p for p in content.split("\n\n") if len(p.strip()) > 50]

        return {
            "word_count": words,
            "paragraph_count": len(paragraphs),
            "has_citations": has_references,
            "has_subheadings": has_headings,
            "is_complete": words >= min_words and has_references and len(paragraphs) >= 2
        }

    @staticmethod
    def identify_knowledge_gaps(content: str) -> list[str]:
        gaps = []
        required_topics = {
            "1": ["antecedentes", "problemática", "objetivos", "justificación"],
            "2": ["definición", "conceptos", "teoría", "framework", "metodología"],
            "3": ["implementación", "desarrollo", "sprint", "metodología ágil"],
            "4": ["costos", "viabilidad", "rentabilidad", "factibilidad"],
            "5": ["conclusión", "recomendación", "trabajo futuro"],
        }

        chapter_match = re.search(r'CAP[ÍI]TULO\s+(\w+)', content[:500], re.IGNORECASE)
        if chapter_match:
            roman = chapter_match.group(1)
            roman_map = {"I": "1", "II": "2", "III": "3", "IV": "4", "V": "5"}
            chapter_num = roman_map.get(roman.upper(), "")
            topics = required_topics.get(chapter_num, [])
            content_lower = content.lower()
            for topic in topics:
                if topic not in content_lower:
                    gaps.append(f"Falta contenido sobre: {topic}")

        return gaps
