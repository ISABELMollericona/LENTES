@extends('layouts.app')

@section('title', 'Mis Pedidos')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <h3 class="fw-bold mb-0"><i class="bi bi-box me-2" style="color: #D4AF37;"></i>Mis Pedidos</h3>
        <div class="d-flex gap-2 w-100 w-sm-auto">
            <form method="GET" action="{{ route('pedidos.index') }}" class="d-flex gap-2 w-100">
                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()" style="max-width: 160px;">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                    <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmados</option>
                    <option value="en_preparacion" {{ request('estado') == 'en_preparacion' ? 'selected' : '' }}>En preparación</option>
                    <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregados</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelados</option>
                </select>
            </form>
        </div>
    </div>

    @if($pedidos->count() > 0)
    <div class="row">
        @foreach($pedidos as $pedido)
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100" style="border-left: 3px solid {{ $pedido->estado == 'entregado' ? '#2b8a3e' : ($pedido->estado == 'cancelado' ? '#c92a2a' : '#D4AF37') }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $pedido->codigo }}</h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $pedido->fecha_pedido->format('d/m/Y') }}
                            </small>
                        </div>
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

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $pedido->detallePedidos->count() }} {{ $pedido->detallePedidos->count() == 1 ? 'lente' : 'lentes' }}</span>
                        <span class="fw-bold" style="color: #D4AF37;">Bs {{ number_format($pedido->total, 2) }}</span>
                    </div>

                    <div class="mt-2">
                        @foreach($pedido->detallePedidos as $detalle)
                        <small class="d-block text-muted">
                            <i class="bi bi-dot me-1"></i>{{ $detalle->lente->nombre ?? 'Lente' }}
                        </small>
                        @endforeach
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-gold">
                            <i class="bi bi-eye me-1"></i>Ver detalle
                        </a>
                        @if($pedido->pago && $pedido->pago->estado == 'pendiente')
                        <a href="{{ route('pagos.create', $pedido) }}" class="btn btn-sm btn-gold">
                            <i class="bi bi-credit-card me-1"></i>Pagar
                        </a>
                        @endif
                        @if($pedido->pago && $pedido->pago->estado == 'aprobado')
                        <a href="{{ route('pagos.comprobante', $pedido->pago) }}" class="btn btn-sm btn-outline-gold">
                            <i class="bi bi-file-pdf me-1"></i>Comprobante
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 5rem; color: #D4AF37;"></i>
        <h4 class="mt-3 fw-bold">No tienes pedidos aún</h4>
        <p class="text-muted">Realiza tu primera compra en nuestro catálogo</p>
        <a href="{{ route('catalogo.index') }}" class="btn btn-gold px-4">
            <i class="bi bi-grid me-2"></i>Ver catálogo
        </a>
    </div>
    @endif
</div>
@endsection
