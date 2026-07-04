# CASOS DE USO - Óptica Golden eCommerce

## Diagrama de Actores

```
┌─────────────────────────────────────────────────────────┐
│                    SISTEMA ÓPTICA GOLDEN                  │
│                                                          │
│  ┌─────────────────────┐   ┌─────────────────────────┐  │
│  │                     │   │                         │  │
│  │    ADMINISTRADOR    │   │        CLIENTE          │  │
│  │                     │   │                         │  │
│  │  - Gestiona lentes  │   │  - Compra lentes        │  │
│  │  - Gestiona pedidos │   │  - Usa asesor virtual   │  │
│  │  - Ve reportes      │   │  - Consulta catálogo    │  │
│  │  - Gestiona usuarios│   │  - Historial de pedidos │  │
│  └─────────────────────┘   └─────────────────────────┘  │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

## CU-001: Registro de Usuario

| Campo | Detalle |
|-------|---------|
| Actor | Cliente |
| Disparador | El usuario desea registrarse en la plataforma |
| Precondición | No tener cuenta activa |
| Flujo Principal | 1. Usuario ingresa a /register<br>2. Completa formulario (nombre, apellido, email, teléfono, dirección, contraseña)<br>3. Sistema valida datos<br>4. Sistema crea usuario con rol Cliente<br>5. Sistema envía correo de bienvenida<br>6. Sistema redirige al login |
| Postcondición | Usuario registrado exitosamente |
| Flujo Alternativo | 3a. Email ya registrado -> mostrar error |
| Flujo Alternativo | 3b. Contraseña débil -> mostrar requisitos |

## CU-002: Inicio de Sesión

| Campo | Detalle |
|-------|---------|
| Actor | Cliente, Administrador |
| Disparador | Usuario desea autenticarse |
| Precondición | Usuario registrado |
| Flujo Principal | 1. Usuario ingresa a /login<br>2. Ingresa email y contraseña<br>3. Sistema autentica credenciales<br>4. Sistema genera token Sanctum<br>5. Sistema redirige según rol |
| Postcondición | Sesión iniciada |
| Flujo Alternativo | 3a. Credenciales inválidas -> mostrar error |

## CU-003: Gestionar Lentes (Admin)

| Campo | Detalle |
|-------|---------|
| Actor | Administrador |
| Disparador | Admin desea gestionar el catálogo |
| Precondición | Admin autenticado |
| Flujo Principal | 1. Admin accede a /admin/lentes<br>2. Visualiza lista de lentes<br>3. Puede crear, editar, eliminar o cambiar estado<br>4. Sistema persiste cambios |
| Postcondición | Catálogo actualizado |
| Flujo Alternativo | 3a. Crear: formulario con todos los campos + imágenes |
| Flujo Alternativo | 3b. Editar: formulario precargado |
| Flujo Alternativo | 3c. Eliminar: confirmación antes de borrado lógico |

## CU-004: Buscar y Filtrar Lentes

| Campo | Detalle |
|-------|---------|
| Actor | Cliente, Administrador |
| Disparador | Usuario desea encontrar un lente específico |
| Precondición | Catálogo con productos |
| Flujo Principal | 1. Usuario escribe en buscador o selecciona filtros<br>2. Sistema consulta BD con filtros<br>3. Sistema muestra resultados con paginación<br>4. Usuario puede ver detalles |
| Postcondición | Resultados mostrados |

## CU-005: Usar Asesor Virtual IA

| Campo | Detalle |
|-------|---------|
| Actor | Cliente |
| Disparador | Usuario desea recomendaciones personalizadas |
| Precondición | Usuario autenticado |
| Flujo Principal | 1. Usuario accede a /asesor-virtual<br>2. Sistema inicia chat con IA<br>3. IA pregunta: uso del lente<br>4. Usuario responde<br>5. IA pregunta: presupuesto<br>6. IA pregunta: estilo preferido<br>7. IA pregunta: colores favoritos<br>8. IA pregunta: tipo de montura<br>9. IA ofrece análisis facial (opcional)<br>10. IA genera recomendaciones desde el catálogo<br>11. IA muestra resultados con % de compatibilidad<br>12. Usuario puede agregar al carrito desde recomendaciones |
| Postcondición | Recomendaciones generadas y almacenadas |

## CU-006: Análisis Facial

| Campo | Detalle |
|-------|---------|
| Actor | Cliente |
| Disparador | Usuario acepta análisis facial durante asesoría |
| Precondición | Usuario autenticado, cámara disponible |
| Flujo Principal | 1. Sistema solicita foto o captura de cámara<br>2. Usuario proporciona imagen<br>3. Sistema procesa con MediaPipe Face Detection<br>4. Sistema genera malla facial (468 puntos)<br>5. Sistema clasifica forma de rostro<br>6. Sistema envía resultado al motor de recomendación<br>7. Sistema muestra forma detectada al usuario |
| Postcondición | Forma facial almacenada y utilizada en recomendación |

## CU-007: Realizar Compra

| Campo | Detalle |
|-------|---------|
| Actor | Cliente |
| Disparador | Usuario desea comprar lentes del carrito |
| Precondición | Usuario autenticado, carrito con items disponibles |
| Flujo Principal | 1. Usuario va a /carrito<br>2. Revisa items y total<br>3. Confirma compra<br>4. Sistema valida disponibilidad<br>5. Sistema crea pedido (Pendiente)<br>6. Sistema actualiza estado de lentes a "Vendido"<br>7. Sistema vacía carrito<br>8. Sistema redirige a pago |
| Postcondición | Pedido creado, lentes marcados como vendidos |

## CU-008: Gestionar Pedidos (Admin)

| Campo | Detalle |
|-------|---------|
| Actor | Administrador |
| Disparador | Admin desea gestionar pedidos |
| Precondición | Admin autenticado |
| Flujo Principal | 1. Admin accede a /admin/pedidos<br>2. Visualiza lista de pedidos<br>3. Puede cambiar estado (Confirmado, En preparación, Entregado, Cancelado)<br>4. Sistema notifica al cliente |
| Postcondición | Estado del pedido actualizado |

## CU-009: Consultar Dashboard

| Campo | Detalle |
|-------|---------|
| Actor | Administrador |
| Disparador | Admin desea ver estadísticas |
| Precondición | Admin autenticado |
| Flujo Principal | 1. Admin accede a /admin/dashboard<br>2. Sistema calcula: usuarios, lentes disponibles/vendidos, ventas, ingresos, recomendaciones, análisis faciales<br>3. Sistema muestra gráficos y cifras |
| Postcondición | Dashboard renderizado |

## CU-010: Ver Detalle de Producto

| Campo | Detalle |
|-------|---------|
| Actor | Cliente, Administrador |
| Disparador | Usuario hace clic en un lente |
| Precondición | Producto existe en BD |
| Flujo Principal | 1. Usuario hace clic en /lentes/{id}<br>2. Sistema carga datos del lente<br>3. Sistema muestra: imágenes, descripción, precio, estado, categoría, marca<br>4. Si está disponible: botón Agregar al carrito<br>5. Si está vendido: indicador visual + botón deshabilitado |
| Postcondición | Detalle mostrado |
