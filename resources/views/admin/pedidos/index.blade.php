@extends('layouts.admin')

@section('title', 'Gestionar Pedidos')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-box me-2"></i>Gestionar Pedidos</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pedidos.index') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Buscar por código, cliente o email..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                    <option value="en_preparacion" {{ request('estado') == 'en_preparacion' ? 'selected' : '' }}>En preparación</option>
                    <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-admin-gold flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                @if(count(request()->query()) > 0)
                <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-gold flex-fill">
                    <i class="bi bi-x-circle me-1"></i>Limpiar
                </a>
                @endif
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
                    @forelse($pedidos as $pedido)
                    <tr>
                        <td class="fw-semibold">{{ $pedido->codigo }}</td>
                        <td>{{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}</td>
                        <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                        <td>{{ $pedido->detallePedidos->count() }}</td>
                        <td class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($pedido->total, 2) }}</td>
                        <td>
                            <span class="badge estado-{{ $pedido->estado }}">
                                {{ str_replace('_', ' ', ucfirst($pedido->estado)) }}
                            </span>
                        </td>
                        <td>
                            @if($pedido->pago)
                            <span class="badge {{ $pedido->pago->estado == 'aprobado' ? 'bg-success' : ($pedido->pago->estado == 'rechazado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($pedido->pago->estado) }}
                            </span>
                            @else
                            <span class="badge bg-secondary">Sin pago</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-gold btn-action">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-gold btn-action dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(['pendiente', 'confirmado', 'en_preparacion', 'entregado', 'cancelado'] as $estado)
                                        <li>
                                            <form action="{{ route('admin.pedidos.estado', $pedido) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="{{ $estado }}">
                                                <button class="dropdown-item {{ $pedido->estado == $estado ? 'active' : '' }}" type="submit">
                                                    <i class="bi {{ $estado == 'entregado' ? 'bi-check-circle text-success' : ($estado == 'cancelado' ? 'bi-x-circle text-danger' : 'bi-circle') }} me-2"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                                </button>
                                            </form>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No hay pedidos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">
            {{ $pedidos->links() }}
        </div>
    </div>
</div>
@endsection
