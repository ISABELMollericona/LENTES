<nav class="navbar navbar-expand-lg navbar-dark navbar-golden sticky-top shadow-lg">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand fw-bold" href="<?php echo e(route('home')); ?>" style="color: #D4AF37 !important; font-size: 1.2rem; letter-spacing: 1px;">
            <i class="bi bi-eyeglasses me-2" style="color: #D4AF37; font-size: 1.5rem;"></i>Óptica Golden
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" style="color: #D4AF37;">
            <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 30 30%22%3E%3Cpath stroke=%22%23D4AF37%22 stroke-linecap=%22round%22 stroke-miterlimit=%2210%22 stroke-width=%222%22 d=%22M4 7h22M4 15h22M4 23h22%22/%3E%3C/svg%3E');"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('catalogo.index')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-grid me-1"></i>Catálogo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('catalogo.index', ['categoria' => 'sol'])); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-brightness-high me-1"></i>Colecciones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('catalogo.index')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-tag me-1"></i>Marcas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('asesor.index')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-robot me-1"></i>Asesor Virtual
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('facial.index')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-camera me-1"></i>Análisis Facial
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if(auth()->guard()->check()): ?>
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo e(route('carrito.index')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-cart3" style="color: #D4AF37; font-size: 1.2rem;"></i>
                        <span class="badge position-absolute top-0 start-100 translate-middle" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;" id="cart-count">
                            <?php echo e(\App\Services\CartService::contarStatic(auth()->id())); ?>

                        </span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" style="color: #d0d0d0;">
                        <i class="bi bi-person-circle me-2"></i><?php echo e(Str::limit(auth()->user()->nombre, 10)); ?>

                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" style="background: #1a1a1a; border: 1px solid rgba(212, 175, 55, 0.2);">
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>" style="color: #d0d0d0;"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('pedidos.index')); ?>" style="color: #d0d0d0;"><i class="bi bi-box me-2"></i>Mis Pedidos</a></li>
                        <?php if(auth()->user()->esAdmin()): ?>
                        <li><hr class="dropdown-divider" style="border-color: rgba(212, 175, 55, 0.2);"></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.dashboard')); ?>" style="color: #d0d0d0;"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider" style="border-color: rgba(212, 175, 55, 0.2);"></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button class="dropdown-item" type="submit" style="color: #d0d0d0;"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('login')); ?>" style="color: #d0d0d0;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-gold px-3 fw-semibold ms-2" style="border-radius: 6px; transition: all 0.3s ease;" href="<?php echo e(route('register')); ?>">Registrarse</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\MOLLERICONA\Downloads\LENTES UPDS\resources\views/partials/navbar.blade.php ENDPATH**/ ?>