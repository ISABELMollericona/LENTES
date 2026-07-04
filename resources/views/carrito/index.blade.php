@extends('layouts.app')

@section('title', 'Mi Carrito')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <h3 class="fw-bold mb-0"><i class="bi bi-cart3 me-2" style="color: #D4AF37;"></i>Mi Carrito</h3>
        <span class="badge badge-gold fs-6 px-3 py-2">{{ $items->count() }} {{ $items->count() == 1 ? 'item' : 'items' }}</span>
    </div>

    @if($items->count() > 0)
    <div class="row g-4">
        <div class="col-lg-8">
            @foreach($items as $item)
            <div class="card shadow-sm mb-3 scroll-reveal">
                <div class="card-body">
                    <div class="d-flex flex-sm-row flex-column align-items-start align-items-sm-center gap-3">
                        <img src="{{ $item->lente->imagen_url ?? 'https://via.placeholder.com/100' }}"
                             alt="{{ $item->lente->nombre }}" class="rounded cart-item-img"
                             style="width: 100%; max-width: 100px; height: 80px; object-fit: cover;">

                        <div class="flex-grow-1 w-100">
                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                                <div>
                                    <small class="text-muted text-uppercase">{{ $item->lente->marca->nombre ?? '' }}</small>
                                    <h5 class="fw-bold mb-1">{{ $item->lente->nombre }}</h5>
                                    <p class="small text-muted mb-0">
                                        <i class="bi bi-box me-1"></i>{{ str_replace('_', ' ', ucfirst($item->lente->tipo_montura)) }} |
                                        <i class="bi bi-palette me-1"></i>{{ $item->lente->color ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-sm-end d-flex d-sm-block justify-content-between align-items-center gap-3">
                                    <div class="fw-bold fs-5" style="color: #D4AF37;">Bs {{ number_format($item->lente->precio, 2) }}</div>
                                    <form action="{{ route('carrito.eliminar', $item) }}" method="POST" class="mt-0 mt-sm-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este lente del carrito?')">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                    <i class="bi bi-receipt me-1"></i> Resumen del pedido
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        @foreach($items as $item)
                        <tr>
                            <td class="ps-0"><small>{{ $item->lente->nombre }}</small></td>
                            <td class="text-end pe-0"><small>Bs {{ number_format($item->lente->precio, 2) }}</small></td>
                        </tr>
                        @endforeach
                    </table>

                    <hr style="border-color: #D4AF37;">

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span style="color: #D4AF37;">Bs {{ number_format($total, 2) }}</span>
                    </div>

                    <form action="{{ route('carrito.confirmar') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-gold w-100 py-2 fs-6" onclick="return confirm('¿Confirmar la compra de todos los lentes en tu carrito?')">
                            <i class="bi bi-check-circle me-2"></i>Confirmar compra
                        </button>
                    </form>

                    <a href="{{ route('catalogo.index') }}" class="btn btn-outline-gold w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Seguir comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 5rem; color: #D4AF37;"></i>
        <h4 class="mt-3 fw-bold">Tu carrito está vacío</h4>
        <p class="text-muted">Explora nuestro catálogo y agrega lentes a tu carrito</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="{{ route('catalogo.index') }}" class="btn btn-gold px-4">
                <i class="bi bi-grid me-2"></i>Ver catálogo
            </a>
            <a href="{{ route('asesor.index') }}" class="btn btn-outline-gold px-4">
                <i class="bi bi-robot me-2"></i>Asesor virtual
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
