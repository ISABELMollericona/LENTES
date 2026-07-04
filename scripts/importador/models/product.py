from dataclasses import dataclass, field, asdict
from typing import Optional
from datetime import datetime


@dataclass
class Product:
    codigo: str = ""
    nombre: str = ""
    marca: str = ""
    categoria: str = ""
    genero: str = "unisex"
    color: str = ""
    material: str = ""
    forma_montura: str = ""
    estilo: str = ""
    uso_recomendado: str = ""
    rostro_recomendado: str = ""
    precio: float = 0.0
    descripcion: str = ""
    imagen: str = ""
    estado: str = "disponible"
    dataset_origen: str = ""
    fecha_registro: Optional[str] = None

    @property
    def required_fields(self):
        return ["codigo", "nombre", "categoria", "imagen", "estado"]

    def is_valid(self) -> bool:
        for field_name in self.required_fields:
            if not getattr(self, field_name, None):
                return False
        return True

    def missing_fields(self) -> list:
        return [f for f in self.required_fields if not getattr(self, f, None)]

    def to_db_dict(self):
        data = asdict(self)
        if self.fecha_registro is None:
            data["fecha_registro"] = datetime.now().isoformat()
        return data
