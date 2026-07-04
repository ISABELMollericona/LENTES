@extends('layouts.admin')

@section('title', 'Gestionar Lentes')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center page-header gap-2">
    <div>
        <h1><i class="bi bi-eyeglasses me-2"></i>Gestionar Lentes</h1>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.lentes.create') }}" class="btn btn-admin-gold">
            <i class="bi bi-plus-lg me-1"></i>Nuevo lente
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.lentes.index') }}" class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, código o marca..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="estado">
                    <option value="">Todos</option>
                    <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="vendido" {{ request('estado') == 'vendido' ? 'selected' : '' }}>Vendido</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="genero">
                    <option value="">Todos</option>
                    <option value="hombre" {{ request('genero') == 'hombre' ? 'selected' : '' }}>Hombre</option>
                    <option value="mujer" {{ request('genero') == 'mujer' ? 'selected' : '' }}>Mujer</option>
                    <option value="unisex" {{ request('genero') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                @if(count(request()->query()) > 0)
                <a href="{{ route('admin.lentes.index') }}" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                @endif
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
                    @forelse($lentes as $lente)
                    <tr>
                        <td>
                            <img src="{{ $lente->imagen_url ?? 'https://via.placeholder.com/50' }}"
                                 style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td class="fw-semibold">{{ $lente->codigo }}</td>
                        <td>{{ $lente->nombre }}</td>
                        <td>{{ $lente->marca->nombre ?? '-' }}</td>
                        <td>{{ $lente->categoria->nombre ?? '-' }}</td>
                        <td class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($lente->precio, 2) }}</td>
                        <td>
                            <span class="badge {{ $lente->estado == 'disponible' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($lente->estado) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($lente->genero) }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.lentes.edit', $lente) }}" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('admin.lentes.show', $lente) }}" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.lentes.destroy', $lente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este lente?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-action"><i class="bi bi-trash"></i></button>
                                </form>
                                @if($lente->estado == 'disponible')
                                <form action="{{ route('admin.lentes.estado', $lente) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado" value="vendido">
                                    <button class="btn btn-sm btn-outline-warning btn-action" title="Marcar como vendido">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.lentes.estado', $lente) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado" value="disponible">
                                    <button class="btn btn-sm btn-outline-success btn-action" title="Marcar como disponible">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay lentes registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $lentes->links() }}
        </div>
    </div>
</div>
@endsection
