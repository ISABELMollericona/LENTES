<?php $__env->startSection('title', 'Gestionar Pedidos'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="bi bi-box me-2"></i>Gestionar Pedidos</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.pedidos.index')); ?>" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Buscar por código, cliente o email..."
                       value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo e(request('estado') == 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                    <option value="confirmado" <?php echo e(request('estado') == 'confirmado' ? 'selected' : ''); ?>>Confirmado</option>
                    <option value="en_preparacion" <?php echo e(request('estado') == 'en_preparacion' ? 'selected' : ''); ?>>En preparación</option>
                    <option value="entregado" <?php echo e(request('estado') == 'entregado' ? 'selected' : ''); ?>>Entregado</option>
                    <option value="cancelado" <?php echo e(request('estado') == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <?php if(count(request()->query()) > 0): ?>
                <a href="<?php echo e(route('admin.pedidos.index')); ?>" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pedidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($pedido->codigo); ?></td>
                        <td><?php echo e($pedido->usuario->nombre); ?> <?php echo e($pedido->usuario->apellido); ?></td>
                        <td><?php echo e($pedido->fecha_pedido->format('d/m/Y')); ?></td>
                        <td><?php echo e($pedido->detallePedidos->count()); ?></td>
                        <td class="fw-bold" style="color: #D4AF37;">Bs <?php echo e(number_format($pedido->total, 2)); ?></td>
                        <td>
                            <span class="badge estado-<?php echo e($pedido->estado); ?>">
                                <?php echo e(str_replace('_', ' ', ucfirst($pedido->estado))); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($pedido->pago): ?>
                            <span class="badge <?php echo e($pedido->pago->estado == 'aprobado' ? 'bg-success' : ($pedido->pago->estado == 'rechazado' ? 'bg-danger' : 'bg-warning text-dark')); ?>">
                                <?php echo e(ucfirst($pedido->pago->estado)); ?>

                            </span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Sin pago</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('admin.pedidos.show', $pedido)); ?>" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-gold btn-action dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php $__currentLoopData = ['pendiente', 'confirmado', 'en_preparacion', 'entregado', 'cancelado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <form action="<?php echo e(route('admin.pedidos.estado', $pedido)); ?>" method="POST">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="estado" value="<?php echo e($estado); ?>">
                                                <button class="dropdown-item <?php echo e($pedido->estado == $estado ? 'active' : ''); ?>" type="submit">
                                                    <i class="bi <?php echo e($estado == 'entregado' ? 'bi-check-circle text-success' : ($estado == 'cancelado' ? 'bi-x-circle text-danger' : 'bi-circle')); ?> me-2"></i>
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $estado))); ?>

                                                </button>
                                            </form>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No hay pedidos registrados</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <?php echo e($pedidos->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/admin/pedidos/index.blade.php ENDPATH**/ ?>