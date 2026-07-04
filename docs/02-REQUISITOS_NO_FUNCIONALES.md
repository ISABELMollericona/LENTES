# REQUISITOS NO FUNCIONALES - Óptica Golden eCommerce

## Rendimiento

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-001 | Tiempo de respuesta | El tiempo de respuesta para cualquier operación CRUD será menor a 3 segundos. |
| RNF-002 | Recomendaciones IA | El tiempo de generación de recomendaciones IA será menor a 5 segundos. |
| RNF-003 | Análisis facial | El tiempo total de análisis facial será menor a 10 segundos. |
| RNF-004 | Carga de imágenes | Las imágenes del catálogo se cargarán con lazy loading y formatos optimizados (WebP). |
| RNF-005 | Concurrencia | El sistema soportará mínimo 300 usuarios concurrentes sin degradación. |

## Seguridad

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-006 | HTTPS | Toda la comunicación será cifrada mediante HTTPS/TLS 1.3. |
| RNF-007 | Contraseñas | Las contraseñas se almacenarán usando bcrypt (Hash::make de Laravel). |
| RNF-008 | Autenticación | Se usará Laravel Sanctum con tokens de acceso para API. |
| RNF-009 | Protección CSRF | Todas las rutas web estarán protegidas contra CSRF. |
| RNF-010 | Validación de entrada | Todos los inputs serán validados del lado del servidor. |
| RNF-011 | SQL Injection | Se usará Eloquent ORM para prevenir inyección SQL. |
| RNF-012 | XSS Protection | Blade escapará automáticamente las salidas con {{ }}. |
| RNF-013 | Rate Limiting | Las rutas API tendrán límite de 60 peticiones por minuto. |
| RNF-014 | CORS | Configuración CORS restrictiva para dominios permitidos. |

## Disponibilidad

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-015 | Uptime | Disponibilidad del sistema mayor al 99% mensual. |
| RNF-016 | Mantenimiento | Ventana de mantenimiento programada (domingo 2:00-4:00 AM). |
| RNF-017 | Backup | Respaldo automático diario de base de datos. |
| RNF-018 | Recovery | Tiempo de recuperación ante fallos menor a 2 horas. |

## Compatibilidad

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-019 | Navegadores | Compatible con Chrome 90+, Firefox 88+, Edge 90+. |
| RNF-020 | Responsive | Diseño adaptable a móviles, tablets y desktop. |
| RNF-021 | Resoluciones | Soporte desde 320px hasta 2560px de ancho. |

## Escalabilidad

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-022 | Base de datos | MySQL 8.0+ optimizado con índices en columnas de búsqueda frecuente. |
| RNF-023 | Caché | Uso de Redis o cache en archivo para consultas frecuentes. |
| RNF-024 | Colas | Uso de Laravel Queues para procesos pesados (IA, análisis facial). |

## Usabilidad

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-025 | Accesibilidad | Cumplimiento WCAG 2.1 nivel AA. |
| RNF-026 | Feedback visual | Cada acción del usuario tendrá retroalimentación visual inmediata. |
| RNF-027 | Idioma | Interfaz completamente en español. |
| RNF-028 | Tiempo de carga inicial | Página principal cargará en menos de 2 segundos. |

## Base de Datos

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-029 | Motor | MySQL 8.0+ con InnoDB. |
| RNF-030 | Juego de caracteres | utf8mb4_unicode_ci. |
| RNF-031 | Índices | Índices en: email, estado, categoria_id, marca_id, precio. |
| RNF-032 | Integridad referencial | Todas las claves foráneas con ON DELETE CASCADE o RESTRICT según corresponda. |

## Logging y Monitoreo

| ID  | RNF | Descripción |
|-----|-----|-------------|
| RNF-033 | Logs | Laravel Log con niveles: error, warning, info, debug. |
| RNF-034 | Auditoría | Registro de acciones críticas: compras, cambios de estado, pagos. |
