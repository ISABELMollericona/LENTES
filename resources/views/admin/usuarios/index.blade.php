@extends('layouts.admin')

@section('title', 'Usuarios')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Gestionar Usuarios</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, apellido o email..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="suspendido" {{ request('estado') == 'suspendido' ? 'selected' : '' }}>Suspendidos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                @if(count(request()->query()) > 0)
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                @endif
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
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td class="fw-semibold">{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->telefono ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $usuario->esAdmin() ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                {{ $usuario->role->nombre ?? ($usuario->esAdmin() ? 'Admin' : 'Cliente') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $usuario->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($usuario->estado) }}
                            </span>
                        </td>
                        <td>{{ $usuario->pedidos->count() }}</td>
                        <td><small>{{ $usuario->created_at->format('d/m/Y') }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($usuario->estado == 'activo')
                                <form action="{{ route('admin.usuarios.suspender', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning btn-action" onclick="return confirm('¿Suspender a {{ $usuario->nombre }}?')">
                                        <i class="bi bi-pause-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.usuarios.activar', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success btn-action">
                                        <i class="bi bi-play-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay usuarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection
