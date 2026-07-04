

<?php $__env->startSection('title', 'Óptica Golden - Lentes de Lujo'); ?>

<?php $__env->startSection('content'); ?>
<!-- HERO 1 - PRINCIPAL -->
<div class="hero-main-new position-relative overflow-hidden" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container-fluid position-relative z-2 px-4">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 col-12 d-flex flex-column justify-content-center">
                <p class="text-gold fw-semibold mb-4" style="font-size: 1rem; letter-spacing: 4px; text-transform: uppercase;">
                    COLECCIÓN 2025
                </p>
                <h1 class="fw-bold mb-4" style="font-size: clamp(2.5rem, 8vw, 5rem); line-height: 1.1; color: #ffffff; font-family: 'Serif', serif;">
                    Ver el <span style="color: #D4AF37; font-style: italic;">mundo</span> en <span style="color: #D4AF37;">dorado</span>.
                </h1>
                <p class="mb-5" style="font-size: clamp(1rem, 2vw, 1.3rem); color: #b0b0b0; max-width: 500px; line-height: 1.6;">
                    Descubre nuestra colección exclusiva de lentes de lujo. Diseños que trascienden la moda y definen el estilo.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo e(route('catalogo.index')); ?>" class="btn btn-gold px-5 py-3 fw-bold" style="border-radius: 8px; font-size: 1.1rem; min-width: 200px;">
                        <i class="bi bi-search me-2"></i>Explorar Catálogo
                    </a>
                    <a href="#collections" class="btn btn-outline-gold px-5 py-3 fw-bold" style="border-radius: 8px; font-size: 1.1rem; min-width: 200px;">
                        <i class="bi bi-arrow-down me-2"></i>Ver Colecciones
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-12 d-flex align-items-center justify-content-center mt-5 mt-lg-0" style="animation: fadeInRight 1s ease 0.4s both;">
                <div style="position: relative; border-radius: 24px; overflow: hidden; width: 100%; max-width: 560px; box-shadow: 0 30px 80px rgba(212, 175, 55, 0.25);">
                    <img src="<?php echo e(asset('img/home/hero.svg')); ?>"
                         alt="Modelo con gafas de lujo"
                         style="width: 100%; height: 580px; object-fit: cover; display: block;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(160deg, rgba(212,175,55,0.18) 0%, transparent 60%, rgba(0,0,0,0.4) 100%);"></div>
                    <div style="position: absolute; bottom: 24px; left: 24px; background: rgba(0,0,0,0.7); border: 1px solid rgba(212,175,55,0.5); border-radius: 10px; padding: 12px 20px; backdrop-filter: blur(8px);">
                        <p class="text-gold fw-bold mb-0" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase;">COLECCIÓN 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative dots -->
    <div style="position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); animation: float 3s ease-in-out infinite;">
        <i class="bi bi-dot" style="font-size: 30px; color: rgba(212, 175, 55, 0.3);"></i>
    </div>
</div>

<!-- SECCIÓN 2 - TU VISIÓN -->
<div class="section-divider" style="background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent); height: 2px;"></div>

<div style="background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%); min-height: 100vh; display: flex; align-items: center; padding: 80px 0;">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12 mb-5 mb-lg-0" style="animation: fadeInRight 0.8s ease 0.2s both;">
                <div style="position: relative; border-radius: 20px; overflow: hidden; border: 2px solid rgba(212, 175, 55, 0.35); box-shadow: 0 20px 60px rgba(212, 175, 55, 0.15);">
                    <img src="<?php echo e(asset('img/home/vision.svg')); ?>"
                         alt="Modelo con lentes exclusivos"
                         style="width: 100%; height: 480px; object-fit: cover; display: block;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.6) 100%);"></div>
                    <div style="position: absolute; top: 20px; right: 20px; background: rgba(212,175,55,0.9); border-radius: 8px; padding: 8px 16px;">
                        <p class="fw-bold mb-0" style="font-size: 0.75rem; letter-spacing: 2px; color: #1a1a1a; text-transform: uppercase;">EDICIÓN LIMITADA</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 ps-lg-5" style="animation: fadeInLeft 0.8s ease 0.2s both;">
                <p class="text-gold fw-semibold mb-3" style="font-size: 1rem; letter-spacing: 4px; text-transform: uppercase;">EDICIÓN LIMITADA</p>
                <h2 class="fw-bold mb-4" style="font-size: clamp(2rem, 6vw, 4rem); line-height: 1.2; color: #ffffff; font-family: 'Serif', serif;">
                    Tu visión. <span style="color: #D4AF37; font-style: italic;">Tu</span> estilo.
                </h2>
                <p style="font-size: clamp(1rem, 1.5vw, 1.2rem); color: #b0b0b0; margin-bottom: 2rem; line-height: 1.8;">
                    Lentes diseñados para quienes no siguen tendencias — las crean. Nuestras colecciones limitadas combinan precisión óptica con diseño de vanguardia.
                </p>
                <ul class="list-unstyled" style="color: #d0d0d0; font-size: 1.1rem;">
                    <li class="mb-3"><i class="bi bi-check-circle" style="color: #D4AF37; margin-right: 15px; font-size: 1.3rem;"></i>Lentes diseñados para quienes no siguen tendencias</li>
                    <li class="mb-3"><i class="bi bi-check-circle" style="color: #D4AF37; margin-right: 15px; font-size: 1.3rem;"></i>Óptica de precisión con estilo de vanguardia</li>
                    <li><i class="bi bi-check-circle" style="color: #D4AF37; margin-right: 15px; font-size: 1.3rem;"></i>Garantía de satisfacción 100%</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- COLECCIONES DESTACADAS -->
