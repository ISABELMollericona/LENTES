# PLAN DE PRUEBAS - Óptica Golden eCommerce

## 1. Pruebas Unitarias

### Backend (PHPUnit)

| ID  | Módulo | Prueba | Descripción |
|-----|--------|--------|-------------|
| PU-01 | Auth | test_user_can_register | Verificar registro con datos válidos |
| PU-02 | Auth | test_user_cannot_register_duplicate_email | Email duplicado debe rechazarse |
| PU-03 | Auth | test_user_can_login | Login con credenciales correctas |
| PU-04 | Auth | test_user_cannot_login_invalid | Login con credenciales incorrectas |
| PU-05 | Lentes | test_lente_scope_disponible | Scope devuelve solo disponibles |
| PU-06 | Lentes | test_lente_scope_vendido | Scope devuelve solo vendidos |
| PU-07 | Lentes | test_marcar_como_vendido | Cambio de estado funciona |
| PU-08 | Recomendación | test_recommendation_engine | Motor genera puntajes correctos |
| PU-09 | Recomendación | test_face_shape_classifier | Clasificador devuelve forma válida |
| PU-10 | Carrito | test_cart_add_lente | Agregar lente disponible |
| PU-11 | Carrito | test_cart_add_vendido | Rechazar lente vendido |
| PU-12 | Carrito | test_cart_duplicate | Rechazar duplicado en carrito |
| PU-13 | Pedido | test_create_order | Crear pedido desde carrito |
| PU-14 | Pedido | test_order_status_transition | Transiciones de estado válidas |

### Frontend (Jest/Vitest)

| ID  | Módulo | Prueba |
|-----|--------|--------|
| PF-01 | FaceAnalyzer | detectFaceSimple returns object |
| PF-02 | FaceAnalyzer | estimateFaceShape returns valid shape |
| PF-03 | AsesorVirtual | addUserMessage appends DOM |
| PF-04 | AsesorVirtual | addSystemMessage appends DOM |
| PF-05 | Carrito | total calculation correct |

## 2. Pruebas de Integración

| ID  | Escenario | Flujo |
|-----|-----------|-------|
| PI-01 | Registro -> Login -> Catálogo | Usuario se registra, inicia sesión y ve catálogo |
| PI-02 | Búsqueda -> Filtros -> Detalle | Usuario busca y filtra lentes |
| PI-03 | Asesor Virtual -> Recomendación -> Carrito | Chat, obtiene recomendaciones, agrega al carrito |
| PI-04 | Análisis Facial -> Recomendación | Sube foto, detecta forma, recomienda |
| PI-05 | Carrito -> Pedido -> Pago | Compra completa |
| PI-06 | Admin -> Crear Lente -> Catálogo | Admin crea lente, aparece en catálogo |
| PI-07 | Admin -> Cambiar estado pedido | Flujo completo de gestión de pedido |

## 3. Pruebas de Aceptación (E2E)

### HU-011: Asesor Virtual
```gherkin
Feature: Asesor Virtual
  Scenario: Usuario recibe recomendaciones personalizadas
    Given el usuario está autenticado en la plataforma
    When accede al Asesor Virtual
    And responde las preguntas del asesor
    And confirma sus preferencias
    Then el sistema muestra una lista de lentes recomendados
    And cada lente tiene un porcentaje de compatibilidad
    And las recomendaciones son solo de lentes disponibles
```

### HU-015: Análisis Facial
```gherkin
Feature: Análisis Facial
  Scenario: Usuario sube foto para análisis facial
    Given el usuario está en la sección de análisis facial
    When sube una fotografía de su rostro
    Then el sistema procesa la imagen con MediaPipe
    And muestra la forma del rostro detectada
    And sugiere tipos de montura compatibles
```

### HU-018: Compra
```gherkin
Feature: Compra de Lentes
  Scenario: Usuario completa una compra exitosa
    Given el usuario tiene lentes en su carrito
    When confirma la compra
    Then el sistema crea un pedido con estado "Pendiente"
    And los lentes cambian a estado "Vendido"
    And el carrito queda vacío
```

## 4. Pruebas de Carga

| ID  | Escenario | Usuarios Concurrentes | Tiempo Esperado |
|-----|-----------|----------------------|-----------------|
| PC-01 | Consulta catálogo | 100 | < 2s |
| PC-02 | Búsqueda con filtros | 50 | < 3s |
| PC-03 | Chat IA | 30 | < 5s |
| PC-04 | Análisis facial | 10 | < 10s |
| PC-05 | Compra concurrente | 20 | < 3s |
| PC-06 | Dashboard admin | 5 | < 3s |

## 5. Pruebas de Seguridad

| ID  | Prueba | Descripción |
|-----|--------|-------------|
| PS-01 | SQL Injection | Intentar inyección en campos de búsqueda |
| PS-02 | XSS | Intentar script injection en formularios |
| PS-03 | CSRF | Intentar POST sin token |
| PS-04 | Auth Bypass | Acceder a rutas protegidas sin token |
| PS-05 | Role Escalation | Usuario cliente acceder a rutas admin |
| PS-06 | Rate Limiting | Exceder límite de peticiones API |
| PS-07 | File Upload | Subir archivos maliciosos en análisis facial |

## 6. Herramientas

| Herramienta | Uso |
|-------------|-----|
| PHPUnit | Pruebas unitarias backend |
| Laravel Dusk | Pruebas E2E browser |
| Postman / Insomnia | Pruebas API manuales |
| K6 / JMeter | Pruebas de carga |
| OWASP ZAP | Pruebas de seguridad |
| Lighthouse | Pruebas de rendimiento frontend |

## 7. Ejecución

```bash
# Pruebas unitarias
vendor/bin/phpunit

# Pruebas con cobertura
vendor/bin/phpunit --coverage-html coverage

# Pruebas E2E con Dusk
php artisan dusk

# Pruebas de carga (K6)
k6 run tests/Load/catalogo-load.js
```
