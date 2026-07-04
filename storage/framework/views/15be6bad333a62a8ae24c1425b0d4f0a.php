<?php $__env->startSection('title', 'Datos de entrega y medidas'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <h3 class="fw-bold mb-0"><i class="bi bi-clipboard2-check me-2" style="color:#D4AF37;"></i>Datos de entrega</h3>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger mb-3">
        <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('carrito.procesarCheckout')); ?>">
        <?php echo csrf_field(); ?>
        <div class="row g-4">

            
            <div class="col-lg-8">

                
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                        <i class="bi bi-geo-alt me-1"></i> Dirección de entrega
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dirección completa <span class="text-danger">*</span></label>
                            <textarea name="direccion_entrega" class="form-control <?php $__errorArgs = ['direccion_entrega'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                rows="3" placeholder="Ej: Calle Sucre #452, Zona Central, Edificio Dorado Piso 3, Ref: frente a la plaza, La Paz"
                                required><?php echo e(old('direccion_entrega')); ?></textarea>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Incluye calle, número, zona, ciudad y una referencia.
                            </div>
                            <?php $__errorArgs = ['direccion_entrega'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $lente = $item->lente; $esSol = $lente->tipo_lente === 'sol'; ?>
                <div class="card shadow-sm mb-3" style="border-left: 3px solid #D4AF37;">
                    <div class="card-header fw-semibold d-flex align-items-center gap-2" style="background:#111; color:#D4AF37;">
                        <i class="bi bi-<?php echo e($esSol ? 'sun' : 'eyeglasses'); ?> me-1"></i>
                        Medidas para: <span class="text-white"><?php echo e($lente->nombre); ?></span>
                        <span class="badge ms-auto" style="background:<?php echo e($esSol ? '#8B5E00' : '#1a4a2e'); ?>; font-size:0.7rem;">
                            <?php echo e($esSol ? 'Lente de sol' : 'Lente óptico'); ?>

                        </span>
                    </div>
                    <div class="card-body p-4">

                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-arrows-expand me-1" style="color:#D4AF37;"></i>
                                Distancia Pupilar (DP) <small class="text-muted fw-normal">en mm</small>
                            </label>
                            <input type="number" step="0.5" min="40" max="80"
                                name="medidas[<?php echo e($lente->id); ?>][dp]"
                                class="form-control" style="max-width:160px;"
                                placeholder="Ej: 62.5"
                                value="<?php echo e(old('medidas.'.$lente->id.'.dp')); ?>">
                            <div class="form-text text-muted">Es la distancia entre el centro de cada pupila.</div>
                        </div>

                        <?php if(!$esSol): ?>
                        
                        <p class="fw-semibold mb-3" style="color:#D4AF37;">
                            <i class="bi bi-prescription me-1"></i>Graduación (Rx)
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" style="font-size:0.9rem;">
                                <thead style="background:#1a1a1a; color:#D4AF37;">
                                    <tr>
                                        <th style="width:110px;">Ojo</th>
                                        <th>Esfera</th>
                                        <th>Cilindro</th>
                                        <th>Eje (°)</th>
                                        <th>Adición</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold"><i class="bi bi-circle me-1"></i>OD (Derecho)</td>
                                        <td>
                                            <input type="number" step="0.25" min="-20" max="20"
                                                name="medidas[<?php echo e($lente->id); ?>][od_esfera]"
                                                class="form-control form-control-sm" placeholder="Ej: -1.50"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.od_esfera')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="-10" max="10"
                                                name="medidas[<?php echo e($lente->id); ?>][od_cilindro]"
                                                class="form-control form-control-sm" placeholder="Ej: -0.50"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.od_cilindro')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="0" max="180"
                                                name="medidas[<?php echo e($lente->id); ?>][od_eje]"
                                                class="form-control form-control-sm" placeholder="Ej: 90"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.od_eje')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="0" max="4"
                                                name="medidas[<?php echo e($lente->id); ?>][od_adicion]"
                                                class="form-control form-control-sm" placeholder="Ej: 1.00"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.od_adicion')); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold"><i class="bi bi-circle me-1"></i>OI (Izquierdo)</td>
                                        <td>
                                            <input type="number" step="0.25" min="-20" max="20"
                                                name="medidas[<?php echo e($lente->id); ?>][oi_esfera]"
                                                class="form-control form-control-sm" placeholder="Ej: -1.25"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.oi_esfera')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="-10" max="10"
                                                name="medidas[<?php echo e($lente->id); ?>][oi_cilindro]"
                                                class="form-control form-control-sm" placeholder="Ej: -0.75"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.oi_cilindro')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="0" max="180"
                                                name="medidas[<?php echo e($lente->id); ?>][oi_eje]"
                                                class="form-control form-control-sm" placeholder="Ej: 85"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.oi_eje')); ?>">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="0" max="4"
                                                name="medidas[<?php echo e($lente->id); ?>][oi_adicion]"
                                                class="form-control form-control-sm" placeholder="Ej: 1.00"
                                                value="<?php echo e(old('medidas.'.$lente->id.'.oi_adicion')); ?>">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-text text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Puedes dejar en blanco los campos que no aplican a tu receta. La adición se usa solo para bifocal/progresivo.
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

            
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="border-top: 3px solid #D4AF37; top: 20px;">
                    <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                        <i class="bi bi-receipt me-1"></i> Resumen del pedido
                    </div>
                    <div class="card-body">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e($item->lente->imagen_url); ?>"
                                    alt="<?php echo e($item->lente->nombre); ?>"
                                    style="width:40px; height:32px; object-fit:cover; border-radius:4px;">
                                <span><?php echo e($item->lente->nombre); ?></span>
                            </div>
                            <span class="fw-semibold">Bs <?php echo e(number_format($item->lente->precio, 2)); ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <hr style="border-color:#D4AF37;">

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                            <span>Total</span>
                            <span style="color:#D4AF37;">Bs <?php echo e(number_format($total, 2)); ?></span>
                        </div>

                        <div class="alert mb-3 p-2 text-center" style="background:rgba(212,175,55,0.10); border:1px solid rgba(212,175,55,0.35); border-radius:8px;">
                            <i class="bi bi-qr-code me-1" style="color:#D4AF37;"></i>
                            <small class="fw-semibold" style="color:#D4AF37;">Pago por QR</small><br>
                            <small class="text-muted">Recibirás el QR en el siguiente paso</small>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2 fw-semibold">
                            <i class="bi bi-arrow-right-circle me-2"></i>Continuar al pago QR
                        </button>
                        <a href="<?php echo e(route('carrito.index')); ?>" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Volver al carrito
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/carrito/checkout.blade.php ENDPATH**/ ?>