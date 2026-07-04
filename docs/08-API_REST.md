# API REST Completa - Óptica Golden

## Base URL
```
Producción: https://opticagolden.com/api/v1
Desarrollo: http://localhost:8000/api
```

## Autenticación
Todas las rutas protegidas usan **Bearer Token** (Sanctum):
```
Authorization: Bearer {token}
```

---

## 1. Autenticación

### POST /api/auth/register
Registro de nuevo usuario.
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan@email.com",
  "telefono": "12345678",
  "direccion": "Calle 123",
  "password": "password123",
  "password_confirmation": "password123"
}
```
**Response 201:**
```json
{
  "success": true,
  "data": { "user": {}, "token": "1|abc123..." },
  "message": "Registro exitoso"
}
```

### POST /api/auth/login
```json
{ "email": "juan@email.com", "password": "password123" }
```
**Response 200:** `{ "success": true, "data": { "user": {}, "token": "1|abc123...", "role": "cliente" } }`

### POST /api/auth/logout
*Requiere auth*

### GET /api/auth/me
*Requiere auth* - Devuelve el usuario autenticado.

### PUT /api/auth/profile
*Requiere auth* - Actualiza nombre, apellido, teléfono, dirección.

### PUT /api/auth/password
*Requiere auth*
```json
{ "current_password": "...", "new_password": "...", "new_password_confirmation": "..." }
```

### POST /api/auth/forgot-password
```json
{ "email": "juan@email.com" }
```

### POST /api/auth/reset-password
```json
{ "token": "...", "email": "...", "password": "...", "password_confirmation": "..." }
```

---

## 2. Catálogo

### GET /api/catalogo
Parámetros query:
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| search | string | Búsqueda por nombre/código |
| genero | string | hombre, mujer, unisex |
| tipo_montura | string | completa, semi_al_aire, al_aire |
| marca_id | int | ID de marca |
| categoria_id | int | ID de categoría |
| color | string | Color del lente |
| precio_min | decimal | Precio mínimo |
| precio_max | decimal | Precio máximo |
| per_page | int | Items por página (default: 12) |

### GET /api/catalogo/{id}
Detalle completo del lente con imágenes, categoría y marca.

### GET /api/categorias
Lista de categorías con conteo de lentes.

### GET /api/marcas
Lista de marcas con conteo de lentes.

---

## 3. Carrito

*Todas requieren auth*

### GET /api/carrito
Items del carrito con total y cantidad.

### POST /api/carrito/agregar/{lente}
Agrega un lente al carrito.

### DELETE /api/carrito/{id}
Elimina un item del carrito.

### POST /api/carrito/confirmar
Confirma la compra, crea el pedido y marca lentes como vendidos.

---

## 4. Pedidos

*Todas requieren auth*

### GET /api/pedidos
Historial de pedidos del usuario autenticado.

### GET /api/pedidos/{id}
Detalle del pedido con lentes y pago.

---

## 5. Pagos

*Todas requieren auth*

### POST /api/pagos/{pedido}
```json
{ "metodo_pago": "tarjeta_credito|tarjeta_debito|transferencia|efectivo" }
```

### GET /api/pagos/comprobante/{pago}
Descarga el comprobante en PDF.

---

## 6. Asesor Virtual

*Todas requieren auth*

### POST /api/asesor/chat
```json
{
  "mensaje": "Necesito lentes para leer",
  "sesion_id": "uuid" // opcional, se genera automáticamente
}
```

### POST /api/asesor/recomendar
```json
{
  "uso_lentes": "lectura",
  "presupuesto_max": 500.00,
  "estilo": "clasico",
  "color_favorito": "negro",
  "tipo_montura": "completa",
  "forma_rostro": "ovalado",
  "analisis_facial_id": 1
}
```

### GET /api/asesor/resultados/{recomendacion}
Resultados de una recomendación.

---

## 7. Análisis Facial

*Todas requieren auth*

### POST /api/analisis-facial
`multipart/form-data`:
| Campo | Tipo | Descripción |
|-------|------|-------------|
| imagen | file | JPG/PNG, máx 5MB |

### GET /api/analisis-facial/{id}
Resultado del análisis facial.

---

## 8. Rutas Admin (Web)

- `GET/POST /admin/login`
- `GET /admin/dashboard` - KPIs y gráficos
- `CRUD /admin/lentes` - Gestión de lentes
- `GET/PATCH /admin/pedidos` - Gestión de pedidos
- `GET /admin/usuarios` - Lista de usuarios
- `GET /admin/reportes/*` - Reportes y exportación

## Códigos de Error

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 201 | Creado |
| 400 | Bad Request |
| 401 | No autenticado |
| 403 | No autorizado |
| 404 | No encontrado |
| 409 | Conflicto (lente no disponible, etc.) |
| 422 | Validación fallida |
| 429 | Rate limit excedido |
| 500 | Error del servidor |
