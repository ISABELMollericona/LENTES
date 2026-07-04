import sqlite3
import json
import hashlib
from pathlib import Path
from config import Config


class AICache:
    def __init__(self):
        self.db_path = Config.CACHE_DIR / "ai_cache.db"
        self._init_db()

    def _init_db(self):
        self.db_path.parent.mkdir(parents=True, exist_ok=True)
        conn = sqlite3.connect(str(self.db_path))
        conn.execute("""
            CREATE TABLE IF NOT EXISTS ai_responses (
                image_hash TEXT PRIMARY KEY,
                image_path TEXT NOT NULL,
                response_json TEXT NOT NULL,
                model_used TEXT,
                dataset_name TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        """)
        conn.commit()
        conn.close()

    def _get_conn(self):
        conn = sqlite3.connect(str(self.db_path))
        conn.row_factory = sqlite3.Row
        return conn

    def get_hash(self, image_path: Path) -> str:
        hasher = hashlib.md5()
        with open(image_path, "rb") as f:
            for chunk in iter(lambda: f.read(65536), b""):
                hasher.update(chunk)
        return hasher.hexdigest()

    def get(self, image_path: Path) -> dict | None:
        img_hash = self.get_hash(image_path)
        conn = self._get_conn()
        try:
            row = conn.execute(
                "SELECT response_json FROM ai_responses WHERE image_hash = ?",
                (img_hash,)
            ).fetchone()
            if row:
                return json.loads(row["response_json"])
            return None
        finally:
            conn.close()

    def set(self, image_path: Path, response_data: dict, model_used: str = "",
            dataset_name: str = ""):
        img_hash = self.get_hash(image_path)
        conn = self._get_conn()
        try:
            conn.execute("""
                INSERT OR REPLACE INTO ai_responses
                (image_hash, image_path, response_json, model_used, dataset_name)
                VALUES (?, ?, ?, ?, ?)
            """, (
                img_hash,
                str(image_path),
                json.dumps(response_data, ensure_ascii=False),
                model_used,
                dataset_name
            ))
            conn.commit()
        finally:
            conn.close()

    def has(self, image_path: Path) -> bool:
        img_hash = self.get_hash(image_path)
        conn = self._get_conn()
        try:
            row = conn.execute(
                "SELECT 1 FROM ai_responses WHERE image_hash = ?",
                (img_hash,)
            ).fetchone()
            return row is not None
        finally:
            conn.close()

    def get_stats(self) -> dict:
        conn = self._get_conn()
        try:
            total = conn.execute("SELECT COUNT(*) as c FROM ai_responses").fetchone()["c"]
            return {"cached_responses": total}
        finally:
            conn.close()
