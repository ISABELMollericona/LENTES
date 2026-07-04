# ARQUITECTURA MVC - Óptica Golden eCommerce

## Estructura del Proyecto (Laravel 12)

```
LENTES UPDS/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerarRecomendaciones.php
│   │       └── LimpiarCarritosExpirados.php
│   ├── Enums/
│   │   ├── EstadoLente.php
│   │   ├── EstadoPedido.php
│   │   ├── EstadoPago.php
│   │   ├── FormaRostro.php
│   │   ├── Genero.php
│   │   ├── MetodoPago.php
│   │   ├── TipoLente.php
│   │   ├── TipoMontura.php
│   │   ├── UsoLente.php
│   │   └── EstiloLente.php
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── ResetPasswordController.php
│   │   │   │   └── ProfileController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LenteController.php
│   │   │   │   ├── PedidoController.php
│   │   │   │   ├── UsuarioController.php
│   │   │   │   └── ReporteController.php
│   │   │   ├── Cliente/
│   │   │   │   ├── CatalogoController.php
│   │   │   │   ├── CarritoController.php
│   │   │   │   ├── PedidoController.php
│   │   │   │   └── PagoController.php
│   │   │   ├── AsesorVirtual/
│   │   │   │   ├── ChatController.php
│   │   │   │   └── RecomendacionController.php
│   │   │   ├── Facial/
│   │   │   │   └── FaceAnalysisController.php
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── CatalogoController.php
│   │   │       ├── CarritoController.php
│   │   │       ├── AsesorController.php
│   │   │       ├── FaceAnalysisController.php
│   │   │       ├── PedidoController.php
│   │   │       └── PagoController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── ClienteMiddleware.php
│   │   │   └── CheckLenteDisponible.php
│   │   └── Requests/
│   │       ├── RegisterRequest.php
│   │       ├── LoginRequest.php
│   │       ├── ProfileRequest.php
│   │       ├── LenteRequest.php
│   │       ├── AsesorRequest.php
│   │       └── FaceAnalysisRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Lente.php
│   │   ├── ImagenLente.php
│   │   ├── Categoria.php
│   │   ├── Marca.php
│   │   ├── Pedido.php
│   │   ├── DetallePedido.php
│   │   ├── Pago.php
│   │   ├── Carrito.php
│   │   ├── AnalisisFacial.php
│   │   ├── Recomendacion.php
│   │   ├── DetalleRecomendacion.php
│   │   └── ChatIA.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Services/
│   │   ├── AI/
│   │   │   ├── GeminiService.php
│   │   │   ├── GroqService.php
│   │   │   └── AIProviderInterface.php
│   │   ├── FaceAnalysis/
│   │   │   ├── MediaPipeService.php
│   │   │   └── FaceShapeClassifier.php
│   │   ├── RecommendationEngine.php
│   │   ├── CartService.php
│   │   ├── OrderService.php
│   │   ├── PaymentService.php
│   │   └── ReportService.php
│   └── Traits/
│       └── ApiResponse.php
├── bootstrap/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── sanctum.php
│   ├── services.php
│   ├── ai.php (Gemini/Groq config)
│   └── mediapipe.php
├── database/
│   ├── migrations/
│   │   ├── 0001_create_roles_table.php
│   │   ├── 0002_create_usuarios_table.php
│   │   ├── 0003_create_personal_access_tokens_table.php
│   │   ├── 0004_create_categorias_table.php
│   │   ├── 0005_create_marcas_table.php
│   │   ├── 0006_create_lentes_table.php
│   │   ├── 0007_create_imagenes_lentes_table.php
│   │   ├── 0008_create_pedidos_table.php
│   │   ├── 0009_create_detalle_pedidos_table.php
│   │   ├── 0010_create_pagos_table.php
│   │   ├── 0011_create_carritos_table.php
│   │   ├── 0012_create_analisis_faciales_table.php
│   │   ├── 0013_create_recomendaciones_table.php
│   │   ├── 0014_create_detalle_recomendaciones_table.php
│   │   ├── 0015_create_chat_ia_table.php
│   │   └── 0016_create_password_reset_tokens_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       ├── AdminSeeder.php
│       ├── CategoriaSeeder.php
│       ├── MarcaSeeder.php
│       └── LenteSeeder.php
├── public/
│   ├── js/
│   │   ├── mediapipe-face.js
│   │   ├── asesor-virtual.js
│   │   ├── carrito.js
│   │   ├── catalogo-filtros.js
│   │   └── admin-charts.js
│   ├── css/
│   │   ├── app.css
│   │   └── admin.css
│   └── img/
│       └── lentes/
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php (layout principal)
│       │   └── admin.blade.php (layout admin)
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── forgot-password.blade.php
│       │   └── reset-password.blade.php
│       ├── lentes/
│       │   ├── index.blade.php (catálogo)
│       │   ├── show.blade.php (detalle)
│       │   └── partials/
│       │       ├── card.blade.php
│       │       └── filters.blade.php
│       ├── asesor/
│       │   ├── index.blade.php (chat virtual)
│       │   ├── resultados.blade.php
│       │   └── partials/
│       │       └── chat-messages.blade.php
│       ├── carrito/
│       │   └── index.blade.php
│       ├── pedidos/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── pagos/
│       │   ├── index.blade.php
│       │   └── comprobante.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── lentes/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── show.blade.php
│       │   ├── usuarios/
│       │   │   └── index.blade.php
│       │   ├── pedidos/
│       │   │   ├── index.blade.php
│       │   │   └── show.blade.php
│       │   └── reportes/
│       │       └── index.blade.php
│       └── partials/
│           ├── navbar.blade.php
│           ├── footer.blade.php
│           ├── alerts.blade.php
│           └── pagination.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── admin.php
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── CatalogoTest.php
│   │   ├── CarritoTest.php
│   │   ├── PedidoTest.php
│   │   ├── AsesorVirtualTest.php
│   │   └── FaceAnalysisTest.php
│   └── Unit/
│       ├── RecommendationEngineTest.php
│       ├── FaceShapeClassifierTest.php
│       └── CartServiceTest.php
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── lentes/
│   └── logs/
├── .env.example
├── composer.json
└── package.json
```

## Patrón MVC Aplicado

### Model (Capa de Datos)
- Eloquent Models con relaciones, scopes y mutators
- Enums para valores fijos
- Traits reutilizables

### View (Capa de Presentación)
- Blade con layout principal y admin
- Bootstrap 5 + componentes
- JavaScript para interactividad AJAX
- Responsive design

### Controller (Capa de Lógica)
- Separa lógica web (Blade) y API (JSON)
- Form Requests para validación
- Servicios para lógica de negocio pesada
- Middleware para autorización

### Servicios (Lógica de Negocio)
- RecommendationEngine: motor de recomendación
- GeminiService/GroqService: IA conversacional
- MediaPipeService: análisis facial
- CartService: lógica del carrito
- OrderService: gestión de pedidos
- PaymentService: procesamiento de pagos
