@extends('layouts.app')

@section('title', 'Datos de entrega y medidas')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.75rem;">
        <h3 class="fw-bold mb-0"><i class="bi bi-clipboard2-check me-2" style="color:#D4AF37;"></i>Datos de entrega</h3>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('carrito.procesarCheckout') }}">
        @csrf
        <div class="row g-4">

            {{-- COLUMNA IZQUIERDA: dirección + medidas --}}
            <div class="col-lg-8">

                {{-- DIRECCIÓN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                        <i class="bi bi-geo-alt me-1"></i> Dirección de entrega
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dirección completa <span class="text-danger">*</span></label>
                            <textarea name="direccion_entrega" class="form-control @error('direccion_entrega') is-invalid @enderror"
                                rows="3" placeholder="Ej: Calle Sucre #452, Zona Central, Edificio Dorado Piso 3, Ref: frente a la plaza, La Paz"
                                required>{{ old('direccion_entrega') }}</textarea>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Incluye calle, número, zona, ciudad y una referencia.
                            </div>
                            @error('direccion_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- MEDIDAS POR LENTE --}}
                @foreach($items as $item)
                @php $lente = $item->lente; $esSol = $lente->tipo_lente === 'sol'; @endphp
                <div class="card shadow-sm mb-3" style="border-left: 3px solid #D4AF37;">
                    <div class="card-header fw-semibold d-flex align-items-center gap-2" style="background:#111; color:#D4AF37;">
                        <i class="bi bi-{{ $esSol ? 'sun' : 'eyeglasses' }} me-1"></i>
                        Medidas para: <span class="text-white">{{ $lente->nombre }}</span>
                        <span class="badge ms-auto" style="background:{{ $esSol ? '#8B5E00' : '#1a4a2e' }}; font-size:0.7rem;">
                            {{ $esSol ? 'Lente de sol' : 'Lente óptico' }}
                        </span>
                    </div>
                    <div class="card-body p-4">

                        {{-- Distancia pupilar (para ambos tipos) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-arrows-expand me-1" style="color:#D4AF37;"></i>
                                Distancia Pupilar (DP) <small class="text-muted fw-normal">en mm</small>
                            </label>
                            <input type="number" step="0.5" min="40" max="80"
                                name="medidas[{{ $lente->id }}][dp]"
                                class="form-control" style="max-width:160px;"
                                placeholder="Ej: 62.5"
                                value="{{ old('medidas.'.$lente->id.'.dp') }}">
                            <div class="form-text text-muted">Es la distancia entre el centro de cada pupila.</div>
                        </div>

                        @if(!$esSol)
                        {{-- GRADUACIÓN / MEDIDAS ÓPTICAS --}}
                        <p class="fw-semibold mb-3" style="color:#D4AF37;">
                            <i class="bi bi-prescription me-1"></i>Graduación (Rx)
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" style="font-size:0.9rem;">
                                <thead style="background:#1a1a1a; color:#D4AF37;">
                                    <tr>
                                        <th style="width:110px;">Ojo</th>
                                        <th>Esfera</th>
                                        <th>Cilindro</th>
                                        <th>Eje (°)</th>
                                        <th>Adición</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold"><i class="bi bi-circle me-1"></i>OD (Derecho)</td>
                                        <td>
                                            <input type="number" step="0.25" min="-20" max="20"
                                                name="medidas[{{ $lente->id }}][od_esfera]"
                                                class="form-control form-control-sm" placeholder="Ej: -1.50"
                                                value="{{ old('medidas.'.$lente->id.'.od_esfera') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="-10" max="10"
                                                name="medidas[{{ $lente->id }}][od_cilindro]"
                                                class="form-control form-control-sm" placeholder="Ej: -0.50"
                                                value="{{ old('medidas.'.$lente->id.'.od_cilindro') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="0" max="180"
                                                name="medidas[{{ $lente->id }}][od_eje]"
                                                class="form-control form-control-sm" placeholder="Ej: 90"
                                                value="{{ old('medidas.'.$lente->id.'.od_eje') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="0" max="4"
                                                name="medidas[{{ $lente->id }}][od_adicion]"
                                                class="form-control form-control-sm" placeholder="Ej: 1.00"
                                                value="{{ old('medidas.'.$lente->id.'.od_adicion') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold"><i class="bi bi-circle me-1"></i>OI (Izquierdo)</td>
                                        <td>
                                            <input type="number" step="0.25" min="-20" max="20"
                                                name="medidas[{{ $lente->id }}][oi_esfera]"
                                                class="form-control form-control-sm" placeholder="Ej: -1.25"
                                                value="{{ old('medidas.'.$lente->id.'.oi_esfera') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="-10" max="10"
                                                name="medidas[{{ $lente->id }}][oi_cilindro]"
                                                class="form-control form-control-sm" placeholder="Ej: -0.75"
                                                value="{{ old('medidas.'.$lente->id.'.oi_cilindro') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="0" max="180"
                                                name="medidas[{{ $lente->id }}][oi_eje]"
                                                class="form-control form-control-sm" placeholder="Ej: 85"
                                                value="{{ old('medidas.'.$lente->id.'.oi_eje') }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.25" min="0" max="4"
                                                name="medidas[{{ $lente->id }}][oi_adicion]"
                                                class="form-control form-control-sm" placeholder="Ej: 1.00"
                                                value="{{ old('medidas.'.$lente->id.'.oi_adicion') }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-text text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Puedes dejar en blanco los campos que no aplican a tu receta. La adición se usa solo para bifocal/progresivo.
                        </div>
                        @endif

                    </div>
                </div>
                @endforeach

            </div>

            {{-- COLUMNA DERECHA: resumen --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="border-top: 3px solid #D4AF37; top: 20px;">
                    <div class="card-header fw-semibold" style="background:#1a1a1a; color:#D4AF37;">
                        <i class="bi bi-receipt me-1"></i> Resumen del pedido
                    </div>
                    <div class="card-body">
                        @foreach($items as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $item->lente->imagen_url }}"
                                    alt="{{ $item->lente->nombre }}"
                                    style="width:40px; height:32px; object-fit:cover; border-radius:4px;">
                                <span>{{ $item->lente->nombre }}</span>
                            </div>
                            <span class="fw-semibold">Bs {{ number_format($item->lente->precio, 2) }}</span>
                        </div>
                        @endforeach

                        <hr style="border-color:#D4AF37;">

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                            <span>Total</span>
                            <span style="color:#D4AF37;">Bs {{ number_format($total, 2) }}</span>
                        </div>

                        <div class="alert mb-3 p-2 text-center" style="background:rgba(212,175,55,0.10); border:1px solid rgba(212,175,55,0.35); border-radius:8px;">
                            <i class="bi bi-qr-code me-1" style="color:#D4AF37;"></i>
                            <small class="fw-semibold" style="color:#D4AF37;">Pago por QR</small><br>
                            <small class="text-muted">Recibirás el QR en el siguiente paso</small>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2 fw-semibold">
                            <i class="bi bi-arrow-right-circle me-2"></i>Continuar al pago QR
                        </button>
                        <a href="{{ route('carrito.index') }}" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Volver al carrito
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