<div id="collections" style="background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%); padding: 100px 0;">
    <div class="container-fluid px-4">
        <div class="text-center mb-5">
            <p class="text-gold fw-semibold" style="font-size: 1rem; letter-spacing: 4px; text-transform: uppercase;">NUESTRA SELECCIÓN</p>
            <h2 class="fw-bold mb-3" style="font-size: clamp(2rem, 6vw, 4rem); color: #ffffff; font-family: 'Serif', serif;">Catálogo de Lentes</h2>
            <p style="font-size: 1.1rem; color: #b0b0b0; max-width: 600px; margin: 0 auto;">
                Descubre nuestras colecciones cuidadosamente seleccionadas de las mejores marcas del mundo.
            </p>
        </div>

        <div class="row g-4 mt-5">
            <!-- Colección Sol -->
            <div class="col-md-6 col-lg-4" style="animation: fadeInUp 0.6s ease 0.1s both;">
                <a href="<?php echo e(route('catalogo.index', ['categoria' => 'sol'])); ?>" class="text-decoration-none">
                    <div style="border-radius: 16px; border: 2px solid rgba(212, 175, 55, 0.2); overflow: hidden; transition: all 0.4s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(212, 175, 55, 0.25)'; this.style.borderColor='var(--gold)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='rgba(212, 175, 55, 0.2)';">
                        <div style="position: relative; height: 260px; overflow: hidden;">
                            <img src="<?php echo e(asset('img/home/sol.svg')); ?>"
                                 alt="Lentes de sol modelo"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;"
                                 onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(212,175,55,0.1) 0%, rgba(0,0,0,0.5) 100%);"></div>
                            <div style="position: absolute; top: 16px; left: 16px;"><i class="bi bi-brightness-high-fill" style="font-size: 1.8rem; color: rgba(212,175,55,0.9);"></i></div>
                        </div>
                        <div class="p-5" style="background: linear-gradient(180deg, rgba(26, 26, 26, 0.8), rgba(10, 10, 10, 0.95));">
                            <h5 class="fw-bold mb-2" style="color: #ffffff; font-size: 1.5rem;">Lentes de Sol</h5>
                            <p style="color: #b0b0b0; font-size: 1rem;">Protección UV + Estilo Premium</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Colección Ópticos -->
            <div class="col-md-6 col-lg-4" style="animation: fadeInUp 0.6s ease 0.2s both;">
                <a href="<?php echo e(route('catalogo.index', ['categoria' => 'óptico'])); ?>" class="text-decoration-none">
                    <div style="border-radius: 16px; border: 2px solid rgba(212, 175, 55, 0.2); overflow: hidden; transition: all 0.4s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(212, 175, 55, 0.25)'; this.style.borderColor='var(--gold)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='rgba(212, 175, 55, 0.2)';">
                        <div style="position: relative; height: 260px; overflow: hidden;">
                            <img src="<?php echo e(asset('img/home/opticos.svg')); ?>"
                                 alt="Modelo con lentes ópticos"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;"
                                 onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(212,175,55,0.1) 0%, rgba(0,0,0,0.5) 100%);"></div>
                            <div style="position: absolute; top: 16px; left: 16px;"><i class="bi bi-eye-fill" style="font-size: 1.8rem; color: rgba(212,175,55,0.9);"></i></div>
                        </div>
                        <div class="p-5" style="background: linear-gradient(180deg, rgba(26, 26, 26, 0.8), rgba(10, 10, 10, 0.95));">
                            <h5 class="fw-bold mb-2" style="color: #ffffff; font-size: 1.5rem;">Lentes Ópticos</h5>
                            <p style="color: #b0b0b0; font-size: 1rem;">Precisión Visual + Diseño Exclusivo</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Colección Premium -->
            <div class="col-md-6 col-lg-4" style="animation: fadeInUp 0.6s ease 0.3s both;">
                <a href="<?php echo e(route('catalogo.index')); ?>" class="text-decoration-none">
                    <div style="border-radius: 16px; border: 2px solid rgba(212, 175, 55, 0.2); overflow: hidden; transition: all 0.4s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-12px)'; this.style.boxShadow='0 20px 50px rgba(212, 175, 55, 0.25)'; this.style.borderColor='var(--gold)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='rgba(212, 175, 55, 0.2)';">
                        <div style="position: relative; height: 260px; overflow: hidden;">
                            <img src="<?php echo e(asset('img/home/premium.svg')); ?>"
                                 alt="Colección premium de gafas"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease;"
                                 onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">
                            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(212,175,55,0.1) 0%, rgba(0,0,0,0.5) 100%);"></div>
                            <div style="position: absolute; top: 16px; left: 16px;"><i class="bi bi-star-fill" style="font-size: 1.8rem; color: rgba(212,175,55,0.9);"></i></div>
                        </div>
                        <div class="p-5" style="background: linear-gradient(180deg, rgba(26, 26, 26, 0.8), rgba(10, 10, 10, 0.95));">
                            <h5 class="fw-bold mb-2" style="color: #ffffff; font-size: 1.5rem;">Colección Premium</h5>
                            <p style="color: #b0b0b0; font-size: 1rem;">Lujo y Exclusividad Total</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ACERCA DE NOSOTROS -->
