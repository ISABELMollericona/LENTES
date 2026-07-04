import json
import chromadb
from chromadb.config import Settings
from sentence_transformers import SentenceTransformer
from pathlib import Path
from typing import Optional

from config import Config
from utils.database import DatabaseManager


class MemoryManager:
    def __init__(self):
        self.db = DatabaseManager()
        self.embedder = SentenceTransformer("all-MiniLM-L6-v2")
        self.chroma_client = chromadb.PersistentClient(
            path=str(Config.VECTOR_DB_PATH),
            settings=Settings(anonymized_telemetry=False)
        )
        self.collection = self.chroma_client.get_or_create_collection(
            name="thesis_memory",
            metadata={"hnsw:space": "cosine"}
        )

    def store_section_embedding(self, section_id: str, content: str, metadata: dict = None):
        if not content.strip():
            return
        embedding = self.embedder.encode(content).tolist()
        meta = metadata or {}
        meta["section_id"] = section_id
        meta["type"] = "section_content"

        self.collection.upsert(
            ids=[section_id],
            embeddings=[embedding],
            metadatas=[meta],
            documents=[content]
        )

    def get_similar_sections(self, query: str, n_results: int = 5) -> list[dict]:
        if not query.strip():
            return []
        query_emb = self.embedder.encode(query).tolist()
        results = self.collection.query(
            query_embeddings=[query_emb],
            n_results=n_results
        )
        items = []
        if results["ids"] and results["ids"][0]:
            for i, doc_id in enumerate(results["ids"][0]):
                items.append({
                    "id": doc_id,
                    "content": results["documents"][0][i] if results["documents"] else "",
                    "metadata": results["metadatas"][0][i] if results["metadatas"] else {},
                    "distance": results["distances"][0][i] if results["distances"] else 0,
                })
        return items

    def build_global_context(self, current_section: str = "") -> str:
        context_parts = []

        memory = self.db.get_all_memory()
        if memory:
            for key, value in memory.items():
                if key.startswith("thesis_"):
                    context_parts.append(f"{key.replace('thesis_', '').replace('_', ' ').title()}: {value}")

        completed = self.db.get_completed_sections()
        for sec in completed[-5:]:
            content = sec.get("content", "")
            if content:
                preview = content[:300] + "..." if len(content) > 300 else content
                context_parts.append(f"\n--- {sec['section_id']} {sec['section_title']} ---\n{preview}")

        if current_section:
            similar = self.get_similar_sections(current_section, n_results=3)
            for item in similar:
                if item["content"]:
                    preview = item["content"][:200]
                    context_parts.append(f"\n[Contexto relacionado: {item['id']}]\n{preview}")

        return "\n\n".join(context_parts)

    def get_thesis_progress(self) -> dict:
        return self.db.get_stats()

    def store_notebooklm_insight(self, raw_text: str, processed_data: dict):
        self.db.save_notebooklm_entry(
            original_text=raw_text,
            processed_json=json.dumps(processed_data, ensure_ascii=False),
            source_section=processed_data.get("secciones_relacionadas", [""])[0] if processed_data.get("secciones_relacionadas") else "",
            relevance=processed_data.get("relevancia", "media")
        )

    def get_context_for_section(self, section_id: str, section_title: str) -> str:
        similar = self.get_similar_sections(section_id, n_results=3)
        context_parts = [f"Información relacionada a {section_id} {section_title}:"]

        for item in similar:
            if item["content"]:
                context_parts.append(f"\nDe {item['id']}: {item['content'][:500]}")

        notebooklm_entries = self.db._get_conn().__enter__().execute(
            "SELECT processed_json FROM notebooklm_entries WHERE source_section LIKE ? ORDER BY created_at DESC LIMIT 3",
            (f"{section_id.split('.')[0]}%",)
        ).fetchall()
        for entry in notebooklm_entries:
            if entry and entry["processed_json"]:
                try:
                    data = json.loads(entry["processed_json"])
                    summary = data.get("resumen_estructurado", "")
                    if summary:
                        context_parts.append(f"\n[NotebookLM]: {summary[:500]}")
                except (json.JSONDecodeError, KeyError):
                    pass

        return "\n\n".join(context_parts)
