# REQUISITOS FUNCIONALES - Óptica Golden eCommerce

## Módulo 1: Autenticación y Usuarios

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-001 | Registro de usuarios | El sistema permitirá el registro de nuevos usuarios mediante formulario con nombre, apellido, correo, teléfono, dirección y contraseña. |
| RF-002 | Inicio de sesión | El sistema autenticará usuarios mediante correo y contraseña utilizando Laravel Sanctum. |
| RF-003 | Recuperación de contraseña | El sistema enviará un enlace de restablecimiento de contraseña al correo registrado. |
| RF-004 | Edición de perfil | El usuario autenticado podrá modificar sus datos personales excepto el correo electrónico. |
| RF-005 | Cambio de contraseña | El usuario autenticado podrá cambiar su contraseña ingresando la actual y la nueva. |
| RF-006 | Cierre de sesión | El sistema invalidará la sesión del usuario eliminando los tokens de acceso. |
| RF-007 | Roles de usuario | El sistema manejará dos roles: Administrador y Cliente. |
| RF-008 | Gestión de usuarios (Admin) | El administrador podrá listar, editar, suspender y eliminar usuarios. |

## Módulo 2: Catálogo de Lentes

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-009 | Visualizar catálogo | El sistema mostrará todos los lentes disponibles con paginación. |
| RF-010 | Buscar productos | El usuario podrá buscar lentes por nombre, código o descripción. |
| RF-011 | Filtrar productos | El usuario podrá filtrar por género, color, tipo de montura, marca, precio y categoría. |
| RF-012 | Ver detalles | El sistema mostrará la ficha completa del producto con imágenes, descripción y disponibilidad. |
| RF-013 | Gestión de lentes (Admin) | El administrador podrá crear, editar, eliminar y cambiar estado de lentes. |
| RF-014 | Imágenes múltiples | Cada lente podrá tener una imagen principal y varias imágenes secundarias. |
| RF-015 | Estado visual | El sistema mostrará claramente "Disponible" (verde) o "Vendido" (rojo) en cada producto. |
| RF-016 | Lentes vendidos visibles | Los usuarios podrán ver lentes vendidos pero no podrán comprarlos. |

## Módulo 3: Asesor Virtual con IA

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-017 | Iniciar asesoría | El usuario podrá iniciar una conversación con el asesor virtual IA. |
| RF-018 | Preguntas del asesor | El asesor realizará preguntas sobre uso, presupuesto, estilo, colores y tipo de montura. |
| RF-019 | Comprensión de respuestas | La IA procesará el lenguaje natural de las respuestas del usuario. |
| RF-020 | Recomendación de lentes | El asesor recomendará lentes disponibles del catálogo con porcentaje de compatibilidad. |
| RF-021 | Justificación de recomendaciones | Cada recomendación incluirá una explicación personalizada generada por IA. |
| RF-022 | Análisis facial opcional | El asesor podrá solicitar una fotografía para análisis facial. |
| RF-023 | Historial de asesorías | El sistema almacenará las conversaciones y recomendaciones generadas. |
| RF-024 | Consultar recomendaciones (Admin) | El administrador podrá ver todas las recomendaciones generadas. |

## Módulo 4: Análisis Facial

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-025 | Captura de fotografía | El usuario podrá subir una foto o capturarla con la cámara. |
| RF-026 | Detección facial | El sistema detectará el rostro en la imagen usando MediaPipe. |
| RF-027 | Clasificación de forma | El sistema clasificará la forma del rostro (ovalado, redondo, cuadrado, rectangular, corazón, diamante). |
| RF-028 | Malla facial | El sistema generará 468 puntos de referencia facial con MediaPipe Face Mesh. |
| RF-029 | Envío al motor de recomendación | La forma facial detectada se integrará automáticamente al motor de recomendación. |
| RF-030 | Historial de análisis (Admin) | El administrador podrá consultar todos los análisis faciales realizados. |

## Módulo 5: Motor de Recomendación

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-031 | Recomendación multicriterio | El motor considerará forma facial, presupuesto, uso, estilo, color favorito y tipo de montura. |
| RF-032 | Reglas forma-montura | El sistema aplicará reglas predefinidas (rostro redondo -> monturas rectangulares, etc.). |
| RF-033 | Filtrado por disponibilidad | Solo se recomendarán lentes con estado "Disponible". |
| RF-034 | Porcentaje de compatibilidad | Cada recomendación mostrará un porcentaje calculado según coincidencia de criterios. |
| RF-035 | Ranking de recomendaciones | Las recomendaciones se ordenarán de mayor a menor compatibilidad. |
| RF-036 | Límite de recomendaciones | El sistema mostrará máximo 10 recomendaciones por consulta. |

## Módulo 6: Carrito de Compra

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-037 | Agregar al carrito | El usuario autenticado podrá agregar un lente disponible a su carrito. |
| RF-038 | Unicidad en carrito | No se podrá agregar un lente que ya esté en el carrito de otro usuario. |
| RF-039 | Eliminar del carrito | El usuario podrá eliminar lentes de su carrito. |
| RF-040 | Visualizar carrito | El usuario verá todos los lentes en su carrito con precios y total. |
| RF-041 | Confirmar compra | El usuario podrá confirmar la compra de todos los lentes en el carrito. |
| RF-042 | Validación de disponibilidad | Antes de confirmar, el sistema validará que todos los lentes aún están disponibles. |

## Módulo 7: Pedidos

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-043 | Generar pedido | Al confirmar la compra, se generará un pedido con estado "Pendiente". |
| RF-044 | Consultar pedidos (Cliente) | El usuario verá sus pedidos con estado y detalles. |
| RF-045 | Historial de pedidos | El usuario podrá consultar el historial completo de sus pedidos. |
| RF-046 | Gestión de pedidos (Admin) | El administrador podrá cambiar el estado de los pedidos. |
| RF-047 | Estados de pedido | Pendiente, Confirmado, En preparación, Entregado, Cancelado. |
| RF-048 | Notificación de cambio de estado | El sistema notificará al usuario cuando cambie el estado de su pedido. |

## Módulo 8: Pagos

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-049 | Registrar pago | El sistema registrará el pago asociado a un pedido. |
| RF-050 | Métodos de pago | El sistema aceptará: Tarjeta de crédito/débito, Transferencia bancaria, Pago en efectivo (tienda). |
| RF-051 | Confirmar pago | El administrador podrá confirmar pagos realizados. |
| RF-052 | Generar comprobante | El sistema generará un comprobante de pago en PDF. |
| RF-053 | Estados de pago | Pendiente, Aprobado, Rechazado, Reembolsado. |

## Módulo 9: Panel Administrativo

| ID  | RF | Descripción |
|-----|-----|-------------|
| RF-054 | Dashboard | El panel mostrará: usuarios registrados, lentes disponibles, lentes vendidos, ventas totales, pedidos realizados, ingresos, recomendaciones realizadas, análisis faciales. |
| RF-055 | Reportes de ventas | El administrador podrá generar reportes de ventas por fecha y por categoría. |
| RF-056 | Lentes más vendidos | Reporte de lentes más populares. |
| RF-057 | Usuarios top | Reporte de usuarios con más compras. |
| RF-058 | Exportar reportes | Los reportes podrán exportarse en PDF y Excel. |
