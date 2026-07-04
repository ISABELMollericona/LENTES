# DISEÑO UI/UX - Óptica Golden

## Paleta de Colores

```css
:root {
  /* Colores principales */
  --primary: #1a1a1a;        /* Negro elegante - sofisticación, lujo */
  --primary-light: #333333;
  --primary-dark: #000000;

  /* Dorado - lujo, calidez, premium */
  --gold: #D4AF37;
  --gold-light: #F0D060;
  --gold-dark: #B8962E;

  /* Neutros */
  --white: #ffffff;
  --gray-50: #f8f9fa;
  --gray-100: #f1f3f5;
  --gray-200: #e9ecef;
  --gray-300: #dee2e6;
  --gray-500: #868e96;
  --gray-700: #495057;
  --gray-900: #212529;

  /* Estados */
  --success: #2b8a3e;        /* Verde - disponible */
  --danger: #c92a2a;         /* Rojo - vendido */
  --warning: #e67700;
  --info: #1864ab;
}
```

## Tipografía

```css
--font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
--font-display: 'Playfair Display', serif; /* Para títulos destacados */
```

## Componentes UI Principales

### 1. Navbar
- Logo Óptica Golden en **dorado** (#D4AF37) sobre fondo **negro** (#1a1a1a)
- Buscador (centro, visible en catálogo)
- Icono carrito + badge cantidad (dorado)
- Menú usuario dropdown (Perfil, Pedidos, Cerrar sesión)
- Fixed-top con sombra sutil
- Hover en links: texto dorado sobre fondo negro
- Responsive: menú hamburguesa en mobile

### 2. Cards de Producto
- Imagen principal (16:9, object-fit cover)
- Borde sutil dorado en hover
- Nombre del producto en negro
- Precio en **dorado** (#D4AF37), bold
- Badge estado: ● Disponible (verde) o ● Vendido (rojo)
- Botón "Agregar al carrito": dorado (#D4AF37) con texto negro, hover más brillante
- Hover: sombra elevada + scale(1.02) + borde dorado
- Transición suave (0.3s ease)

### 3. Chat Asesor Virtual
- Panel lateral derecho (o full en mobile)
- Burbujas: usuario (derecha, fondo dorado, texto negro), sistema (izquierda, fondo gris claro)
- Botones de respuesta rápida: borde dorado, hover fondo dorado
- Indicador "escribiendo..." animado en dorado
- Input + botón enviar (botón dorado)
- Botón "Análisis Facial" con icono cámara
- Scroll automático a último mensaje
- Header del chat: fondo negro con título en dorado

### 4. Filtros
- Sidebar en desktop (izquierda) con header negro
- Acordeón en mobile
- Selectores con borde dorado al focus
- Slider rango precio con track dorado
- Botón "Aplicar" dorado, "Limpiar" outline dorado
- Contador de resultados visibles

### 5. Panel Admin
- Sidebar: fondo negro, items con iconos dorados, hover: fondo gris oscuro
- Cards de KPIs con borde superior dorado, iconos dorados
- Tablas con búsqueda y paginación, header negro con texto dorado
- Gráficos (Chart.js): estilo dorado/negro
- Botones de acción primary: dorado, danger: rojo, success: verde

## Diseño Responsive

| Breakpoint | Dispositivo | Comportamiento |
|------------|-------------|---------------|
| < 576px | Mobile | 1 columna, nav hamburguesa, filtros acordeón |
| 576-768px | Tablet | 2 columnas, sidebar oculto |
| 768-1024px | Tablet landscape | 3 columnas, sidebar compacto |
| > 1024px | Desktop | 4 columnas, sidebar completo |

## Micro-interacciones

1. **Loading states**: Skeleton screens en cards mientras cargan
2. **Transitions**: Fade-in en resultados, slide en carrito
3. **Hover effects**: Card elevación, botones darken
4. **Feedback**: Toast notifications para acciones (Agregado, Error, etc.)
5. **Empty states**: Ilustraciones cuando no hay resultados
6. **Error states**: Inputs con borde rojo + mensaje de error

## Animaciones

```css
/* Card hover */
.card-producto {
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  border: 1px solid transparent;
}
.card-producto:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(212, 175, 55, 0.15);
  border-color: var(--gold);
}

/* Botón dorado */
.btn-gold {
  background: linear-gradient(135deg, #D4AF37, #B8962E);
  color: #1a1a1a;
  font-weight: 600;
  border: none;
  transition: all 0.3s ease;
}
.btn-gold:hover {
  background: linear-gradient(135deg, #F0D060, #D4AF37);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
}

/* Navbar gold accent */
.navbar-golden {
  background: linear-gradient(180deg, #1a1a1a, #000000) !important;
  border-bottom: 2px solid #D4AF37;
}
.navbar-golden .nav-link:hover {
  color: #D4AF37 !important;
}
.navbar-golden .navbar-brand {
  color: #D4AF37 !important;
  font-weight: 700;
}

/* Gold focus ring */
.form-control:focus {
  border-color: #D4AF37;
  box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
}

/* Skeleton loading */
@keyframes shimmer {
  0% { background-position: -200px 0; }
  100% { background-position: calc(200px + 100%) 0; }
}
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200px 100%;
  animation: shimmer 1.5s infinite;
}
```

## Accesibilidad (WCAG 2.1 AA)

- Contraste mínimo 4.5:1 en texto normal
- Texto dorado solo usado para decoración/énfasis, no para contenido crítico
- Texto negro sobre fondo dorado garantiza contraste suficiente
- Todos los botones con aria-label
- Formularios con labels visibles
- Navegación por teclado (Tab, Enter, Escape)
- Alt text descriptivo en imágenes
- Focus visible en todos los elementos interactivos
- Mensajes de error asociados con aria-describedby
