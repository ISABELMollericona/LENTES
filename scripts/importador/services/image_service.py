import hashlib
import shutil
from pathlib import Path
from config import Config


class ImageServiceError(Exception):
    pass


class ImageService:
    @staticmethod
    def compute_hash(image_path: Path) -> str:
        hasher = hashlib.sha256()
        with open(image_path, "rb") as f:
            for chunk in iter(lambda: f.read(65536), b""):
                hasher.update(chunk)
        return hasher.hexdigest()

    @staticmethod
    def is_valid_image(image_path: Path) -> bool:
        if not image_path.exists():
            return False
        if image_path.suffix.lower() not in Config.SUPPORTED_EXTENSIONS:
            return False
        if image_path.stat().st_size > Config.IMAGE_MAX_SIZE:
            return False
        try:
            from PIL import Image
            with Image.open(image_path) as img:
                img.verify()
            return True
        except Exception:
            return False

    @staticmethod
    def copy_to_destination(image_path: Path, codigo: str) -> str:
        dest_ext = image_path.suffix.lower()
        dest_filename = f"{codigo}{dest_ext}"
        dest_path = Config.IMAGES_DEST / dest_filename
        public_path = Config.PUBLIC_STORAGE / "lentes" / dest_filename

        shutil.copy2(image_path, dest_path)
        if not public_path.exists():
            public_path.parent.mkdir(parents=True, exist_ok=True)
            try:
                public_path.symlink_to(dest_path)
            except OSError:
                shutil.copy2(image_path, public_path)

        dest_path.parent.mkdir(parents=True, exist_ok=True)

        return f"lentes/{dest_filename}"

    @staticmethod
    def find_images(dataset_path: Path) -> list[Path]:
        images = []
        for ext in Config.SUPPORTED_EXTENSIONS:
            images.extend(dataset_path.rglob(f"*{ext}"))
        valid = []
        for img in images:
            if img.stat().st_size <= Config.IMAGE_MAX_SIZE:
                valid.append(img)
        return sorted(valid)
