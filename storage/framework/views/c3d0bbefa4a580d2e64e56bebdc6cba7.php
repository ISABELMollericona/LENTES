<?php $__env->startSection('title', 'Gestionar Lentes'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center page-header gap-2">
    <div>
        <h1><i class="bi bi-eyeglasses me-2"></i>Gestionar Lentes</h1>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('admin.lentes.create')); ?>" class="btn btn-admin-gold">
            <i class="bi bi-plus-lg me-1"></i>Nuevo lente
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.lentes.index')); ?>" class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, código o marca..."
                       value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="disponible" <?php echo e(request('estado') == 'disponible' ? 'selected' : ''); ?>>Disponible</option>
                    <option value="vendido" <?php echo e(request('estado') == 'vendido' ? 'selected' : ''); ?>>Vendido</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="genero">
                    <option value="">Todos</option>
                    <option value="hombre" <?php echo e(request('genero') == 'hombre' ? 'selected' : ''); ?>>Hombre</option>
                    <option value="mujer" <?php echo e(request('genero') == 'mujer' ? 'selected' : ''); ?>>Mujer</option>
                    <option value="unisex" <?php echo e(request('genero') == 'unisex' ? 'selected' : ''); ?>>Unisex</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <?php if(count(request()->query()) > 0): ?>
                <a href="<?php echo e(route('admin.lentes.index')); ?>" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Género</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $lentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <img src="<?php echo e($lente->imagen_url ?? 'https://via.placeholder.com/50'); ?>"
                                 style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td class="fw-semibold"><?php echo e($lente->codigo); ?></td>
                        <td><?php echo e($lente->nombre); ?></td>
                        <td><?php echo e($lente->marca->nombre ?? '-'); ?></td>
                        <td><?php echo e($lente->categoria->nombre ?? '-'); ?></td>
                        <td class="fw-bold" style="color: #D4AF37;">Bs <?php echo e(number_format($lente->precio, 2)); ?></td>
                        <td>
                            <span class="badge <?php echo e($lente->estado == 'disponible' ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e(ucfirst($lente->estado)); ?>

                            </span>
                        </td>
                        <td><?php echo e(ucfirst($lente->genero)); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('admin.lentes.edit', $lente)); ?>" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo e(route('admin.lentes.show', $lente)); ?>" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="<?php echo e(route('admin.lentes.destroy', $lente)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este lente?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php if($lente->estado == 'disponible'): ?>
                                <form action="<?php echo e(route('admin.lentes.estado', $lente)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="estado" value="vendido">
                                    <button class="btn btn-sm btn-outline-warning btn-action" title="Marcar como vendido">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?php echo e(route('admin.lentes.estado', $lente)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="estado" value="disponible">
                                    <button class="btn btn-sm btn-outline-success btn-action" title="Marcar como disponible">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay lentes registrados</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <?php echo e($lentes->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/admin/lentes/index.blade.php ENDPATH**/ ?>