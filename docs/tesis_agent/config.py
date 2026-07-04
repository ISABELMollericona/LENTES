import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()


class Config:
    PROJECT_ROOT = Path(__file__).parent.resolve()
    CREDENTIALS_DIR = PROJECT_ROOT / "credentials"
    PROMPTS_DIR = PROJECT_ROOT / "prompts"
    OUTPUT_DIR = PROJECT_ROOT / "output"
    VECTOR_DB_PATH = PROJECT_ROOT / os.getenv("CHROMA_DB_PATH", "vector_db")
    DATABASE_PATH = PROJECT_ROOT / os.getenv("DATABASE_PATH", "database.db")

    AI_PRIMARY_PROVIDER = os.getenv("AI_PRIMARY_PROVIDER", "openai")
    AI_FALLBACK_PROVIDERS = os.getenv("AI_FALLBACK_PROVIDERS", "gemini,groq")

    OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", "")
    OPENAI_MODEL = os.getenv("OPENAI_MODEL", "gpt-4o")
    EMBEDDING_MODEL = os.getenv("EMBEDDING_MODEL", "text-embedding-3-small")

    GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "")
    GEMINI_MODEL = os.getenv("GEMINI_MODEL", "gemini-2.0-flash-lite")

    GROQ_API_KEY = os.getenv("GROQ_API_KEY", "")
    GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.2-90b-vision-preview")

    GOOGLE_CREDENTIALS_FILE = CREDENTIALS_DIR / os.getenv("GOOGLE_CREDENTIALS_FILE", "google_credentials.json")
    GOOGLE_DOCS_TOPIC_ID = os.getenv("GOOGLE_DOCS_TOPIC_ID", "")

    THESIS_TITLE = os.getenv("THESIS_TITLE", "")
    AUTHOR_NAME = os.getenv("AUTHOR_NAME", "")
    UNIVERSITY = os.getenv("UNIVERSITY", "")
    CAREER = os.getenv("CAREER", "Ingeniería de Sistemas")
    YEAR = os.getenv("YEAR", "2026")

    TEMPERATURE = float(os.getenv("TEMPERATURE", "0.7"))
    MAX_TOKENS = int(os.getenv("MAX_TOKENS", "4096"))
    TOP_P = float(os.getenv("TOP_P", "0.9"))

    MAX_RETRIES = 3
    CHUNK_SIZE = 2000
    OVERLAP_SIZE = 200

    SECTIONS = {
        "capitulo_i": {
            "title": "CAPÍTULO I - INTRODUCCIÓN",
            "level": 1,
            "subsections": {
                "1.1": "Antecedentes",
                "1.2": "Planteamiento del problema",
                "1.2.1": "Situación problemática y/o requerimiento de la institución",
                "1.2.2": "Objeto de estudio",
                "1.2.3": "Estudio de soluciones",
                "1.2.4": "Pregunta de investigación",
                "1.3": "Objetivos de la investigación",
                "1.3.1": "Objetivo general",
                "1.3.2": "Objetivos específicos",
                "1.4": "Definición de variables",
                "1.5": "Delimitación",
                "1.5.1": "Límite temporal",
                "1.5.2": "Límite geográfico",
                "1.6": "Justificación",
                "1.6.1": "Justificación técnica",
                "1.6.2": "Justificación económica",
                "1.6.3": "Justificación social",
                "1.7": "Tipología de proyectos",
                "1.8": "Tipo y estudio de la investigación",
                "1.9": "Técnicas e instrumentos de investigación",
                "1.10": "Población y muestra",
            }
        },
        "capitulo_ii": {
            "title": "CAPÍTULO II - MARCO TEÓRICO",
            "level": 1,
            "subsections": {
                "2.1": "Comercio Electrónico (eCommerce)",
                "2.2": "Sistemas de Recomendación",
                "2.3": "Inteligencia Artificial",
                "2.4": "Asistentes Virtuales Inteligentes",
                "2.5": "APIs de Inteligencia Artificial",
                "2.6": "Visión Artificial",
                "2.7": "Análisis Facial",
                "2.8": "MediaPipe",
                "2.9": "Bases de Datos",
                "2.10": "PostgreSQL",
                "2.11": "Neon Database",
                "2.12": "Framework Laravel",
                "2.13": "Arquitectura Cliente-Servidor",
                "2.14": "APIs REST",
                "2.15": "Seguridad en Aplicaciones Web",
                "2.16": "Experiencia de Usuario (UX/UI)",
                "2.17": "Óptica y Lentes Oftálmicos",
            }
        },
        "capitulo_iii": {
            "title": "CAPÍTULO III - MARCO PRÁCTICO",
            "level": 1,
            "subsections": {
                "3.1": "Metodología de Desarrollo",
                "3.2": "Planificación y Gestión del Backlog",
                "3.3": "Casos de Uso",
                "3.4": "Sprint 1",
                "3.5": "Sprint 2",
                "3.6": "Sprint 3",
                "3.7": "Sprint 4",
            }
        },
        "capitulo_iv": {
            "title": "CAPÍTULO IV - ANÁLISIS DE VIABILIDAD",
            "level": 1,
            "subsections": {
                "4.1": "Estructura de costos y análisis de inversión",
                "4.2": "Análisis de rentabilidad",
                "4.3": "Factibilidad técnica",
                "4.4": "Factibilidad operativa",
                "4.5": "Conclusión de viabilidad",
            }
        },
        "capitulo_v": {
            "title": "CAPÍTULO V - CONCLUSIONES Y RECOMENDACIONES",
            "level": 1,
            "subsections": {
                "5.1": "Conclusiones",
                "5.2": "Recomendaciones",
            }
        },
        "bibliografia": {
            "title": "BIBLIOGRAFÍA",
            "level": 1,
            "subsections": {}
        },
        "anexos": {
            "title": "ANEXOS",
            "level": 1,
            "subsections": {}
        }
    }

    @classmethod
    def ensure_dirs(cls):
        for d in [cls.CREDENTIALS_DIR, cls.PROMPTS_DIR, cls.OUTPUT_DIR, cls.VECTOR_DB_PATH]:
            d.mkdir(parents=True, exist_ok=True)

    @classmethod
    def get_section_path(cls, section_id: str) -> list[str]:
        parts = section_id.split(".")
        if len(parts) == 1:
            for key, data in cls.SECTIONS.items():
                if key == section_id or data["title"].startswith(section_id.upper()):
                    return [data["title"]]
            return [section_id]
        chapter_num = parts[0]
        for key, data in cls.SECTIONS.items():
            if key.endswith(chapter_num) or key == f"capitulo_{chapter_num}":
                chapter_title = data["title"]
                sub_key = section_id
                if sub_key in data["subsections"]:
                    return [chapter_title, f"{sub_key} {data['subsections'][sub_key]}"]
                return [chapter_title, section_id]
        return [section_id]
