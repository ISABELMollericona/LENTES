# HISTORIAS DE USUARIO - Óptica Golden eCommerce

## Módulo 1: Autenticación

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-001 | Como cliente, quiero registrarme en la plataforma para poder comprar lentes en línea. | - Formulario con todos los campos<br>- Validación de email único<br>- Contraseña mínima 8 caracteres<br>- Confirmación de registro |
| HU-002 | Como cliente, quiero iniciar sesión para acceder a mi cuenta. | - Login con email y contraseña<br>- Recordar sesión opcional<br>- Redirección al dashboard |
| HU-003 | Como cliente, quiero recuperar mi contraseña si la olvido. | - Enlace de recuperación<br>- Email con token temporal<br>- Formulario de nueva contraseña |
| HU-004 | Como cliente, quiero editar mi perfil para mantener mis datos actualizados. | - Editar nombre, teléfono, dirección<br>- No modificar email<br>- Validación de datos |
| HU-005 | Como cliente, quiero cerrar sesión para proteger mi cuenta. | - Botón cerrar sesión<br>- Invalidación de tokens<br>- Redirección al inicio |

## Módulo 2: Catálogo

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-006 | Como cliente, quiero ver todos los lentes disponibles para explorar opciones. | - Grid de productos con imágenes<br>- Paginación<br>- Indicador de disponibilidad |
| HU-007 | Como cliente, quiero buscar lentes por nombre o código para encontrar rápidamente. | - Búsqueda en tiempo real con AJAX<br>- Resultados filtrados instantáneamente |
| HU-008 | Como cliente, quiero filtrar lentes por género, color, marca y precio para refinar mi búsqueda. | - Filtros combinables<br>- Actualización sin recargar página<br>- Rango de precio con slider |
| HU-009 | Como cliente, quiero ver los detalles de un lente para decidir si comprarlo. | - Imagen principal + galería<br>- Descripción completa<br>- Precio y disponibilidad claros |
| HU-010 | Como administrador, quiero gestionar el catálogo de lentes para mantenerlo actualizado. | - CRUD completo<br>- Carga de imágenes<br>- Cambio de estado individual |

## Módulo 3: Asesor Virtual

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-011 | Como cliente, quiero un asesor virtual que me guíe a elegir los lentes ideales. | - Chat interactivo<br>- Preguntas progresivas<br>- Respuestas en lenguaje natural |
| HU-012 | Como cliente, quiero recibir recomendaciones basadas en mis respuestas. | - Lista de lentes recomendados<br>- % de compatibilidad visible<br>- Explicación de cada recomendación |
| HU-013 | Como cliente, quiero que el asesor considere mi presupuesto. | - Input de presupuesto máximo<br>- Filtro automático por precio |
| HU-014 | Como cliente, quiero que el asesor me recomiende según mi estilo. | - Opciones: Clásico, Moderno, Ejecutivo, Deportivo, Minimalista<br>- Mapping de estilos a categorías |

## Módulo 4: Análisis Facial

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-015 | Como cliente, quiero subir una foto para que el sistema analice mi rostro. | - Subida de imagen o captura de cámara<br>- Validación de formato (JPG, PNG)<br>- Máximo 5MB |
| HU-016 | Como cliente, quiero que el sistema detecte la forma de mi rostro. | - Detección automática<br>- Resultado visual de la forma<br>- Uso en recomendaciones |
| HU-017 | Como cliente, quiero que las recomendaciones mejoren con mi forma facial. | - Reglas forma-montura aplicadas<br>- Prioridad en recomendaciones |

## Módulo 5-6: Carrito y Compra

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-018 | Como cliente, quiero agregar lentes a mi carrito para comprarlos después. | - Botón agregar en catálogo y detalle<br>- Validación de disponibilidad<br>- Feedback visual |
| HU-019 | Como cliente, quiero ver mi carrito y el total a pagar. | - Lista de items<br>- Precio unitario y total<br>- Opción de eliminar items |
| HU-020 | Como cliente, quiero confirmar mi compra para recibir los lentes. | - Botón confirmar compra<br>- Validación final de disponibilidad<br>- Redirección a pago |

## Módulo 7: Pedidos

| ID  | Historia | Criterios de Aceptación |
|-----|----------|------------------------|
| HU-021 | Como cliente, quiero ver mis pedidos realizados para dar seguimiento. | - Lista de pedidos con estado<br>- Detalle de cada pedido<br>- Fecha y total |
| HU-022 | Como administrador, quiero gestionar los estados de los pedidos. | - Cambiar estado<br>- Notificación al cliente |
| HU-023 | Como administrador, quiero ver el dashboard con estadísticas. | - KPIs principales<br>- Gráficos actualizados en tiempo real<br>- Reportes exportables |