<div style="background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%); padding: 100px 0;">
    <div class="container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12 mb-5 mb-lg-0" style="animation: fadeInUp 0.6s ease 0.1s both;">
                <p class="text-gold fw-semibold mb-3" style="font-size: 1rem; letter-spacing: 4px; text-transform: uppercase;">EMPRESA</p>
                <h2 class="fw-bold mb-4" style="font-size: clamp(2rem, 6vw, 4rem); color: #ffffff; font-family: 'Serif', serif;">
                    Más de 15 años
                    <br>
                    <span style="color: #D4AF37;">ofreciendo excelencia</span>
                </h2>
                <p style="font-size: 1.1rem; color: #b0b0b0; margin-bottom: 2rem; line-height: 1.8;">
                    En Óptica Golden, creemos que los lentes no son solo un accesorio — son una declaración de estilo y confianza. Durante más de 15 años, hemos seleccionado las mejores marcas mundiales para ofrecerte calidad, estilo y servicio sin igual.
                </p>
                <a href="<?php echo e(route('catalogo.index')); ?>" class="btn btn-gold px-5 py-3 fw-bold" style="border-radius: 8px; font-size: 1.1rem;">
                    <i class="bi bi-arrow-right me-2"></i>Explorar Ahora
                </a>
            </div>
            <div class="col-lg-6 col-12 ps-lg-5" style="animation: fadeInLeft 0.6s ease 0.2s both;">
                <div class="row text-center">
                    <div class="col-6 mb-5">
                        <h3 class="text-gold fw-bold" style="font-size: 3rem;">15+</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">Años de Experiencia</p>
                    </div>
                    <div class="col-6 mb-5">
                        <h3 class="text-gold fw-bold" style="font-size: 3rem;">10K+</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">Clientes Satisfechos</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-gold fw-bold" style="font-size: 3rem;">50+</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">Marcas Premium</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-gold fw-bold" style="font-size: 3rem;">100%</h3>
                        <p class="text-muted" style="font-size: 1.1rem;">Garantía</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA FINAL -->
<div style="background: linear-gradient(135deg, #D4AF37 0%, #B8962E 100%); padding: 80px 0; text-align: center; position: relative; overflow: hidden;">
    <div class="container-fluid px-4 position-relative z-2">
        <h2 class="fw-bold mb-4" style="font-size: clamp(2rem, 6vw, 3.5rem); color: #1a1a1a; font-family: 'Serif', serif;">
            ¿Listo para tu nuevo look?
        </h2>
        <p style="font-size: 1.2rem; color: #1a1a1a; max-width: 600px; margin: 0 auto 2rem; line-height: 1.6;">
            Explora nuestra colección completa y encuentra el estilo que define quién eres.
        </p>
        <a href="<?php echo e(route('catalogo.index')); ?>" class="btn btn-dark px-6 py-4 fw-bold" style="border-radius: 8px; font-size: 1.2rem;">
            <i class="bi bi-search me-2"></i>Explorar Catálogo Completo
        </a>
    </div>
</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/home.blade.php ENDPATH**/ ?>