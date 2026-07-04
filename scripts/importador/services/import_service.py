import logging
import json
import time
from pathlib import Path
from concurrent.futures import ThreadPoolExecutor, as_completed
from datetime import datetime
from tqdm import tqdm

from config import Config
from database.database_manager import DatabaseManager, DatabaseError
from models.product import Product
from services.dataset_service import DatasetService, DatasetServiceError
from services.image_service import ImageService, ImageServiceError
from services.duplicate_service import DuplicateService, DuplicateServiceError
from ai.ai_product_generator import AIProductGenerator, AIError


logger = logging.getLogger("importador")


class ImportService:
    def __init__(self):
        self.db = DatabaseManager()
        self.ai_gen = AIProductGenerator()
        self.dup_service = DuplicateService()
        self.stats = {
            "datasets": 0,
            "imagenes_analizadas": 0,
            "insertados": 0,
            "omitidos": 0,
            "errores": 0,
            "errors_detail": [],
        }
        self.start_time = None
        self.processed_count = 0

    def run(self):
        self.start_time = time.time()
        logger.info("=" * 60)
        logger.info("INICIO DE IMPORTACIÓN DE CATÁLOGO DE LENTES")
        logger.info("=" * 60)

        Config.ensure_dirs()
        self.db.create_tables()

        datasets = self._discover_datasets()
        if not datasets:
            logger.warning("No se encontraron datasets con imágenes válidas.")
            self._print_report()
            return

        self.stats["datasets"] = len(datasets)

        for ds in datasets:
            self._process_dataset(ds)

        self._print_report()

    def _discover_datasets(self) -> list[dict]:
        logger.info("Descubriendo datasets...")
        try:
            datasets = DatasetService.discover_datasets()
            logger.info(f"Datasets encontrados: {len(datasets)}")
            for ds in datasets:
                logger.info(f"  -> {ds['name']}: {ds['image_count']} imágenes")
            return datasets
        except DatasetServiceError as e:
            logger.error(f"Error al descubrir datasets: {e}")
            return []

    def _process_dataset(self, dataset: dict):
        ds_name = dataset["name"]
        images = dataset["images"]
        logger.info(f"\n--- Procesando dataset: {ds_name} ({len(images)} imágenes) ---")

        batch_size = Config.BATCH_SIZE
        max_workers = Config.MAX_WORKERS

        for i in range(0, len(images), batch_size):
            batch = images[i : i + batch_size]
            self._process_batch(batch, ds_name, max_workers)

        logger.info(f"--- Dataset {ds_name} completado ---")

    def _process_batch(self, batch: list[Path], dataset_name: str, max_workers: int):
        with ThreadPoolExecutor(max_workers=max_workers) as executor:
            futures = {
                executor.submit(self._process_single_image, img_path, dataset_name): img_path
                for img_path in batch
            }
            for future in tqdm(
                as_completed(futures),
                total=len(futures),
                desc=f"  {dataset_name}",
                unit="img",
                leave=False,
            ):
                try:
                    future.result()
                except Exception as e:
                    self.stats["errores"] += 1
                    self.stats["errors_detail"].append(
                        f"{futures[future].name}: {str(e)}"
                    )
                    logger.error(f"Error en {futures[future].name}: {e}")

    def _process_single_image(self, image_path: Path, dataset_name: str):
        self.stats["imagenes_analizadas"] += 1
        img_name = image_path.name

        # 1. Verificar que la imagen sea válida
        if not ImageService.is_valid_image(image_path):
            logger.warning(f"Imagen inválida: {image_path}")
            self.stats["omitidos"] += 1
            return

        # 2. Verificar duplicados por hash
        is_dup, img_hash = self.dup_service.is_duplicate_by_image_path(image_path)
        if is_dup:
            logger.info(f"Imagen duplicada (hash): {img_name}")
            self.stats["omitidos"] += 1
            return

        # 3. Generar datos con IA
        try:
            product = self.ai_gen.generate(image_path, dataset_name)
        except AIError as e:
            logger.error(f"Error de IA en {img_name}: {e}")
            self.stats["errores"] += 1
            return

        if not product:
            logger.warning(f"No se pudieron generar datos para {img_name}")
            self.stats["omitidos"] += 1
            return

        # 4. Asignar código único
        product.codigo = self.db.get_next_codigo()

        # 5. Verificar duplicados por nombre
        if product.nombre and self.dup_service.is_duplicate_by_name(product.nombre):
            logger.info(f"Nombre duplicado: {product.nombre}")
            self.stats["omitidos"] += 1
            return

        # 6. Validar campos obligatorios
        if not product.is_valid():
            missing = product.missing_fields()
            logger.warning(f"Campos obligatorios faltantes en {img_name}: {missing}")
            self.stats["omitidos"] += 1
            return

        # 7. Copiar imagen
        try:
            image_rel_path = ImageService.copy_to_destination(image_path, product.codigo)
            product.imagen = image_rel_path
        except (ImageServiceError, OSError) as e:
            logger.error(f"Error copiando imagen {img_name}: {e}")
            self.stats["errores"] += 1
            return

        # 8. Insertar en BD
        product.estado = "disponible"
        try:
            db_data = product.to_db_dict()
            excluded = {"fecha_registro", "dataset_origen"}
            insert_data = {k: v for k, v in db_data.items() if k not in excluded}
            insert_data["dataset_origen"] = dataset_name
            self.db.insert("lentes", insert_data)
        except DatabaseError as e:
            logger.error(f"Error insertando {product.codigo}: {e}")
            # Intentar limpiar imagen copiada
            dest = Config.IMAGES_DEST / Path(product.imagen).name
            if dest.exists():
                dest.unlink(missing_ok=True)
            self.stats["errores"] += 1
            return

        # 9. Registrar imagen como procesada
        try:
            self.dup_service.register_processed_image(
                img_hash, str(image_path), dataset_name
            )
        except DatabaseError as e:
            logger.warning(f"No se pudo registrar hash para {img_name}: {e}")

        self.stats["insertados"] += 1
        logger.info(f"✓ Insertado {product.codigo} - {product.nombre} [{dataset_name}]")

        # Guardar checkpoint para reanudación
        if Config.RESUME_ENABLED:
            self._save_checkpoint()

    def _save_checkpoint(self):
        checkpoint = Config.TEMP_DIR / "checkpoint.json"
        try:
            checkpoint.write_text(
                json.dumps({
                    "stats": {k: v for k, v in self.stats.items()
                              if k != "errors_detail"},
                    "timestamp": datetime.now().isoformat(),
                }, indent=2),
                encoding="utf-8",
            )
        except OSError:
            pass

    def _print_report(self):
        elapsed = time.time() - self.start_time
        minutes, seconds = divmod(int(elapsed), 60)

        logger.info("\n" + "=" * 60)
        logger.info("REPORTE FINAL DE IMPORTACIÓN")
        logger.info("=" * 60)
        logger.info(f"DATASETS ENCONTRADOS:    {self.stats['datasets']}")
        logger.info(f"IMÁGENES ANALIZADAS:     {self.stats['imagenes_analizadas']}")
        logger.info(f"REGISTROS INSERTADOS:    {self.stats['insertados']}")
        logger.info(f"REGISTROS OMITIDOS:      {self.stats['omitidos']}")
        logger.info(f"ERRORES:                 {self.stats['errores']}")
        logger.info(f"TIEMPO TOTAL:            {minutes}m {seconds}s")
        if self.stats["errors_detail"]:
            logger.info("\nDETALLE DE ERRORES:")
            for err in self.stats["errors_detail"][:20]:
                logger.info(f"  - {err}")
            if len(self.stats["errors_detail"]) > 20:
                logger.info(f"  ... y {len(self.stats['errors_detail']) - 20} más")
        logger.info("=" * 60)

        # Mostrar también en consola
        print("\n" + "=" * 60)
        print("REPORTE FINAL DE IMPORTACIÓN".center(60))
        print("=" * 60)
        print(f"  DATASETS ENCONTRADOS:    {self.stats['datasets']}")
        print(f"  IMÁGENES ANALIZADAS:     {self.stats['imagenes_analizadas']}")
        print(f"  REGISTROS INSERTADOS:    {self.stats['insertados']}")
        print(f"  REGISTROS OMITIDOS:      {self.stats['omitidos']}")
        print(f"  ERRORES:                 {self.stats['errores']}")
        print(f"  TIEMPO TOTAL:            {minutes}m {seconds}s")
        print("=" * 60)
