from pathlib import Path
from database.database_manager import DatabaseManager
from services.image_service import ImageService


class DuplicateServiceError(Exception):
    pass


class DuplicateService:
    def __init__(self):
        self.db = DatabaseManager()

    def is_duplicate_by_name(self, nombre: str) -> bool:
        return self.db.exists(
            "lentes", "LOWER(nombre) = LOWER(%s)", (nombre.strip(),)
        )

    def is_duplicate_by_hash(self, hash_imagen: str) -> bool:
        return self.db.exists(
            "imagenes_procesadas", "hash_imagen = %s", (hash_imagen,)
        )

    def is_duplicate_by_image_path(self, image_path: Path) -> tuple[bool, str]:
        hash_value = ImageService.compute_hash(image_path)
        return self.is_duplicate_by_hash(hash_value), hash_value

    def register_processed_image(self, hash_imagen: str, ruta_original: str,
                                  dataset_origen: str):
        self.db.insert("imagenes_procesadas", {
            "hash_imagen": hash_imagen,
            "ruta_original": ruta_original,
            "dataset_origen": dataset_origen,
        })
