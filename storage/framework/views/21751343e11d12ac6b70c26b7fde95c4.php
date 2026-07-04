<?php $__env->startSection('title', 'Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Gestionar Usuarios</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.usuarios.index')); ?>" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, apellido o email..."
                       value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" <?php echo e(request('estado') == 'activo' ? 'selected' : ''); ?>>Activos</option>
                    <option value="suspendido" <?php echo e(request('estado') == 'suspendido' ? 'selected' : ''); ?>>Suspendidos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <?php if(count(request()->query()) > 0): ?>
                <a href="<?php echo e(route('admin.usuarios.index')); ?>" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Pedidos</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($usuario->id); ?></td>
                        <td class="fw-semibold"><?php echo e($usuario->nombre); ?> <?php echo e($usuario->apellido); ?></td>
                        <td><?php echo e($usuario->email); ?></td>
                        <td><?php echo e($usuario->telefono ?? '-'); ?></td>
                        <td>
                            <span class="badge <?php echo e($usuario->esAdmin() ? 'bg-warning text-dark' : 'bg-info text-dark'); ?>">
                                <?php echo e($usuario->role->nombre ?? ($usuario->esAdmin() ? 'Admin' : 'Cliente')); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo e($usuario->estado == 'activo' ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e(ucfirst($usuario->estado)); ?>

                            </span>
                        </td>
                        <td><?php echo e($usuario->pedidos->count()); ?></td>
                        <td><small><?php echo e($usuario->created_at->format('d/m/Y')); ?></small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if($usuario->estado == 'activo'): ?>
                                <form action="<?php echo e(route('admin.usuarios.suspender', $usuario)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-warning btn-action" onclick="return confirm('¿Suspender a <?php echo e($usuario->nombre); ?>?')">
                                        <i class="bi bi-pause-circle"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?php echo e(route('admin.usuarios.activar', $usuario)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-success btn-action">
                                        <i class="bi bi-play-circle"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay usuarios registrados</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <?php echo e($usuarios->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/admin/usuarios/index.blade.php ENDPATH**/ ?>