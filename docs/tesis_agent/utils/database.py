import sqlite3
import json
from datetime import datetime
from pathlib import Path
from typing import Optional, Any
from contextlib import contextmanager
from config import Config


class DatabaseManager:
    def __init__(self):
        self.db_path = Config.DATABASE_PATH
        self._init_db()

    def _init_db(self):
        with self._get_conn() as conn:
            conn.executescript("""
                CREATE TABLE IF NOT EXISTS thesis_state (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section_id TEXT UNIQUE NOT NULL,
                    section_title TEXT NOT NULL,
                    chapter TEXT NOT NULL,
                    status TEXT DEFAULT 'pending',
                    content TEXT,
                    word_count INTEGER DEFAULT 0,
                    references_count INTEGER DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS generation_history (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section_id TEXT NOT NULL,
                    action TEXT NOT NULL,
                    prompt_tokens INTEGER DEFAULT 0,
                    completion_tokens INTEGER DEFAULT 0,
                    total_tokens INTEGER DEFAULT 0,
                    model_used TEXT,
                    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS notebooklm_entries (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    original_text TEXT NOT NULL,
                    processed_json TEXT,
                    source_section TEXT,
                    relevance TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS global_memory (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    key TEXT UNIQUE NOT NULL,
                    value TEXT NOT NULL,
                    category TEXT DEFAULT 'general',
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS references_table (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section_id TEXT NOT NULL,
                    reference_text TEXT NOT NULL,
                    apa_key TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            """)

    @contextmanager
    def _get_conn(self):
        conn = sqlite3.connect(str(self.db_path))
        conn.row_factory = sqlite3.Row
        conn.execute("PRAGMA journal_mode=WAL")
        conn.execute("PRAGMA foreign_keys=ON")
        try:
            yield conn
            conn.commit()
        except Exception:
            conn.rollback()
            raise
        finally:
            conn.close()

    def get_section_state(self, section_id: str) -> Optional[dict]:
        with self._get_conn() as conn:
            row = conn.execute(
                "SELECT * FROM thesis_state WHERE section_id = ?", (section_id,)
            ).fetchone()
            return dict(row) if row else None

    def upsert_section_state(self, section_id: str, section_title: str,
                              chapter: str, status: str = "pending",
                              content: str = "", word_count: int = 0):
        with self._get_conn() as conn:
            existing = conn.execute(
                "SELECT id FROM thesis_state WHERE section_id = ?", (section_id,)
            ).fetchone()
            if existing:
                conn.execute("""
                    UPDATE thesis_state SET status=?, content=?, word_count=?,
                    updated_at=CURRENT_TIMESTAMP WHERE section_id=?
                """, (status, content, word_count, section_id))
            else:
                conn.execute("""
                    INSERT INTO thesis_state (section_id, section_title, chapter, status, content, word_count)
                    VALUES (?, ?, ?, ?, ?, ?)
                """, (section_id, section_title, chapter, status, content, word_count))

    def get_pending_sections(self) -> list[dict]:
        with self._get_conn() as conn:
            rows = conn.execute(
                "SELECT * FROM thesis_state WHERE status = 'pending' ORDER BY section_id"
            ).fetchall()
            return [dict(r) for r in rows]

    def get_completed_sections(self) -> list[dict]:
        with self._get_conn() as conn:
            rows = conn.execute(
                "SELECT * FROM thesis_state WHERE status = 'completed' ORDER BY section_id"
            ).fetchall()
            return [dict(r) for r in rows]

    def log_generation(self, section_id: str, action: str,
                        prompt_tokens: int = 0, completion_tokens: int = 0,
                        model_used: str = ""):
        with self._get_conn() as conn:
            conn.execute("""
                INSERT INTO generation_history
                (section_id, action, prompt_tokens, completion_tokens, total_tokens, model_used)
                VALUES (?, ?, ?, ?, ?, ?)
            """, (section_id, action, prompt_tokens, completion_tokens,
                  prompt_tokens + completion_tokens, model_used))

    def save_notebooklm_entry(self, original_text: str, processed_json: str = "",
                               source_section: str = "", relevance: str = "media"):
        with self._get_conn() as conn:
            conn.execute("""
                INSERT INTO notebooklm_entries
                (original_text, processed_json, source_section, relevance)
                VALUES (?, ?, ?, ?)
            """, (original_text, processed_json, source_section, relevance))

    def set_memory(self, key: str, value: str, category: str = "general"):
        with self._get_conn() as conn:
            conn.execute("""
                INSERT INTO global_memory (key, value, category)
                VALUES (?, ?, ?)
                ON CONFLICT(key) DO UPDATE SET value=excluded.value,
                category=excluded.category, updated_at=CURRENT_TIMESTAMP
            """, (key, value, category))

    def get_memory(self, key: str) -> Optional[str]:
        with self._get_conn() as conn:
            row = conn.execute(
                "SELECT value FROM global_memory WHERE key = ?", (key,)
            ).fetchone()
            return row["value"] if row else None

    def get_all_memory(self, category: str = "") -> dict:
        with self._get_conn() as conn:
            if category:
                rows = conn.execute(
                    "SELECT key, value FROM global_memory WHERE category = ?",
                    (category,)
                ).fetchall()
            else:
                rows = conn.execute(
                    "SELECT key, value FROM global_memory"
                ).fetchall()
            return {r["key"]: r["value"] for r in rows}

    def save_reference(self, section_id: str, reference_text: str, apa_key: str = ""):
        with self._get_conn() as conn:
            conn.execute("""
                INSERT INTO references_table (section_id, reference_text, apa_key)
                VALUES (?, ?, ?)
            """, (section_id, reference_text, apa_key))

    def get_references(self, section_id: str = "") -> list[dict]:
        with self._get_conn() as conn:
            if section_id:
                rows = conn.execute(
                    "SELECT * FROM references_table WHERE section_id = ? ORDER BY reference_text",
                    (section_id,)
                ).fetchall()
            else:
                rows = conn.execute(
                    "SELECT * FROM references_table ORDER BY section_id, reference_text"
                ).fetchall()
            return [dict(r) for r in rows]

    def get_stats(self) -> dict:
        with self._get_conn() as conn:
            total = conn.execute("SELECT COUNT(*) as c FROM thesis_state").fetchone()["c"]
            completed = conn.execute(
                "SELECT COUNT(*) as c FROM thesis_state WHERE status='completed'"
            ).fetchone()["c"]
            pending = conn.execute(
                "SELECT COUNT(*) as c FROM thesis_state WHERE status='pending'"
            ).fetchone()["c"]
            total_words = conn.execute(
                "SELECT COALESCE(SUM(word_count), 0) as w FROM thesis_state"
            ).fetchone()["w"]
            total_refs = conn.execute(
                "SELECT COUNT(*) as c FROM references_table"
            ).fetchone()["c"]
            total_tokens = conn.execute(
                "SELECT COALESCE(SUM(total_tokens), 0) as t FROM generation_history"
            ).fetchone()["t"]
            return {
                "total_sections": total,
                "completed": completed,
                "pending": pending,
                "total_words": total_words,
                "total_references": total_refs,
                "total_tokens": total_tokens,
            }
