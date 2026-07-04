import json
import hashlib
import base64
import time
from pathlib import Path
from typing import Optional
from config import Config
from models.product import Product
from ai.ai_cache import AICache


class AIError(Exception):
    pass


class AIProductGenerator:
    _last_call_time = 0

    def __init__(self):
        self.providers = self._init_providers()
        self.cache_enabled = Config.CACHE_ENABLED
        self.ai_cache = AICache() if self.cache_enabled else None
        self.model_last_used = ""

    def _rate_limit(self):
        elapsed = time.time() - self._last_call_time
        if elapsed < Config.API_DELAY_SECONDS:
            time.sleep(Config.API_DELAY_SECONDS - elapsed)
        self._last_call_time = time.time()

    def _init_providers(self) -> dict:
        providers = {}
        priority = 0

        if Config.OPENAI_API_KEY and "your_" not in Config.OPENAI_API_KEY.lower():
            providers["openai"] = {"priority": priority, "available": True}
            priority += 1

        if Config.GEMINI_API_KEY and "your_" not in Config.GEMINI_API_KEY.lower():
            providers["gemini"] = {"priority": priority, "available": True}
            priority += 1

        if Config.GROQ_API_KEY and "your_" not in Config.GROQ_API_KEY.lower():
            providers["groq"] = {"priority": priority, "available": True}
            priority += 1

        primary = Config.AI_PROVIDER
        if primary in providers:
            providers[primary]["priority"] = -1

        return providers

    def _encode_image(self, image_path: Path) -> str:
        with open(image_path, "rb") as f:
            return base64.b64encode(f.read()).decode("utf-8")

    def _resize_and_encode(self, image_path: Path, max_size: int = 800) -> str:
        from PIL import Image
        import io
        with Image.open(image_path) as img:
            if img.mode != "RGB":
                img = img.convert("RGB")
            w, h = img.size
            if w > max_size or h > max_size:
                ratio = min(max_size / w, max_size / h)
                img = img.resize((int(w * ratio), int(h * ratio)), Image.LANCZOS)
            buf = io.BytesIO()
            img.save(buf, format="JPEG", quality=85, optimize=True)
            return base64.b64encode(buf.getvalue()).decode("utf-8")

    def _build_prompt(self) -> str:
        return """Analiza esta imagen de lentes/anteojos y devuelve SOLO un JSON válido (sin markdown, sin explicaciones) con esta estructura exacta:

{
  "nombre": "Nombre comercial del producto",
  "marca": "Marca del lente",
  "categoria": "Categoria (ej: lentes_opticos, lentes_sol, lentes_contacto)",
  "genero": "hombre, mujer o unisex",
  "color": "Color principal del lente o montura",
  "material": "Material de la montura (ej: metal, acetato, TR90, titanio)",
  "forma_montura": "Forma de la montura (ej: rectangular, redondo, aviador, wayfarer, gato, ovalado, cuadrado)",
  "estilo": "Estilo (ej: clasico, moderno, retro, deportivo, elegante)",
  "uso_recomendado": "Uso recomendado (ej: uso_diario, deportes, lectura, computadora, moda)",
  "rostro_recomendado": "Tipo de rostro recomendado (ej: ovalado, redondo, cuadrado, corazon, diamante)",
  "precio": 0,
  "descripcion": "Descripcion comercial atractiva del producto"
}

Campos requeridos: nombre, categoria, genero, forma_montura, precio.
Si no puedes determinar un valor, usa valores razonables por defecto.
RESPONDE UNICAMENTE CON EL JSON."""

    def _parse_response(self, text: str) -> dict:
        text = text.strip()
        if text.startswith("```"):
            lines = text.splitlines()
            text = "\n".join(
                line for line in lines if not line.startswith("```")
            )
        start = text.find("{")
        end = text.rfind("}") + 1
        if start >= 0 and end > start:
            text = text[start:end]
        return json.loads(text)

    def _call_openai(self, image_path: Path) -> dict:
        self._rate_limit()
        from openai import OpenAI
        client = OpenAI(api_key=Config.OPENAI_API_KEY)
        image_data = self._resize_and_encode(image_path)
        response = client.chat.completions.create(
            model=Config.OPENAI_MODEL,
            messages=[
                {
                    "role": "user",
                    "content": [
                        {"type": "text", "text": self._build_prompt()},
                        {
                            "type": "image_url",
                            "image_url": {"url": f"data:image/jpeg;base64,{image_data}"},
                        },
                    ],
                }
            ],
            response_format={"type": "json_object"},
            temperature=0.2,
            max_tokens=1024,
        )
        return json.loads(response.choices[0].message.content)

    def _call_gemini(self, image_path: Path) -> dict:
        self._rate_limit()
        from google import genai
        client = genai.Client(api_key=Config.GEMINI_API_KEY)
        image_data = self._resize_and_encode(image_path)
        response = client.models.generate_content(
            model=Config.GEMINI_MODEL,
            contents=[
                self._build_prompt(),
                {"inline_data": {"mime_type": "image/jpeg", "data": image_data}},
            ],
        )
        return self._parse_response(response.text)

    def _call_groq(self, image_path: Path) -> dict:
        self._rate_limit()
        from groq import Groq
        client = Groq(api_key=Config.GROQ_API_KEY)
        image_data = self._resize_and_encode(image_path)
        response = client.chat.completions.create(
            model=Config.GROQ_MODEL,
            messages=[
                {
                    "role": "user",
                    "content": [
                        {"type": "text", "text": self._build_prompt()},
                        {
                            "type": "image_url",
                            "image_url": {
                                "url": f"data:image/jpeg;base64,{image_data}"
                            },
                        },
                    ],
                }
            ],
            temperature=0.2,
            max_tokens=1024,
        )
        return self._parse_response(response.choices[0].message.content)

    def _try_providers(self, image_path: Path) -> dict:
        ordered = sorted(
            self.providers.items(),
            key=lambda x: x[1]["priority"]
        )
        last_error = None
        for name, _ in ordered:
            try:
                self.model_last_used = name
                if name == "openai":
                    return self._call_openai(image_path)
                elif name == "gemini":
                    return self._call_gemini(image_path)
                elif name == "groq":
                    return self._call_groq(image_path)
            except Exception as e:
                last_error = e
                continue
        raise AIError(
            f"Todos los proveedores fallaron para {image_path.name}. "
            f"Ultimo error: {last_error}"
        )

    def generate(self, image_path: Path, dataset_name: str = "") -> Optional[Product]:
        if not image_path.exists():
            raise AIError(f"Imagen no encontrada: {image_path}")

        if self.ai_cache and self.ai_cache.has(image_path):
            data = self.ai_cache.get(image_path)
            if data:
                return self._dict_to_product(data, dataset_name)

        try:
            data = self._try_providers(image_path)
            product = self._dict_to_product(data, dataset_name)

            if self.ai_cache:
                self.ai_cache.set(
                    image_path, data,
                    model_used=self.model_last_used,
                    dataset_name=dataset_name
                )

            return product
        except Exception as e:
            raise AIError(f"Error generando datos para {image_path.name}: {e}")

    def _dict_to_product(self, data: dict, dataset_name: str = "") -> Product:
        return Product(
            nombre=data.get("nombre", ""),
            marca=data.get("marca", ""),
            categoria=data.get("categoria", ""),
            genero=data.get("genero", "unisex"),
            color=data.get("color", ""),
            material=data.get("material", ""),
            forma_montura=data.get("forma_montura", ""),
            estilo=data.get("estilo", ""),
            uso_recomendado=data.get("uso_recomendado", ""),
            rostro_recomendado=data.get("rostro_recomendado", ""),
            precio=float(data.get("precio", 0) or 0),
            descripcion=data.get("descripcion", ""),
            dataset_origen=dataset_name,
        )
