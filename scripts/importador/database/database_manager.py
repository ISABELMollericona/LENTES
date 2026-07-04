import psycopg2
from psycopg2 import pool, extras
from contextlib import contextmanager
from pathlib import Path
from config import Config


class DatabaseError(Exception):
    pass


class DatabaseManager:
    _instance = None
    _pool = None

    def __new__(cls):
        if cls._instance is None:
            cls._instance = super().__new__(cls)
        return cls._instance

    def __init__(self):
        if self._pool is None:
            self._init_pool()

    def _init_pool(self):
        try:
            self._pool = pool.ThreadedConnectionPool(
                minconn=1,
                maxconn=Config.MAX_WORKERS + 2,
                **Config.get_db_params()
            )
        except Exception as e:
            raise DatabaseError(f"Error al crear pool de conexiones: {e}")

    @contextmanager
    def get_conn(self):
        conn = None
        try:
            conn = self._pool.getconn()
            if conn.closed:
                conn = self._pool.getconn()
            yield conn
            conn.commit()
        except Exception as e:
            if conn:
                conn.rollback()
            raise DatabaseError(f"Error de base de datos: {e}")
        finally:
            if conn and not conn.closed:
                self._pool.putconn(conn)

    def execute(self, query: str, params: tuple = None):
        with self.get_conn() as conn:
            with conn.cursor() as cur:
                cur.execute(query, params)
                if cur.description:
                    return cur.fetchall()
                return None

    def execute_many(self, query: str, params_list: list):
        with self.get_conn() as conn:
            with conn.cursor() as cur:
                extras.execute_batch(cur, query, params_list)
                conn.commit()

    def insert(self, table: str, data: dict) -> int:
        columns = list(data.keys())
        values = list(data.values())
        placeholders = ", ".join(["%s"] * len(columns))
        cols = ", ".join(columns)
        query = f"INSERT INTO {table} ({cols}) VALUES ({placeholders}) RETURNING id"
        with self.get_conn() as conn:
            with conn.cursor() as cur:
                cur.execute(query, values)
                return cur.fetchone()[0]

    def update(self, table: str, data: dict, where: str, where_params: tuple = None):
        set_clause = ", ".join([f"{k} = %s" for k in data.keys()])
        query = f"UPDATE {table} SET {set_clause} WHERE {where}"
        params = tuple(data.values()) + (where_params or ())
        with self.get_conn() as conn:
            with conn.cursor() as cur:
                cur.execute(query, params)
                return cur.rowcount

    def fetch_one(self, query: str, params: tuple = None):
        with self.get_conn() as conn:
            with conn.cursor(cursor_factory=extras.RealDictCursor) as cur:
                cur.execute(query, params)
                return cur.fetchone()

    def fetch_all(self, query: str, params: tuple = None):
        with self.get_conn() as conn:
            with conn.cursor(cursor_factory=extras.RealDictCursor) as cur:
                cur.execute(query, params)
                return cur.fetchall()

    def exists(self, table: str, where: str, params: tuple) -> bool:
        query = f"SELECT 1 FROM {table} WHERE {where} LIMIT 1"
        result = self.fetch_one(query, params)
        return result is not None

    def get_next_codigo(self) -> str:
        result = self.fetch_one(
            "SELECT codigo FROM lentes ORDER BY codigo DESC LIMIT 1"
        )
        if result and result["codigo"]:
            last_num = int(result["codigo"].replace("LEN", ""))
            return f"LEN{last_num + 1:06d}"
        return "LEN000001"

    def create_tables(self):
        queries = [
            """
            CREATE TABLE IF NOT EXISTS imagenes_procesadas (
                id SERIAL PRIMARY KEY,
                hash_imagen VARCHAR(64) NOT NULL UNIQUE,
                ruta_original TEXT NOT NULL,
                dataset_origen VARCHAR(255),
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """,
            """
            CREATE INDEX IF NOT EXISTS idx_imagenes_procesadas_hash
            ON imagenes_procesadas (hash_imagen)
            """,
        ]
        for q in queries:
            self.execute(q)

    def close(self):
        if self._pool:
            self._pool.closeall()
