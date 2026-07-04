import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()


class Config:
    PROJECT_ROOT = Path(__file__).parent.resolve()
    SCRIPTS_DIR = PROJECT_ROOT.parent
    LARAVEL_ROOT = Path(os.getenv("LARAVEL_PROJECT_PATH", SCRIPTS_DIR))

    DATASET_PATH = Path(os.getenv("DATASET_PATH", PROJECT_ROOT / ".." / ".." / "dataset")).resolve()
    IMAGES_DEST = LARAVEL_ROOT / os.getenv("IMAGES_DEST_PATH", "storage/app/public/lentes")
    PUBLIC_STORAGE = LARAVEL_ROOT / "public" / "storage"

    LOG_DIR = PROJECT_ROOT / "logs"
    CACHE_DIR = PROJECT_ROOT / "cache"
    TEMP_DIR = PROJECT_ROOT / "temp"
    PROC_IMG_DIR = PROJECT_ROOT / "imagenes_procesadas"

    DB_CONNECTION = os.getenv("DB_CONNECTION", "pgsql")
    DB_HOST = os.getenv("DB_HOST", "localhost")
    DB_PORT = int(os.getenv("DB_PORT", 5432))
    DB_DATABASE = os.getenv("DB_DATABASE", "neondb")
    DB_USERNAME = os.getenv("DB_USERNAME", "neondb_owner")
    DB_PASSWORD = os.getenv("DB_PASSWORD", "")
    DB_SSLMODE = os.getenv("DB_SSLMODE", "require")

    AI_PRIMARY_PROVIDER = os.getenv("AI_PRIMARY_PROVIDER", os.getenv("AI_PROVIDER", "gemini")).lower()
    AI_FALLBACK_PROVIDERS = os.getenv("AI_FALLBACK_PROVIDERS", "openai,groq")
    AI_PROVIDER = AI_PRIMARY_PROVIDER

    GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "")
    GEMINI_MODEL = os.getenv("GEMINI_MODEL", "gemini-2.0-flash-lite")

    OPENAI_API_KEY = os.getenv("OPENAI_API_KEY", "")
    OPENAI_MODEL = os.getenv("OPENAI_MODEL", "gpt-4o")

    GROQ_API_KEY = os.getenv("GROQ_API_KEY", "")
    GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.2-90b-vision-preview")

    BATCH_SIZE = int(os.getenv("BATCH_SIZE", 5))
    MAX_WORKERS = int(os.getenv("MAX_WORKERS", 3))
    RESUME_ENABLED = os.getenv("RESUME_ENABLED", "true").lower() == "true"
    CACHE_ENABLED = os.getenv("CACHE_ENABLED", "true").lower() == "true"
    API_DELAY_SECONDS = float(os.getenv("API_DELAY_SECONDS", "0.5"))
    MAX_IMAGES_PER_DATASET = int(os.getenv("MAX_IMAGES_PER_DATASET", "0"))

    LOG_FILE = LOG_DIR / "importacion.log"
    CACHE_FILE = CACHE_DIR / "ai_cache.json"

    SUPPORTED_EXTENSIONS = {".jpg", ".jpeg", ".png", ".webp", ".bmp"}
    IMAGE_MAX_SIZE = 10 * 1024 * 1024

    @classmethod
    def ensure_dirs(cls):
        for d in [cls.LOG_DIR, cls.CACHE_DIR, cls.TEMP_DIR, cls.PROC_IMG_DIR,
                  cls.IMAGES_DEST, cls.PUBLIC_STORAGE / "lentes"]:
            d.mkdir(parents=True, exist_ok=True)

    @classmethod
    def get_db_uri(cls):
        return f"postgresql://{cls.DB_USERNAME}:{cls.DB_PASSWORD}@{cls.DB_HOST}:{cls.DB_PORT}/{cls.DB_DATABASE}"

    @classmethod
    def get_db_params(cls):
        return {
            "host": cls.DB_HOST,
            "port": cls.DB_PORT,
            "dbname": cls.DB_DATABASE,
            "user": cls.DB_USERNAME,
            "password": cls.DB_PASSWORD,
            "sslmode": cls.DB_SSLMODE,
        }
