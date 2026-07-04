<div class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <i class="bi bi-eyeglasses me-2"></i>Óptica Golden
        <small class="d-block" style="font-size: 0.7rem; color: #999; font-weight: 400;">Panel Admin</small>
    </a>

    <nav class="nav flex-column mt-2">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('admin.lentes.*') ? 'active' : '' }}" href="{{ route('admin.lentes.index') }}">
            <i class="bi bi-eyeglasses"></i> Lentes
        </a>
        <a class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}" href="{{ route('admin.pedidos.index') }}">
            <i class="bi bi-box"></i> Pedidos
        </a>
        <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
            <i class="bi bi-people"></i> Usuarios
        </a>
        <a class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}" href="{{ route('admin.reportes.index') }}">
            <i class="bi bi-bar-chart"></i> Reportes
        </a>

        <hr style="border-color: #333; margin: 1rem;">

        <a class="nav-link" href="{{ route('home') }}">
            <i class="bi bi-house"></i> Ver tienda
        </a>
        <a class="nav-link" href="{{ route('asesor.index') }}">
            <i class="bi bi-robot"></i> Asesor IA
        </a>

        <hr style="border-color: #333; margin: 1rem;">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="nav-link w-100 text-start border-0 bg-transparent" type="submit">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </nav>
</div>
