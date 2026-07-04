# RESUMEN EJECUTIVO - Óptica Golden eCommerce + Asesor Virtual IA

## Visión General

Plataforma web eCommerce para la Óptica Golden que permite a clientes visualizar, consultar y comprar lentes ópticos y de sol en línea, incorporando un Asesor Virtual basado en Inteligencia Artificial para recomendaciones personalizadas.

## Arquitectura

```
[Cliente Web/Navegador]
        │
        ├── Blade + Bootstrap 5 + JavaScript/AJAX ───→ [Frontend Web]
        │
        └── API REST (Sanctum) ───→ [Backend Laravel 12]
                                      │
                                      ├── MySQL 8.0 (Base de Datos)
                                      ├── Gemini/Groq API (IA Conversacional)
                                      └── MediaPipe (Análisis Facial)
```

## Componentes Clave

| Componente | Tecnología | Función |
|------------|------------|---------|
| Frontend | Blade, Bootstrap 5, JS, AJAX | Interfaz de usuario responsive |
| Backend | Laravel 12, PHP 8.3 | Lógica de negocio y API |
| Base de Datos | MySQL 8.0 (InnoDB) | Persistencia de datos |
| Autenticación | Laravel Sanctum | Tokens de acceso seguro |
| IA Conversacional | Gemini API / Groq API | Chat asesor virtual |
| Visión Artificial | MediaPipe Face Detection/Mesh | Análisis de forma facial |
| Motor de Recomendación | PHP (Reglas + Scoring) | Recomendaciones personalizadas |

## Estructura de Base de Datos (15 tablas)

- **Catálogo:** roles, usuarios, categorias, marcas, lentes, imagenes_lentes
- **Transaccional:** pedidos, detalle_pedidos, pagos, carritos
- **IA:** analisis_faciales, recomendaciones, detalle_recomendaciones, chat_ia
- **Seguridad:** personal_access_tokens, password_reset_tokens

## Funcionalidades Principales

1. **Catálogo Inteligente:** Búsqueda, filtros combinados (género, precio, marca, color, montura), paginación
2. **Asesor Virtual IA:** Chat conversacional que guía al usuario preguntando uso, presupuesto, estilo, colores y tipo de montura
3. **Análisis Facial:** Detección de forma de rostro (ovalado, redondo, cuadrado, rectangular, corazón, diamante) usando MediaPipe
4. **Motor de Recomendación:** Algoritmo que combina forma facial, preferencias y reglas de estilo para recomendar lentes con % de compatibilidad
5. **Carrito + Pedidos + Pagos:** Flujo completo de compra con validación de disponibilidad
6. **Panel Administrativo:** Dashboard con KPIs, gestión completa de lentes/pedidos/usuarios, reportes exportables

## Reglas de Negocio Críticas

- Cada lente es único (stock = 1)
- Estado binario: Disponible / Vendido
- Solo usuarios registrados compran
- Recomendaciones solo sobre lentes disponibles
- Transacciones atómicas para evitar sobreventa

## Stack Tecnológico

```
Laravel 12 + PHP 8.3
MySQL 8.0 (InnoDB, utf8mb4)
Blade + Bootstrap 5 + JavaScript + AJAX
Laravel Sanctum (API Auth)
Gemini API 2.0 Flash / Groq API (Llama 3)
MediaPipe Face Detection + Face Mesh
Chart.js (Dashboard)
DomPDF (Comprobantes)
```

## Tiempo Estimado: 12 semanas

## Equipo: 6 personas (PM, Backend, Frontend, UI/UX, AI/ML, QA, DevOps)
