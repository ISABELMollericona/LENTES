<?php $__env->startSection('title', 'Catálogo de Lentes'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<div class="hero-section position-relative">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <p class="text-gold fw-semibold mb-2" style="font-size: 0.9rem; letter-spacing: 2px;">NUESTRA SELECCIÓN</p>
                <h1 class="display-4 fw-bold mb-3" style="color: #ffffff;">Catálogo de Lentes</h1>
                <p class="fs-5 mb-4" style="color: #d0d0d0;">Explora nuestra exclusiva colección de lentes ópticos y de sol de las mejores marcas del mundo. Encuentra tu estilo perfecto.</p>
                <p class="text-gold fw-semibold" style="font-size: 1.1rem;">
                    <i class="bi bi-search me-2"></i><?php echo e($lentes->total()); ?> lentes disponibles
                </p>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <i class="bi bi-eyeglasses" style="font-size: 120px; color: rgba(212, 175, 55, 0.2);"></i>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Filtros y Ordenamiento -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 2px solid rgba(212, 175, 55, 0.3);">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-grid me-2" style="color: #D4AF37;"></i>Catálogo
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="bi bi-check-circle me-1" style="color: #D4AF37;"></i><?php echo e($lentes->total()); ?> resultados encontrados
                </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-gold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                    <i class="bi bi-funnel"></i> Filtros
                    <?php if(count(request()->query()) > 0): ?>
                    <span class="badge bg-gold" style="background: #D4AF37; color: #1a1a1a; font-size: 0.75rem;"><?php echo e(count(request()->query())); ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-gold dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <i class="bi bi-sort"></i> Ordenar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" style="background: #1a1a1a; border-color: rgba(212, 175, 55, 0.2);">
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'reciente'])); ?>" style="color: #e0e0e0;">Más recientes</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'precio_asc'])); ?>" style="color: #e0e0e0;">Menor precio</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'precio_desc'])); ?>" style="color: #e0e0e0;">Mayor precio</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'nombre'])); ?>" style="color: #e0e0e0;">A-Z</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Marcas Premium -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <div class="text-muted small me-3" style="line-height: 40px;">Marcas destacadas:</div>
            <?php $__currentLoopData = ['RAY-BAN', 'TOM FORD', 'GUCCI', 'PERSOL', 'OAKLEY', 'SAINT LAURENT', 'PRADA', 'DIOR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nombreMarca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $marcaActiva = strtoupper(request('marca', ''));
                $esActiva = $marcaActiva === strtoupper($nombreMarca);
            ?>
            <a href="<?php echo e($esActiva ? route('catalogo.index') : '?marca='.urlencode($nombreMarca)); ?>"
               class="btn btn-sm <?php echo e($esActiva ? 'btn-gold' : 'btn-outline-gold'); ?>"
               style="border-radius: 25px; font-size: 0.85rem;">
                <?php if($esActiva): ?><i class="bi bi-x-circle me-1"></i><?php endif; ?>
                <?php echo e($nombreMarca); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Grid de Lentes -->
    <div class="row">
        <div class="col-12">
            <?php if($lentes->count() > 0): ?>
            <div class="row g-4 card-grid">
                <?php $__currentLoopData = $lentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-sm-6 col-lg-4 col-xl-3 scroll-reveal">
                    <div class="card card-producto h-100 shadow-sm">
                        <div class="position-relative overflow-hidden" style="aspect-ratio: 4/3; background: #ffffff;">
                             <img src="<?php echo e($lente->imagen_url ?? 'https://via.placeholder.com/300x300?text=Sin+Imagen'); ?>"
                                 class="card-img-top w-100 h-100" alt="<?php echo e($lente->nombre); ?>"
                                 style="object-fit: contain; object-position: center; padding: 8px;">
                            
                            <!-- Badge de estado -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if($lente->estado == 'disponible'): ?>
                                    <span class="badge badge-disponible" style="background: linear-gradient(135deg, #2ecc71, #27ae60); font-size: 0.75rem; padding: 0.5rem 0.75rem;">
                                        <i class="bi bi-check-circle me-1"></i>Disponible
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-vendido" style="background: linear-gradient(135deg, #e74c3c, #c0392b); font-size: 0.75rem; padding: 0.5rem 0.75rem;">
                                        <i class="bi bi-x-circle me-1"></i>Vendido
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Badge especial si es destacado -->
                            <?php if($lente->destacado ?? false): ?>
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-warning text-dark" style="font-size: 0.75rem; padding: 0.5rem 0.75rem;">
                                    <i class="bi bi-star-fill me-1"></i>Destacado
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Marca -->
                            <p class="text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 0.5rem;">
                                <i class="bi bi-tag me-1"></i><?php echo e($lente->marca->nombre ?? 'Sin marca'); ?>

                            </p>
                            
                            <!-- Nombre -->
                            <h6 class="card-title fw-bold mb-2" style="color: #ffffff; font-size: 1rem; line-height: 1.3; min-height: 2.6rem;">
                                <?php echo e($lente->nombre); ?>

                            </h6>

                            <!-- Info del producto -->
                            <p class="card-text small flex-grow-1" style="color: #999999; margin-bottom: 0.75rem;">
                                <i class="bi bi-bag me-1"></i><?php echo e($lente->categoria->nombre ?? 'N/A'); ?> •
                                <i class="bi bi-person me-1"></i><?php echo e(ucfirst($lente->genero)); ?>

                            </p>

                            <!-- Rating y Precio -->
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <div>
                                    <div class="text-warning mb-1">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-half"></i>
                                        <small class="text-muted ms-1">(4.8)</small>
                                    </div>
                                    <span class="precio">Bs <?php echo e(number_format($lente->precio, 2)); ?></span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <?php if($lente->estado == 'disponible'): ?>
                                <?php if(auth()->guard()->check()): ?>
                                <form action="<?php echo e(route('carrito.agregar', $lente)); ?>" method="POST" class="d-inline w-100 ajax-add-cart">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-agregar w-100 fw-semibold py-2" style="border-radius: 8px;">
                                        <i class="bi bi-cart-plus me-1"></i>Agregar al Carrito
                                    </button>
                                </form>
                                <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-gold w-100 fw-semibold py-2" style="border-radius: 8px;">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                                </a>
                                <?php endif; ?>
                            <?php else: ?>
                            <div class="alert alert-danger py-2 mb-0 text-center" style="border-radius: 8px; font-size: 0.9rem;">
                                <i class="bi bi-info-circle me-1"></i>Agotado
                            </div>
                            <?php endif; ?>

                            <a href="<?php echo e(route('catalogo.show', $lente)); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-5">
                <?php echo e($lentes->withQueryString()->links('partials.pagination')); ?>

            </div>
            <?php else: ?>
            <!-- Sin resultados -->
            <div class="text-center py-5">
                <div style="font-size: 4rem; color: rgba(212, 175, 55, 0.3); margin-bottom: 1rem;">
                    <i class="bi bi-search"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: #ffffff;">No encontramos lentes con esos filtros</h4>
                <p class="text-muted mb-4">Intenta con otros criterios de búsqueda o explora nuestro catálogo completo</p>
                <a href="<?php echo e(route('catalogo.index')); ?>" class="btn btn-gold">
                    <i class="bi bi-arrow-clockwise me-1"></i>Ver todos los lentes
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filtrosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content modal-gold" style="background: linear-gradient(135deg, rgba(26, 26, 26, 0.95), rgba(15, 15, 15, 0.95)); border: 1px solid rgba(212, 175, 55, 0.2);">
            <div class="modal-header" style="background: linear-gradient(90deg, #1a1a1a, #000000); border-bottom: 2px solid rgba(212, 175, 55, 0.3); color: #D4AF37;">
                <h5 class="modal-title fw-bold" id="filtrosModalLabel">
                    <i class="bi bi-funnel me-2"></i>Filtros Avanzados
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="color: #e0e0e0;">
                <form method="GET" action="<?php echo e(route('catalogo.index')); ?>" id="form-filtros">
                    <!-- Búsqueda -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-search me-2"></i>Búsqueda
                        </label>
                        <input type="text" class="form-control" name="search" placeholder="Buscar lentes..."
                               value="<?php echo e(request('search')); ?>" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                    </div>

                    <!-- Género -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-person me-2"></i>Género
                        </label>
                        <div class="d-flex flex-column gap-2">
                            <?php $__currentLoopData = ['todos' => 'Todos', 'hombre' => 'Hombre', 'mujer' => 'Mujer', 'unisex' => 'Unisex']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="genero" id="gen_<?php echo e($val); ?>"
                                       value="<?php echo e($val == 'todos' ? '' : $val); ?>" 
                                       <?php echo e((request('genero') == ($val == 'todos' ? '' : $val)) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="gen_<?php echo e($val); ?>"><?php echo e($label); ?></label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Tipo de Montura -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-glasses me-2"></i>Tipo de Montura
                        </label>
                        <select class="form-select" name="tipo_montura" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                            <option value="">Todas</option>
                            <option value="completa" <?php echo e(request('tipo_montura') == 'completa' ? 'selected' : ''); ?>>Completa</option>
                            <option value="semi_al_aire" <?php echo e(request('tipo_montura') == 'semi_al_aire' ? 'selected' : ''); ?>>Semi al aire</option>
                            <option value="al_aire" <?php echo e(request('tipo_montura') == 'al_aire' ? 'selected' : ''); ?>>Al aire</option>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-tag me-2"></i>Marca
                        </label>
                        <select class="form-select" name="marca_id" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                            <option value="">Todas</option>
                            <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($marca->id); ?>" <?php echo e(request('marca_id') == $marca->id ? 'selected' : ''); ?>>
                                <?php echo e($marca->nombre); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-collection me-2"></i>Categoría
                        </label>
                        <select class="form-select" name="categoria_id" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                            <option value="">Todas</option>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(request('categoria_id') == $cat->id ? 'selected' : ''); ?>>
                                <?php echo e($cat->nombre); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Rango de Precio -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2" style="color: #D4AF37;">
                            <i class="bi bi-cash-coin me-2"></i>Rango de Precio (Bs)
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control" name="precio_min" placeholder="Mínimo"
                                       value="<?php echo e(request('precio_min')); ?>" min="0" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="precio_max" placeholder="Máximo"
                                       value="<?php echo e(request('precio_max')); ?>" min="0" style="background: rgba(26, 26, 26, 0.6); border-color: rgba(212, 175, 55, 0.2);">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gold fw-semibold py-2">
                            <i class="bi bi-check-circle me-2"></i>Aplicar Filtros
                        </button>
                        <a href="<?php echo e(route('catalogo.index')); ?>" class="btn btn-outline-gold fw-semibold py-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-content .form-select,
    .modal-content .form-control {
        color: #e0e0e0;
    }
    .modal-content .form-select option,
    .modal-content .form-control option {
        background: #1a1a1a;
        color: #e0e0e0;
    }
    .modal-content .form-check-label {
        color: #e0e0e0;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.ajax-add-cart').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Agregando...';
        try {
            const resp = await fetch(this.action, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value } 
            });
            const data = await resp.json();
            if (resp.ok) {
                const badge = document.getElementById('cart-count');
                if (badge) badge.textContent = parseInt(badge.textContent) + 1;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Agregado';
                btn.classList.remove('btn-agregar');
                btn.classList.add('btn-success');
                setTimeout(() => btn.closest('form').reset(), 1500);
            } else {
                alert(data.message || 'Error al agregar');
                btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Agregar al Carrito';
                btn.disabled = false;
            }
        } catch(err) {
            alert('Error de conexión');
            btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Agregar al Carrito';
            btn.disabled = false;
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/lentes/index.blade.php ENDPATH**/ ?>