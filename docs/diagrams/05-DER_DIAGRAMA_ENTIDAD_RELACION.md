# DIAGRAMA ENTIDAD-RELACIÓN (DER) - Óptica Golden

## Representación Textual

```
┌─────────────────────┐       ┌─────────────────────────┐       ┌────────────────────┐
│      roles          │       │       usuarios           │       │     lentes         │
├─────────────────────┤       ├─────────────────────────┤       ├────────────────────┤
│ PK id (INT)         │◄──────│ FK rol_id (INT)         │       │ PK id (INT)        │
│ nombre (VARCHAR)    │       │ id (INT) PK             │       │ codigo (VARCHAR)   │
│ descripcion (TEXT)  │       │ nombre (VARCHAR)        │       │ nombre (VARCHAR)   │
│ created_at          │       │ apellido (VARCHAR)      │       │ descripcion (TEXT) │
│ updated_at          │       │ email (VARCHAR) UNIQUE  │       │ FK categoria_id    │
└─────────────────────┘       │ telefono (VARCHAR)      │       │ genero (ENUM)      │
                              │ direccion (TEXT)        │       │ tipo_lente (ENUM)  │
┌─────────────────────┐       │ password (VARCHAR)      │       │ tipo_montura (ENUM)│
│   categorias        │       │ foto (VARCHAR)          │       │ material (VARCHAR) │
├─────────────────────┤       │ email_verified_at (DATETIME)│   │ color (VARCHAR)    │
│ PK id (INT)         │       │ estado (ENUM)           │       │ FK marca_id        │
│ nombre (VARCHAR)    │       │ ultimo_acceso (DATETIME)│       │ precio (DECIMAL)   │
│ slug (VARCHAR)      │       │ created_at              │       │ estado (ENUM)      │
│ descripcion (TEXT)  │       │ updated_at              │       │ imagen_principal   │
│ created_at          │       └─────────────────────────┘       │ created_at          │
│ updated_at          │                                         │ updated_at          │
└─────────────────────┘                                         └────────────────────┘
        │                                                              │
        │                                                              │
        │       ┌─────────────────────────┐        ┌──────────────────┐│
        │       │  imagenes_lentes         │        │  marcas          ││
        │       ├─────────────────────────┤        ├──────────────────┤│
        │       │ PK id (INT)             │        │ PK id (INT)      ││
        │       │ FK lente_id (INT)       │◄───────│ nombre (VARCHAR) ││
        │       │ url (VARCHAR)           │        │ slug (VARCHAR)   ││
        │       │ orden (INT)             │        │ descripcion (TEXT)││
        │       │ created_at              │        │ created_at        ││
        │       │ updated_at              │        │ updated_at        ││
        │       └─────────────────────────┘        └──────────────────┘│
        │                                                              │
┌───────┴──────────────┐            ┌───────────────────────────────┐  │
│    pedidos           │            │  detalle_pedidos              │  │
├──────────────────────┤            ├───────────────────────────────┤  │
│ PK id (INT)          │            │ PK id (INT)                   │  │
│ FK usuario_id (INT)  │◄───────────│ FK pedido_id (INT)            │  │
│ codigo (VARCHAR)     │            │ FK lente_id (INT)             │◄─┘
│ fecha_pedido (DATE)  │            │ precio_unitario (DECIMAL)     │
│ total (DECIMAL)      │            │ created_at                    │
│ estado (ENUM)        │            │ updated_at                    │
│ observaciones (TEXT) │            └───────────────────────────────┘
│ created_at           │
│ updated_at           │
└──────────────────────┘
        │
        │
┌───────┴───────────────────┐      ┌───────────────────────────────┐
│     pagos                 │      │  analisis_faciales            │
├───────────────────────────┤      ├───────────────────────────────┤
│ PK id (INT)               │      │ PK id (INT)                   │
│ FK pedido_id (INT)        │      │ FK usuario_id (INT)           │
│ metodo_pago (ENUM)        │      │ imagen_url (VARCHAR)          │
│ fecha_pago (DATETIME)     │      │ forma_rostro (ENUM)           │
│ monto (DECIMAL)           │      │ puntos_referencia (JSON)      │
│ estado (ENUM)             │      │ confianza (DECIMAL)           │
│ comprobante_url (VARCHAR) │      │ created_at                    │
│ created_at                │      │ updated_at                    │
│ updated_at                │      └───────────────────────────────┘
└───────────────────────────┘
        │
┌───────┴───────────────────┐      ┌───────────────────────────────┐
│   recomendaciones         │      │  chat_ia                      │
├───────────────────────────┤      ├───────────────────────────────┤
│ PK id (INT)               │      │ PK id (INT)                   │
│ FK usuario_id (INT)       │      │ FK usuario_id (INT)           │
│ FK analisis_facial_id (INT)NULL│  │ mensaje (TEXT)                │
│ forma_rostro (ENUM)       │      │ respuesta (TEXT)              │
│ presupuesto_max (DECIMAL) │      │ tipo (ENUM: usuario/sistema)  │
│ uso_lentes (ENUM)         │      │ created_at                    │
│ estilo (ENUM)             │      │ updated_at                    │
│ color_favorito (VARCHAR)  │      └───────────────────────────────┘
│ tipo_montura (ENUM)       │
│ created_at                │
│ updated_at                │
└───────────────────────────┘
        │
┌───────┴───────────────────┐
│ detalle_recomendaciones   │
├───────────────────────────┤
│ PK id (INT)               │
│ FK recomendacion_id (INT) │
│ FK lente_id (INT)         │
│ compatibilidad (DECIMAL)  │
│ justificacion (TEXT)      │
│ orden (INT)               │
│ created_at                │
│ updated_at                │
└───────────────────────────┘
```

## Relaciones

1. **roles (1) ── (N) usuarios**: Un rol tiene muchos usuarios
2. **usuarios (1) ── (N) pedidos**: Un usuario tiene muchos pedidos
3. **usuarios (1) ── (N) analisis_faciales**: Un usuario tiene muchos análisis
4. **usuarios (1) ── (N) recomendaciones**: Un usuario tiene muchas recomendaciones
5. **usuarios (1) ── (N) chat_ia**: Un usuario tiene muchos mensajes de chat
6. **categorias (1) ── (N) lentes**: Una categoría tiene muchos lentes
7. **marcas (1) ── (N) lentes**: Una marca tiene muchos lentes
8. **lentes (1) ── (N) imagenes_lentes**: Un lente tiene muchas imágenes
9. **lentes (1) ── (N) detalle_pedidos**: Un lente está en muchos detalles (pero solo UNO como vendido activo)
10. **pedidos (1) ── (N) detalle_pedidos**: Un pedido tiene muchos detalles
11. **pedidos (1) ── (1) pagos**: Un pedido tiene un pago
12. **recomendaciones (1) ── (N) detalle_recomendaciones**: Una recomendación tiene muchos detalles
13. **lentes (1) ── (N) detalle_recomendaciones**: Un lente aparece en muchos detalles
14. **analisis_faciales (1) ── (N) recomendaciones**: Un análisis puede generar muchas recomendaciones
