import re
from typing import Optional
from config import Config


class PlagiarismReducer:
    def __init__(self):
        self.client = None
        if Config.OPENAI_API_KEY:
            from openai import OpenAI
            self.client = OpenAI(api_key=Config.OPENAI_API_KEY)

    def paraphrase(self, text: str, intensity: str = "high") -> str:
        if not text.strip():
            return text

        if not self.client:
            return self._basic_paraphrase(text)

        prompt_path = Config.PROMPTS_DIR / "paraphrase.txt"
        prompt_template = prompt_path.read_text(encoding="utf-8")
        prompt = prompt_template.replace("{original_text}", text)

        try:
            response = self.client.chat.completions.create(
                model=Config.OPENAI_MODEL,
                messages=[{"role": "user", "content": prompt}],
                temperature=0.8 if intensity == "high" else 0.5,
                max_tokens=Config.MAX_TOKENS,
            )
            return response.choices[0].message.content.strip()
        except Exception:
            return self._basic_paraphrase(text)

    def _basic_paraphrase(self, text: str) -> str:
        replacements = {
            r'\bla\b': 'la mencionada',
            r'\blos\b': 'los mencionados',
            r'\bes\b': 'se constituye como',
            r'\bse utiliza\b': 'se emplea',
            r'\bpermite\b': 'facilita',
            r'\bimportante\b': 'relevante',
            r'\bdesarrollar\b': 'implementar',
            r'\busar\b': 'emplear',
            r'\bcrear\b': 'generar',
            r'\bmuy\b': 'altamente',
            r'\bgrande\b': 'considerable',
            r'\bpequeño\b': 'reducido',
            r'\bcosa\b': 'elemento',
            r'\bhacer\b': 'realizar',
            r'\bdecir\b': 'señalar',
            r'\bporque\b': 'debido a que',
            r'\bentonces\b': 'por consiguiente',
            r'\bpero\b': 'no obstante',
            r'\btambién\b': 'asimismo',
            r'\bademás\b': 'adicionalmente',
        }

        result = text
        for pattern, replacement in replacements.items():
            result = re.sub(pattern, replacement, result, flags=re.IGNORECASE)

        sentences = re.split(r'(?<=[.!?])\s+', result)
        if len(sentences) > 1:
            mid = len(sentences) // 2
            sentences = sentences[mid:] + sentences[:mid]

        return " ".join(sentences)

    def check_similarity(self, text1: str, text2: str) -> float:
        words1 = set(text1.lower().split())
        words2 = set(text2.lower().split())

        if not words1 or not words2:
            return 0.0

        intersection = words1 & words2
        union = words1 | words2

        return len(intersection) / len(union) if union else 0.0

    def rewrite_paragraph(self, original: str, context: str = "") -> str:
        if self.client:
            return self.paraphrase(original, intensity="high")

        sentences = re.split(r'(?<=[.!?])\s+', original)
        if len(sentences) <= 2:
            return self._basic_paraphrase(original)

        import random
        random.seed(hash(original) % (2**32))
        indices = list(range(len(sentences)))
        random.shuffle(indices)

        reordered = [sentences[i] for i in indices]
        result = " ".join(reordered)
        result = self._basic_paraphrase(result)

        return result

    def add_citations(self, text: str, section_context: str = "") -> str:
        citation_insertions = [
            " En este sentido, diversos autores coinciden en que este enfoque resulta fundamental en el ámbito de la Ingeniería de Sistemas (Pressman, 2021).",
            " Este hallazgo se alinea con los postulados de Sommerville (2020) respecto a la ingeniería de software moderna.",
            " Como señalan Tanenbaum y Bos (2023), esta arquitectura constituye la base de los sistemas distribuidos contemporáneos.",
            " De acuerdo con la literatura especializada, estos factores determinan significativamente la calidad del producto final (Bass, Clements & Kazman, 2022).",
            " Investigaciones previas en el campo confirman la relevancia de este enfoque metodológico (Pressman, 2021; Sommerville, 2020).",
        ]

        import random
        random.seed(hash(text) % (2**32))
        citation = random.choice(citation_insertions)

        paragraphs = text.split("\n\n")
        if paragraphs:
            target = random.randint(0, len(paragraphs) - 1)
            paragraphs[target] = paragraphs[target].rstrip(".") + "." + citation

        return "\n\n".join(paragraphs)
