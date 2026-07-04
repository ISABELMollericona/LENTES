@extends('layouts.app')

@section('title', 'Mis Recomendaciones')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <div>
            <h3 class="fw-bold mb-0"><i class="bi bi-stars me-2" style="color: #D4AF37;"></i>Tus Lentes Recomendados</h3>
            <p class="text-muted mb-0 small">Basado en tus preferencias y análisis facial</p>
        </div>
        <a href="{{ route('asesor.index') }}" class="btn btn-outline-gold">
            <i class="bi bi-arrow-left me-1"></i>Nueva asesoría
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @if($recomendacion->detalles->count() > 0)
                @foreach($recomendacion->detalles as $detalle)
                <div class="card shadow-sm mb-3 recommendation-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="me-3 text-center" style="min-width: 60px;">
                                <div class="fw-bold fs-4" style="color: #D4AF37;">#{{ $loop->iteration }}</div>
                                <div class="progress mt-1" style="height: 6px; width: 60px;">
                                    <div class="progress-bar" style="background: #D4AF37; width: {{ $detalle->compatibilidad }}%;"></div>
                                </div>
                                <small class="fw-semibold" style="color: #D4AF37;">{{ $detalle->compatibilidad }}%</small>
                            </div>

                            <img src="{{ $detalle->lente->imagen_url ?? 'https://via.placeholder.com/100' }}"
                                 alt="{{ $detalle->lente->nombre }}" class="me-3 rounded"
                                 style="width: 100px; height: 80px; object-fit: cover;">

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted text-uppercase">{{ $detalle->lente->marca->nombre ?? '' }}</small>
                                        <h5 class="fw-bold mb-1">{{ $detalle->lente->nombre }}</h5>
                                        <p class="mb-1 small text-muted">
                                            <i class="bi bi-bag me-1"></i>{{ $detalle->lente->categoria->nombre ?? '' }} |
                                            <i class="bi bi-person me-1"></i>{{ ucfirst($detalle->lente->genero) }} |
                                            <i class="bi bi-box me-1"></i>{{ str_replace('_', ' ', ucfirst($detalle->lente->tipo_montura)) }}
                                        </p>
                                        <span class="fw-bold fs-5" style="color: #D4AF37;">Bs {{ number_format($detalle->lente->precio, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="badge {{ $detalle->lente->estado == 'disponible' ? 'badge-disponible' : 'badge-vendido' }}">
                                            {{ ucfirst($detalle->lente->estado) }}
                                        </span>
                                    </div>
                                </div>

                                <p class="mt-2 mb-0 p-2 rounded" style="background: #f8f9fa; border-left: 3px solid #D4AF37;">
                                    <small><i class="bi bi-chat-quote me-1" style="color: #D4AF37;"></i>{{ $detalle->justificacion }}</small>
                                </p>

                                @if($detalle->lente->estado == 'disponible')
                                <div class="mt-2">
                                    <form action="{{ route('carrito.agregar', $detalle->lente) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-gold">
                                            <i class="bi bi-cart-plus me-1"></i>Agregar al carrito
                                        </button>
                                    </form>
                                    <a href="{{ route('catalogo.show', $detalle->lente) }}" class="btn btn-sm btn-outline-gold ms-1">
                                        <i class="bi bi-eye me-1"></i>Ver detalle
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #D4AF37;"></i>
                    <h4 class="mt-3 fw-bold">No se encontraron recomendaciones</h4>
                    <p class="text-muted">Intenta con otras preferencias</p>
                    <a href="{{ route('asesor.index') }}" class="btn btn-gold">
                        <i class="bi bi-arrow-repeat me-1"></i>Volver al asesor
                    </a>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600;">
                    <i class="bi bi-sliders me-1"></i> Preferencias aplicadas
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Uso</td>
                            <td class="fw-semibold text-end">{{ ucfirst(str_replace('_', ' ', $recomendacion->uso_lentes)) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estilo</td>
                            <td class="fw-semibold text-end">{{ ucfirst($recomendacion->estilo) }}</td>
                        </tr>
                        @if($recomendacion->presupuesto_max)
                        <tr>
                            <td class="text-muted">Presupuesto</td>
                            <td class="fw-semibold text-end">Bs {{ number_format($recomendacion->presupuesto_max, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Montura</td>
                            <td class="fw-semibold text-end">{{ str_replace('_', ' ', ucfirst($recomendacion->tipo_montura)) }}</td>
                        </tr>
                        @if($recomendacion->color_favorito)
                        <tr>
                            <td class="text-muted">Color</td>
                            <td class="fw-semibold text-end">{{ ucfirst($recomendacion->color_favorito) }}</td>
                        </tr>
                        @endif
                        @if($recomendacion->forma_rostro)
                        <tr>
                            <td class="text-muted">Forma de rostro</td>
                            <td class="fw-semibold text-end">{{ ucfirst($recomendacion->forma_rostro) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow-sm mt-3" style="border-top: 3px solid #D4AF37;">
                <div class="card-body text-center">
                    <i class="bi bi-cart-check" style="font-size: 2.5rem; color: #D4AF37;"></i>
                    <h6 class="fw-bold mt-2">¿Listo para comprar?</h6>
                    <p class="small text-muted">Los lentes que te recomendamos están disponibles</p>
                    <a href="{{ route('carrito.index') }}" class="btn btn-gold w-100">
                        <i class="bi bi-cart3 me-1"></i>Ver mi carrito
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
