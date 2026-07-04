#!/usr/bin/env python3
"""
Importador automático de catálogo de lentes para Óptica Golden eCommerce.
Procesa datasets de imágenes, genera atributos mediante IA, y registra
los productos en la base de datos PostgreSQL (NeonDB).
"""

import sys
import logging
from pathlib import Path

from config import Config
from services.import_service import ImportService


def setup_logging():
    Config.ensure_dirs()

    log_file = Config.LOG_FILE
    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s | %(levelname)-8s | %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
        handlers=[
            logging.FileHandler(log_file, encoding="utf-8"),
            logging.StreamHandler(sys.stdout),
        ],
    )


def validate_config():
    errors = []

    if not Config.DATASET_PATH.exists():
        errors.append(f"Directorio de datasets no encontrado: {Config.DATASET_PATH}")

    if Config.AI_PROVIDER == "gemini" and not Config.GEMINI_API_KEY:
        errors.append("GEMINI_API_KEY no configurada en .env")
    elif Config.AI_PROVIDER == "groq" and not Config.GROQ_API_KEY:
        errors.append("GROQ_API_KEY no configurada en .env")
    elif Config.AI_PROVIDER not in ("gemini", "groq"):
        errors.append(f"AI_PROVIDER inválido: {Config.AI_PROVIDER}. Usa 'gemini' o 'groq'")

    return errors


def main():
    print("=" * 60)
    print("   IMPORTADOR AUTOMÁTICO DE CATÁLOGO DE LENTES")
    print("   Óptica Golden eCommerce")
    print("=" * 60)

    setup_logging()
    logger = logging.getLogger("importador")

    logger.info("Iniciando importador...")
    logger.info(f"Proveedor IA: {Config.AI_PROVIDER.upper()}")
    logger.info(f"Ruta datasets: {Config.DATASET_PATH}")
    logger.info(f"Workers: {Config.MAX_WORKERS}, Batch size: {Config.BATCH_SIZE}")

    errors = validate_config()
    if errors:
        for err in errors:
            logger.error(f"Error de configuración: {err}")
        print("\nCorrige los errores de configuración en el archivo .env")
        sys.exit(1)

    try:
        importer = ImportService()
        importer.run()
    except KeyboardInterrupt:
        logger.warning("\nProceso interrumpido por el usuario.")
        sys.exit(0)
    except Exception as e:
        logger.exception(f"Error fatal: {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()
