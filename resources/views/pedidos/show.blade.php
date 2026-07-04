@extends('layouts.app')

@section('title', 'Pedido '.$pedido->codigo)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}" class="text-decoration-none" style="color: #D4AF37;">Mis Pedidos</a></li>
            <li class="breadcrumb-item active">{{ $pedido->codigo }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #1a1a1a; color: #D4AF37;">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Detalle del pedido</h5>
                    <span class="badge px-3 py-2
                        @if($pedido->estado == 'pendiente') bg-warning text-dark
                        @elseif($pedido->estado == 'confirmado') bg-primary
                        @elseif($pedido->estado == 'en_preparacion') bg-info text-dark
                        @elseif($pedido->estado == 'entregado') bg-success
                        @else bg-danger
                        @endif">
                        {{ str_replace('_', ' ', ucfirst($pedido->estado)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Código</small>
                            <span class="fw-bold">{{ $pedido->codigo }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Fecha</small>
                            <span class="fw-bold">{{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Lentes incluidos</h6>
                    @foreach($pedido->detallePedidos as $detalle)
                    <div class="d-flex align-items-center p-3 mb-2 rounded" style="background: #f8f9fa;">
                        <img src="{{ $detalle->lente->imagen_url ?? 'https://via.placeholder.com/60' }}"
                             class="me-3 rounded" style="width: 60px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <small class="text-muted">{{ $detalle->lente->marca->nombre ?? '' }}</small>
                            <h6 class="mb-0 fw-semibold">{{ $detalle->lente->nombre ?? 'Lente' }}</h6>
                            <small class="text-muted">{{ $detalle->lente->codigo }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($detalle->precio_unitario, 2) }}</div>
                        </div>
                    </div>
                    @endforeach

                    @if($pedido->observaciones)
                    <div class="mt-3 p-3 rounded" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                        <small class="fw-semibold">Observaciones:</small>
                        <p class="mb-0 small">{{ $pedido->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                    <i class="bi bi-credit-card me-1"></i> Información de pago
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>Bs {{ number_format($pedido->total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Envío</span>
                        <span class="text-success fw-semibold">Gratis</span>
                    </div>
                    <hr style="border-color: #D4AF37;">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span style="color: #D4AF37;">Bs {{ number_format($pedido->total, 2) }}</span>
                    </div>

                    @if($pedido->pago)
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Método de pago</span>
                        <span class="fw-semibold">{{ str_replace('_', ' ', ucfirst($pedido->pago->metodo_pago)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">Estado del pago</span>
                        <span class="badge {{ $pedido->pago->estado == 'aprobado' ? 'bg-success' : ($pedido->pago->estado == 'rechazado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                            {{ ucfirst($pedido->pago->estado) }}
                        </span>
                    </div>
                    @endif

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>📅 Pedido realizado</span>
                            <span>{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($pedido->pago && $pedido->pago->fecha_pago)
                        <div class="d-flex justify-content-between text-muted small">
                            <span>💳 Pagado el</span>
                            <span>{{ $pedido->pago->fecha_pago->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($pedido->pago && $pedido->pago->estado == 'aprobado')
            <div class="card shadow-sm mt-3">
                <div class="card-body text-center">
                    <a href="{{ route('pagos.comprobante', $pedido->pago) }}" class="btn btn-gold w-100">
                        <i class="bi bi-file-pdf me-2"></i>Descargar comprobante
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
