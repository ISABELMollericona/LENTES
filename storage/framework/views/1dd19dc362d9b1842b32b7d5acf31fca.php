<?php $__env->startSection('title', 'Asesor Virtual'); ?>

<?php $__env->startSection('content'); ?>
<?php if(auth()->guard()->guest()): ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm py-5 px-4" style="border-top: 3px solid #D4AF37; background: #1a1a1a;">
                <i class="bi bi-robot mb-3" style="font-size: 4rem; color: #D4AF37;"></i>
                <h3 class="fw-bold mb-2" style="color: #ffffff;">Asesor Virtual</h3>
                <p class="text-muted mb-4">Inicia sesión para recibir recomendaciones de lentes personalizadas según tu estilo, uso y forma de rostro.</p>
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-gold fw-semibold py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-gold fw-semibold py-2">
                        <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(auth()->guard()->check()): ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <div>
            <h3 class="fw-bold mb-0"><i class="bi bi-robot me-2" style="color: #D4AF37;"></i>Asesor Virtual</h3>
            <p class="text-muted mb-0 small">Golden Assistant te ayuda a encontrar los lentes ideales</p>
        </div>
        <div>
            <button class="btn btn-outline-gold" id="btn-nueva-asesoria">
                <i class="bi bi-plus-circle me-1"></i>Nueva asesoría
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm chat-container">
                <div class="chat-header d-flex align-items-center">
                    <i class="bi bi-robot me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="mb-0">Golden Assistant</h5>
                        <small style="color: #D4AF37; opacity: 0.8;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem; color: #2b8a3e;"></i>En línea
                        </small>
                    </div>
                </div>

                <div class="p-3" id="chat-messages" style="height: 500px; overflow-y: auto; background: #f8f9fa; scroll-behavior: smooth;">
                </div>

                <div class="p-3 border-top">
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" placeholder="Escribe tu mensaje..."
                               style="border-color: #D4AF37;">
                        <button class="btn btn-gold" type="button" id="chat-send">
                            <i class="bi bi-send"></i>
                        </button>
                        <button class="btn btn-outline-gold" type="button" id="btn-recommend" title="Ver recomendaciones">
                            <i class="bi bi-stars"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle me-1"></i>Responde las preguntas del asistente para recibir recomendaciones personalizadas
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                    <i class="bi bi-info-circle me-1"></i> ¿Cómo funciona?
                </div>
                <div class="card-body">
                    <div class="stepper-item visible">
                        <div class="step-number">1</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Indica tu género y uso</h6>
                            <small class="text-muted">Para filtrar lentes adecuados para ti</small>
                        </div>
                    </div>
                    <div class="stepper-item visible" style="transition-delay: 0.1s;">
                        <div class="step-number">2</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Análisis facial en vivo</h6>
                            <small class="text-muted">Ve tus puntos faciales en tiempo real y detecta la forma de tu rostro</small>
                        </div>
                    </div>
                    <div class="stepper-item visible" style="transition-delay: 0.2s;">
                        <div class="step-number">3</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Elige estilo y montura</h6>
                            <small class="text-muted">Personaliza tu búsqueda con más filtros</small>
                        </div>
                    </div>
                    <div class="stepper-item visible" style="transition-delay: 0.3s;">
                        <div class="step-number">4</div>
                        <div>
                            <h6 class="fw-semibold mb-1">Recibe y compra</h6>
                            <small class="text-muted">Lentes recomendados con % de compatibilidad</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body text-center py-3">
                    <i class="bi bi-shield-check" style="font-size: 2rem; color: #D4AF37;"></i>
                    <p class="small text-muted mt-2 mb-0">El análisis facial ocurre en tu dispositivo. No enviamos tu imagen a servidores externos.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="faceModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Análisis Facial en Tiempo Real</h5>
                <button type="button" class="btn-close btn-close-white" id="btn-close-face-modal"></button>
            </div>
            <div class="modal-body p-3">

                
                <div id="face-camera-view">
                    <p class="text-muted small text-center mb-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Centra tu rostro en el recuadro. Los puntos de color muestran el análisis en tiempo real.
                    </p>

                    
                    <div style="position: relative; width: 100%; max-width: 520px; margin: 0 auto; border-radius: 12px; overflow: hidden; background: #000; aspect-ratio: 4/3;">
                        <video id="face-video" autoplay playsinline muted
                               style="width: 100%; height: 100%; object-fit: cover; display: block; transform: scaleX(-1);"></video>
                        <canvas id="face-canvas"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; transform: scaleX(-1);"></canvas>

                        
                        <div id="face-overlay-status"
                             style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
                                    background: rgba(0,0,0,0.65); color: #D4AF37; border-radius: 20px;
                                    padding: 4px 14px; font-size: 0.8rem; white-space: nowrap;">
                            <span class="spinner-border spinner-border-sm me-1"></span>Iniciando cámara...
                        </div>

                        
                        <div id="face-live-shape"
                             style="display:none; position: absolute; top: 10px; left: 50%; transform: translateX(-50%);
                                    background: rgba(212,175,55,0.9); color: #1a1a1a; border-radius: 20px;
                                    padding: 4px 14px; font-size: 0.85rem; font-weight: 700; white-space: nowrap;">
                        </div>
                    </div>

                    
                    <div class="text-center mt-3">
                        <label class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-upload me-1"></i>Subir foto en su lugar
                            <input type="file" id="face-file-input" accept="image/*" style="display:none;">
                        </label>
                    </div>
                </div>

                
                <div id="face-result-view" style="display:none;">
                    <div class="text-center">
                        <div id="face-result-content"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" id="face-modal-footer" style="background: #f8f9fa;">
                <button class="btn btn-outline-secondary btn-sm" id="btn-skip-face">
                    <i class="bi bi-skip-forward me-1"></i>Omitir análisis
                </button>
                <button class="btn btn-gold px-4" id="btn-capture-face">
                    <i class="bi bi-camera me-1"></i>Capturar y analizar
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php if(auth()->guard()->check()): ?>
<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/mediapipe-face.js')); ?>?v=<?php echo e(filemtime(public_path('js/mediapipe-face.js'))); ?>"></script>
<script src="<?php echo e(asset('js/asesor-virtual.js')); ?>?v=<?php echo e(filemtime(public_path('js/asesor-virtual.js'))); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Verificar soporte de cámara al cargar
    try {
        const support = await FaceAnalyzer.checkCameraSupport();
        if (!support.supported) {
            sessionStorage.setItem('asesor_camera_blocked', JSON.stringify(support.errors));
        }
    } catch (e) {
        // ignorar
    }

    window.asesor = new AsesorVirtual({
        chatContainerId: 'chat-messages',
        inputId: 'chat-input',
        sendButtonId: 'chat-send',
        recommendButtonId: 'btn-recommend',
        baseUrl: '/api/asesor'
    });

    document.getElementById('btn-nueva-asesoria').addEventListener('click', function() {
        Object.keys(sessionStorage)
            .filter(k => k.startsWith('asesor_'))
            .forEach(k => sessionStorage.removeItem(k));
        location.reload();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/asesor/index.blade.php ENDPATH**/ ?>