from pathlib import Path
from config import Config
from services.image_service import ImageService


class DatasetServiceError(Exception):
    pass


class DatasetService:
    @staticmethod
    def discover_datasets() -> list[dict]:
        dataset_root = Config.DATASET_PATH
        if not dataset_root.exists():
            raise DatasetServiceError(
                f"El directorio de datasets no existe: {dataset_root}"
            )

        datasets = []
        max_per = Config.MAX_IMAGES_PER_DATASET
        for entry in sorted(dataset_root.iterdir()):
            if entry.is_dir() and not entry.name.startswith("."):
                images = ImageService.find_images(entry)
                if max_per > 0 and len(images) > max_per:
                    import random
                    random.seed(42)
                    images = sorted(random.sample(images, max_per))
                if images:
                    datasets.append({
                        "name": entry.name,
                        "path": entry,
                        "image_count": len(images),
                        "images": images,
                    })
        return datasets
