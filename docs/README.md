# 👓 Óptica Golden - eCommerce con Asesor Virtual IA

## Desarrollo de un eCommerce para la Óptica Golden con un Asesor Virtual para la Recomendación Personalizada de Lentes Ópticos Basado en Inteligencia Artificial, Análisis Facial y Preferencias del Usuario.

---

## 📋 Documentación Completa

| Archivo | Descripción |
|---------|-------------|
| `docs/01-REQUISITOS_FUNCIONALES.md` | 58 requisitos funcionales detallados |
| `docs/02-REQUISITOS_NO_FUNCIONALES.md` | 34 requisitos no funcionales |
| `docs/03-CASOS_DE_USO.md` | 10 casos de uso completos |
| `docs/04-HISTORIAS_DE_USUARIO.md` | 23 historias de usuario con criterios |
| `docs/diagrams/05-DER_DIAGRAMA_ENTIDAD_RELACION.md` | DER completo con 15 tablas |
| `docs/diagrams/06-MODELO_RELACIONAL.md` | SQL completo del modelo relacional |
| `docs/07-ARQUITECTURA_MVC_LARAVEL.md` | Estructura completa del proyecto Laravel |
| `docs/08-API_REST.md` | Documentación completa de la API REST |
| `docs/09-WIREFRAMES.md` | 8 wireframes de todas las pantallas |
| `docs/10-DISENIO_UI-UX.md` | Guía de diseño UI/UX (colores, tipografía, componentes) |
| `docs/11-PLAN_DE_PRUEBAS.md` | Plan de pruebas (unitarias, integración, carga, seguridad) |
| `docs/12-CRONOGRAMA.md` | Cronograma de 12 semanas |
| `docs/13-RIESGOS.md` | Matriz de 15 riesgos con mitigación |
| `docs/14-RESUMEN_EJECUTIVO.md` | Resumen ejecutivo del proyecto |
| `docs/15-INTEGRACION_IA_MEDIAPIPE.md` | Guía de integración Gemini/Groq + MediaPipe |

## 🗄️ Código Fuente

### Migraciones (database/migrations/)
- `0001_create_roles_table.php`
- `0002_create_usuarios_table.php`
- `0003_create_categorias_table.php`
- `0004_create_marcas_table.php`
- `0005_create_lentes_table.php`
- `0006_create_imagenes_lentes_table.php`
- `0007_create_pedidos_table.php`
- `0008_create_detalle_pedidos_table.php`
- `0009_create_pagos_table.php`
- `0010_create_carritos_table.php`
- `0011_create_analisis_faciales_table.php`
- `0012_create_recomendaciones_table.php`
- `0013_create_detalle_recomendaciones_table.php`
- `0014_create_chat_ia_table.php`

### Modelos Eloquent (app/Models/)
- `User.php`, `Role.php`, `Lente.php`, `ImagenLente.php`
- `Categoria.php`, `Marca.php`
- `Pedido.php`, `DetallePedido.php`, `Pago.php`
- `Carrito.php`
- `AnalisisFacial.php`, `Recomendacion.php`, `DetalleRecomendacion.php`
- `ChatIA.php`

### Servicios (app/Services/)
- `RecommendationEngine.php` - Motor de recomendación con scoring
- `CartService.php` - Lógica del carrito
- `OrderService.php` - Gestión de pedidos
- `PaymentService.php` - Procesamiento de pagos
- `ReportService.php` - Estadísticas y reportes
- `AI/GeminiService.php` - Integración Gemini API
- `AI/GroqService.php` - Integración Groq API
- `AI/AIProviderInterface.php` - Contrato de proveedor IA
- `FaceAnalysis/MediaPipeService.php` - Análisis facial
- `FaceAnalysis/FaceShapeClassifier.php` - Clasificador de forma

### Middleware (app/Http/Middleware/)
- `AdminMiddleware.php` - Protección rutas admin
- `ClienteMiddleware.php` - Protección rutas cliente
- `CheckLenteDisponible.php` - Validación disponibilidad

### API Controllers (app/Http/Controllers/Api/)
- `AuthController.php` - 8 endpoints de autenticación
- `CatalogoController.php` - 4 endpoints de catálogo
- `CarritoController.php` - 4 endpoints de carrito
- `AsesorController.php` - 3 endpoints de asesor IA
- `FaceAnalysisController.php` - 2 endpoints de análisis facial
- `PedidoController.php` - 2 endpoints de pedidos
- `PagoController.php` - 2 endpoints de pagos

### Rutas (routes/)
- `web.php` - 25 rutas web (frontend Blade)
- `admin.php` - 18 rutas admin (panel administrativo)
- `api.php` - 23 rutas API REST

### Seeders (database/seeders/)
- `RoleSeeder.php`, `AdminSeeder.php`
- `CategoriaSeeder.php`, `MarcaSeeder.php`, `LenteSeeder.php`

### Frontend JS (public/js/)
- `mediapipe-face.js` - Cliente MediaPipe con FaceAnalyzer class
- `asesor-virtual.js` - Asesor Virtual con chat y recomendaciones

### Config (config/)
- `ai.php` - Configuración de proveedores IA
- `mediapipe.php` - Configuración de análisis facial

---

## 🚀 Instalación

```bash
# 1. Crear proyecto Laravel
composer create-project laravel/laravel optica-golden "^12.0"

# 2. Copiar archivos del proyecto
# (copiar app/, database/, resources/, routes/, config/, public/js/)

# 3. Configurar .env
DB_DATABASE=optica_golden
GEMINI_API_KEY=tu_api_key
# o
AI_PROVIDER=groq
GROQ_API_KEY=tu_api_key

# 4. Migrar base de datos
php artisan migrate --seed

# 5. Servir aplicación
php artisan serve
```

## 🎯 Tecnologías

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| Laravel | 12 | Backend framework |
| PHP | 8.3 | Lenguaje de programación |
| MySQL | 8.0 | Base de datos relacional |
| Bootstrap | 5.3 | Frontend framework |
| JavaScript | ES6 | Interactividad frontend |
| Gemini API | 2.0 Flash | IA conversacional |
| Groq API | Llama 3.3 | IA alterna |
| MediaPipe | Face Mesh | Visión artificial |
| Laravel Sanctum | ^4 | Autenticación API |
| Chart.js | ^4 | Gráficos dashboard |
| DomPDF | ^10 | Comprobantes PDF |

---

**© 2026 Óptica Golden - Todos los derechos reservados**
