@extends('layouts.admin')

@section('title', 'Detalle de Lente')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="bi bi-eye me-2"></i>Detalle del Lente</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.lentes.edit', $lente) }}" class="btn btn-admin-gold">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <a href="{{ route('admin.lentes.index') }}" class="btn btn-outline-gold">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <img src="{{ $lente->imagen_url ?? 'https://via.placeholder.com/400x300?text=Sin+Imagen' }}"
                 class="card-img-top" alt="{{ $lente->nombre }}" style="height: 300px; object-fit: contain;">
            @if($lente->imagenes->count() > 0)
            <div class="card-body">
                <h6 class="fw-bold">Galería</h6>
                <div class="d-flex gap-2 overflow-auto">
                    @foreach($lente->imagenes as $img)
                    <img src="{{ $img->url_completa }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer;">
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="fw-bold">{{ $lente->nombre }}</h3>
                <p class="text-muted">{{ $lente->codigo }} | {{ $lente->marca->nombre ?? '-' }}</p>

                <div class="mb-3">
                    <span class="badge fs-6 px-3 py-2 {{ $lente->estado == 'disponible' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($lente->estado) }}
                    </span>
                    <span class="badge fs-6 px-3 py-2 bg-secondary ms-1">{{ ucfirst($lente->genero) }}</span>
                </div>

                <div class="row g-2">
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Categoría</small>
                        <div class="fw-semibold">{{ $lente->categoria->nombre ?? '-' }}</div>
                    </div>
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Tipo de lente</small>
                        <div class="fw-semibold">{{ ucfirst($lente->tipo_lente) }}</div>
                    </div>
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Montura</small>
                        <div class="fw-semibold">{{ str_replace('_', ' ', ucfirst($lente->tipo_montura)) }}</div>
                    </div>
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Material</small>
                        <div class="fw-semibold">{{ $lente->material ?? '-' }}</div>
                    </div>
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Color</small>
                        <div class="fw-semibold">{{ $lente->color ?? '-' }}</div>
                    </div>
                    <div class="col-6 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">Precio</small>
                        <div class="fw-bold fs-5" style="color: #D4AF37;">Bs {{ number_format($lente->precio, 2) }}</div>
                    </div>
                </div>

                @if($lente->descripcion)
                <div class="mt-3">
                    <h6 class="fw-bold">Descripción</h6>
                    <p class="text-muted">{{ $lente->descripcion }}</p>
                </div>
                @endif

                <div class="mt-3 text-muted small">
                    <i class="bi bi-calendar me-1"></i> Registrado: {{ $lente->fecha_registro->format('d/m/Y') }}
                    @if($lente->created_at)
                    | <i class="bi bi-clock me-1"></i> Creado: {{ $lente->created_at->format('d/m/Y H:i') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
