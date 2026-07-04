<nav class="navbar navbar-dark px-3" style="background: #1a1a1a; border-bottom: 1px solid #333;">
    <div class="d-flex align-items-center">
        <button class="btn btn-sm btn-outline-gold me-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="text-white-50 small">
            <i class="bi bi-calendar me-1"></i>{{ now()->format('d/m/Y') }}
        </span>
    </div>

    <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle text-white-50" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1" style="color: #D4AF37;"></i>
            {{ auth()->user()->nombre }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Mi perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
