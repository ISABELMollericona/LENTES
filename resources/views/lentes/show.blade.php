@extends('layouts.app')

@section('title', $lente->nombre)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: transparent; padding: 0;">
            <li class="breadcrumb-item"><a href="{{ route('catalogo.index') }}" style="color: #D4AF37;">Catálogo</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalogo.index', ['categoria_id' => $lente->categoria_id]) }}" style="color: #D4AF37;">{{ $lente->categoria->nombre ?? 'Categoría' }}</a></li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #999;">{{ $lente->nombre }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card" style="background: linear-gradient(135deg, rgba(26,26,26,0.9), rgba(15,15,15,0.95)); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px;">
                <div class="card-body p-3">
                    <div id="productCarousel" class="carousel slide">
                        <div class="carousel-inner rounded" style="background: #0a0a0a;">
                            <div class="carousel-item active">
                                <img src="{{ $lente->imagen_url ?? 'https://via.placeholder.com/600x400?text=Sin+Imagen' }}"
                                     class="d-block w-100" alt="{{ $lente->nombre }}"
                                     style="height: 400px; object-fit: contain; padding: 20px;">
                            </div>
                            @foreach($lente->imagenes as $img)
                            <div class="carousel-item">
                                <img src="{{ $img->url_completa }}" class="d-block w-100" alt="{{ $lente->nombre }}"
                                     style="height: 400px; object-fit: contain; padding: 20px;">
                            </div>
                            @endforeach
                        </div>
                        @if($lente->imagenes->count() > 0)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                        @endif
                    </div>

                    @if($lente->imagenes->count() > 0)
                    <div class="d-flex gap-2 mt-3 flex-wrap justify-content-center">
                        @foreach($lente->imagenes as $img)
                        <img src="{{ $img->url_completa }}"
                             class="rounded"
                             style="width: 70px; height: 50px; object-fit: cover; cursor: pointer; border: 2px solid rgba(212,175,55,0.3); opacity: 0.7; transition: all 0.3s ease;"
                             onmouseover="this.style.opacity='1'; this.style.borderColor='#D4AF37';"
                             onmouseout="this.style.opacity='0.7'; this.style.borderColor='rgba(212,175,55,0.3)';"
                             onclick="document.querySelector('#productCarousel .carousel-item.active').classList.remove('active');
                                      document.querySelectorAll('#productCarousel .carousel-item')[{{ $loop->index + 1 }}].classList.add('active');">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card" style="background: linear-gradient(135deg, rgba(26,26,26,0.9), rgba(15,15,15,0.95)); border: 1px solid rgba(212,175,55,0.2); border-radius: 16px;">
                <div class="card-body p-4">
                    <small style="color: #D4AF37; letter-spacing: 2px; text-transform: uppercase; font-weight: 600;">{{ $lente->marca->nombre ?? '' }}</small>
                    <h2 class="fw-bold mt-1" style="color: #fff;">{{ $lente->nombre }}</h2>

                    <div class="my-3">
                        <span class="badge fs-6 px-3 py-2" style="background: linear-gradient(135deg, #2b8a3e, #1f6230); color: #fff;">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ ucfirst($lente->estado) }}
                        </span>
                    </div>

                    <h3 style="color: #D4AF37; font-weight: 700; font-size: 2rem;">Bs {{ number_format($lente->precio, 2) }}</h3>

                    <div class="row g-2 mt-4 mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Código</small>
                                <div style="color: #fff; font-weight: 600;">{{ $lente->codigo }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Categoría</small>
                                <div style="color: #fff; font-weight: 600;">{{ $lente->categoria->nombre ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Tipo de lente</small>
                                <div style="color: #fff; font-weight: 600;">{{ ucfirst($lente->tipo_lente) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Montura</small>
                                <div style="color: #fff; font-weight: 600;">{{ str_replace('_', ' ', ucfirst($lente->tipo_montura)) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Material</small>
                                <div style="color: #fff; font-weight: 600;">{{ $lente->material ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Color</small>
                                <div style="color: #fff; font-weight: 600;">{{ $lente->color ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Género</small>
                                <div style="color: #fff; font-weight: 600;">{{ ucfirst($lente->genero) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.1);">
                                <small style="color: #999;">Marca</small>
                                <div style="color: #fff; font-weight: 600;">{{ $lente->marca->nombre ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($lente->descripcion)
                    <div class="mb-4">
                        <h6 style="color: #D4AF37; font-weight: 700;">Descripción</h6>
                        <p style="color: #b0b0b0;">{{ $lente->descripcion }}</p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($lente->estado == 'disponible')
                            @auth
                            <form action="{{ route('carrito.agregar', $lente) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-gold w-100 py-2 fs-5">
                                    <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
                                </button>
                            </form>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-gold w-100 py-2 fs-5">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Inicia sesión para comprar
                            </a>
                            @endauth
                        @else
                        <button class="btn w-100 py-2 fs-5" disabled style="background: #333; color: #999; border: none;">
                            <i class="bi bi-x-circle me-2"></i>Lente vendido
                        </button>
                        @endif

                        <a href="{{ route('asesor.index') }}" class="btn btn-outline-gold w-100 py-2">
                            <i class="bi bi-robot me-2"></i>Preguntar al asesor virtual
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
