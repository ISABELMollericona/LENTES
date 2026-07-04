@extends('layouts.app')

@section('title', 'Pago QR - ' . $pedido->codigo)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <h3 class="fw-bold mb-0"><i class="bi bi-qr-code me-2" style="color:#D4AF37;"></i>Pagar con QR</h3>
        <span class="badge badge-gold fs-6 px-3 py-2">{{ $pedido->codigo }}</span>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 justify-content-center">

        {{-- QR --}}
        <div class="col-lg-5">
            <div class="card shadow-sm text-center" style="border-top: 3px solid #D4AF37;">
                <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                    <i class="bi bi-qr-code me-1"></i> Escanea para pagar
                </div>
                <div class="card-body py-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="p-3 rounded" style="background:#fff; border: 3px solid #D4AF37; display:inline-block;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('OPTICA GOLDEN | Pedido: ' . $pedido->codigo . ' | Total: Bs ' . number_format($pedido->total, 2)) }}"
                                 alt="QR de pago"
                                 style="width:200px; height:200px; display:block;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="fw-bold fs-4 mb-0" style="color:#D4AF37;">
                            Bs {{ number_format($pedido->total, 2) }}
                        </p>
                        <small class="text-muted">Monto a pagar</small>
                    </div>

                    <div class="alert p-3 mb-3" style="background:rgba(212,175,55,0.10); border:1px solid rgba(212,175,55,0.35); border-radius:8px; text-align:left;">
                        <p class="fw-semibold mb-2" style="color:#D4AF37;"><i class="bi bi-info-circle me-1"></i>Instrucciones:</p>
                        <ol class="mb-0 small text-muted ps-3">
                            <li>Abre tu aplicación bancaria o de pagos.</li>
                            <li>Selecciona la opción <strong>Pago por QR</strong>.</li>
                            <li>Escanea el código de arriba.</li>
                            <li>Confirma el monto: <strong>Bs {{ number_format($pedido->total, 2) }}</strong></li>
                            <li>Haz clic en <strong>"Confirmar pago"</strong> abajo.</li>
                        </ol>
                    </div>

                    <form method="POST" action="{{ route('pagos.store', $pedido) }}">
                        @csrf
                        <button type="submit" class="btn btn-gold w-100 py-2 fw-semibold fs-5">
                            <i class="bi bi-check-circle me-2"></i>Confirmar pago — Bs {{ number_format($pedido->total, 2) }}
                        </button>
                    </form>

                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                        Pagar más tarde
                    </a>
                </div>
            </div>
        </div>

        {{-- Resumen del pedido --}}
        <div class="col-lg-5">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                    <i class="bi bi-receipt me-1"></i> Detalle del pedido
                </div>
                <div class="card-body">
                    @foreach($pedido->detallePedidos as $detalle)
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom:1px solid #f0f0f0;">
                        <img src="{{ $detalle->lente->imagen_url }}"
                             alt="{{ $detalle->lente->nombre }}"
                             style="width:56px; height:44px; object-fit:cover; border-radius:6px;">
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $detalle->lente->nombre }}</div>
                            <small class="text-muted">{{ $detalle->lente->marca->nombre ?? '' }}</small>
                            @if($detalle->lente->tipo_lente === 'sol')
                                <br><span class="badge" style="background:rgba(139,94,0,0.15); color:#8B5E00; font-size:0.65rem;">Sol</span>
                            @else
                                <br><span class="badge" style="background:rgba(26,74,46,0.15); color:#1a4a2e; font-size:0.65rem;">Óptico</span>
                            @endif
                        </div>
                        <div class="fw-bold" style="color:#D4AF37;">Bs {{ number_format($detalle->precio_unitario, 2) }}</div>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between fw-bold fs-5 mt-2">
                        <span>Total</span>
                        <span style="color:#D4AF37;">Bs {{ number_format($pedido->total, 2) }}</span>
                    </div>

                    @if($pedido->direccion_entrega)
                    <div class="mt-3 p-2 rounded small" style="background:#f8f9fa; border-left:3px solid #D4AF37;">
                        <i class="bi bi-geo-alt me-1" style="color:#D4AF37;"></i>
                        <strong>Entrega:</strong> {{ $pedido->direccion_entrega }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
