@extends('layouts.admin')

@section('title', 'Detalle de Pedido')

@section('content')
<div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
    <h1><i class="bi bi-receipt me-2"></i>Pedido {{ $pedido->codigo }}</h1>
    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-gold">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                <h5 class="mb-0"><i class="bi bi-box me-2"></i>Items del pedido</h5>
                <span class="badge px-3 py-2 estado-{{ $pedido->estado }}">
                    {{ str_replace('_', ' ', ucfirst($pedido->estado)) }}
                </span>
            </div>
            <div class="card-body">
                @foreach($pedido->detallePedidos as $detalle)
                <div class="d-flex align-items-center p-3 mb-2 rounded" style="background: #f8f9fa;">
                    <img src="{{ $detalle->lente->imagen_url ?? 'https://via.placeholder.com/60' }}"
                         class="me-3 rounded" style="width: 60px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <small class="text-muted">{{ $detalle->lente->codigo }}</small>
                        <h6 class="mb-0 fw-semibold">{{ $detalle->lente->nombre ?? 'Lente' }}</h6>
                        <small class="text-muted">{{ $detalle->lente->marca->nombre ?? '' }} | {{ $detalle->lente->color ?? '' }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($detalle->precio_unitario, 2) }}</div>
                        <span class="badge {{ $detalle->lente->estado == 'disponible' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($detalle->lente->estado) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
            <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                <i class="bi bi-person me-1"></i> Cliente
            </div>
            <div class="card-body">
                <h6 class="fw-bold">{{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}</h6>
                <p class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i>{{ $pedido->usuario->email }}</p>
                @if($pedido->usuario->telefono)
                <p class="text-muted small mb-1"><i class="bi bi-telephone me-1"></i>{{ $pedido->usuario->telefono }}</p>
                @endif
                @if($pedido->usuario->direccion)
                <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $pedido->usuario->direccion }}</p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                <i class="bi bi-credit-card me-1"></i> Pago
            </div>
            <div class="card-body">
                @if($pedido->pago)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Método</span>
                    <span class="fw-semibold">{{ str_replace('_', ' ', ucfirst($pedido->pago->metodo_pago)) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Monto</span>
                    <span class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($pedido->pago->monto, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Estado</span>
                    <span class="badge {{ $pedido->pago->estado == 'aprobado' ? 'bg-success' : ($pedido->pago->estado == 'rechazado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ ucfirst($pedido->pago->estado) }}
                    </span>
                </div>
                @if($pedido->pago->fecha_pago)
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Fecha pago</span>
                    <span>{{ $pedido->pago->fecha_pago->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                @else
                <p class="text-muted text-center mb-0">Sin pago registrado</p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                <i class="bi bi-arrow-repeat me-1"></i> Cambiar estado
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @foreach(['pendiente', 'confirmado', 'en_preparacion', 'entregado', 'cancelado'] as $estado)
                    <form action="{{ route('admin.pedidos.estado', $pedido) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estado" value="{{ $estado }}">
                        <button type="submit" class="btn w-100 text-start {{ $pedido->estado == $estado ? 'btn-admin-gold' : 'btn-outline-gold' }}"
                            {{ $pedido->estado == $estado ? 'disabled' : '' }}>
                            <i class="bi {{ $estado == 'entregado' ? 'bi-check-circle' : ($estado == 'cancelado' ? 'bi-x-circle' : 'bi-circle') }} me-2"></i>
                            {{ ucfirst(str_replace('_', ' ', $estado)) }}
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
