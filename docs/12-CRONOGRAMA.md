# CRONOGRAMA DE DESARROLLO - Óptica Golden eCommerce

## Duración Total Estimada: 12 semanas (3 meses)

## Fase 1: Planificación y Diseño (Semanas 1-2)

| Semana | Actividad | Entregable |
|--------|-----------|------------|
| 1 | Análisis de requisitos | Documento de requisitos funcionales y no funcionales |
| 1 | Definición de casos de uso | Diagramas de casos de uso |
| 1 | Diseño de base de datos | DER y modelo relacional |
| 2 | Diseño de arquitectura | Documento de arquitectura MVC |
| 2 | Diseño UI/UX | Wireframes y prototipos Figma |
| 2 | Setup del proyecto Laravel | Repositorio Git, entorno de desarrollo |

## Fase 2: Core del Sistema (Semanas 3-5)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 3 | Migraciones y modelos | Base de datos + Eloquent |
| 3 | Sistema de autenticación | Registro, login, recuperación de contraseña |
| 3 | Roles y permisos | Admin y Cliente middleware |
| 4 | CRUD de categorías y marcas | Catálogo (parte 1) |
| 4 | CRUD de lentes con imágenes | Catálogo (parte 2) |
| 4 | Catálogo público con búsqueda | Catálogo (parte 3) |
| 5 | Filtros (género, precio, marca, etc.) | Catálogo (parte 4) |
| 5 | Detalle de producto | Catálogo (parte 5) |

## Fase 3: Carrito y Pedidos (Semanas 5-6)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 5 | Servicio de carrito | Carrito (parte 1) |
| 6 | Carrito frontend (AJAX) | Carrito (parte 2) |
| 6 | Servicio de pedidos | Pedidos (parte 1) |
| 6 | Frontend de pedidos + historial | Pedidos (parte 2) |

## Fase 4: Pagos (Semana 7)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 7 | Servicio de pagos | Pagos (parte 1) |
| 7 | Integración métodos de pago | Pagos (parte 2) |
| 7 | Generación de comprobantes PDF | Pagos (parte 3) |

## Fase 5: IA y Análisis Facial (Semanas 8-10)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 8 | Integración Gemini/Groq API | IA Conversacional |
| 8 | Servicio de chat IA | Asesor Virtual (parte 1) |
| 8 | Frontend de chat asesor | Asesor Virtual (parte 2) |
| 9 | Motor de recomendación | Recomendación (parte 1) |
| 9 | Reglas forma-montura | Recomendación (parte 2) |
| 9 | Frontend de recomendaciones | Recomendación (parte 3) |
| 10 | Integración MediaPipe Face Detection | Análisis Facial (parte 1) |
| 10 | MediaPipe Face Mesh | Análisis Facial (parte 2) |
| 10 | Clasificador de forma de rostro | Análisis Facial (parte 3) |

## Fase 6: Panel Admin (Semanas 9-10)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 9 | Dashboard con KPIs | Admin (parte 1) |
| 9 | Gestión de lentes (admin) | Admin (parte 2) |
| 10 | Gestión de pedidos (admin) | Admin (parte 3) |
| 10 | Gestión de usuarios (admin) | Admin (parte 4) |
| 10 | Reportes y exportación | Admin (parte 5) |

## Fase 7: API REST (Semana 11)

| Semana | Actividad | Módulo |
|--------|-----------|--------|
| 11 | API de autenticación | API (parte 1) |
| 11 | API de catálogo | API (parte 2) |
| 11 | API de carrito y pedidos | API (parte 3) |
| 11 | API de asesor y análisis facial | API (parte 4) |
| 11 | Rate limiting y CORS | API (parte 5) |

## Fase 8: Pruebas y Despliegue (Semanas 11-12)

| Semana | Actividad | Entregable |
|--------|-----------|------------|
| 11 | Pruebas unitarias (PHPUnit) | Reporte de pruebas |
| 11 | Pruebas de integración | Reporte de pruebas |
| 12 | Pruebas E2E (Dusk) | Reporte de pruebas |
| 12 | Pruebas de carga (K6) | Reporte de rendimiento |
| 12 | Pruebas de seguridad | Reporte de seguridad |
| 12 | Despliegue en producción | Sitio en vivo |
| 12 | Documentación final | Documentación completa |

## Diagrama de Gantt (Resumen)

```
Semana:   1   2   3   4   5   6   7   8   9  10  11  12
Planif    ██  ██
BD        ██  ██
Auth          ██  ██
Catálogo          ██  ██  ██
Carrito               ██  ██
Pedidos                   ██  ██
Pagos                        ██  ██
IA Chat                          ██  ██  ██
Recomendación                        ██  ██  ██
Análisis Facial                          ██  ██  ██
Admin                                           ██  ██
API                                                     ██  ██
Pruebas                                                     ██  ██
Despliegue                                                      ██
```

## Hitos Clave

| Hito | Fecha | Descripción |
|------|-------|-------------|
| H1 | Semana 2 | Diseño aprobado |
| H2 | Semana 5 | Catálogo funcional |
| H3 | Semana 7 | Carrito + Pedidos + Pagos |
| H4 | Semana 10 | IA + Análisis Facial completo |
| H5 | Semana 11 | API REST completa |
| H6 | Semana 12 | Sistema desplegado en producción |

## Recursos

| Rol | Dedicación |
|-----|------------|
| Project Manager | Tiempo completo |
| Backend Developer (Laravel) | Tiempo completo |
| Frontend Developer (Blade/JS) | Tiempo completo |
| UI/UX Designer | Tiempo parcial (semanas 1-2) |
| AI/ML Engineer | Tiempo parcial (semanas 8-10) |
| QA Tester | Tiempo completo (semanas 11-12) |
| DevOps | Tiempo parcial (semanas 1, 12) |
