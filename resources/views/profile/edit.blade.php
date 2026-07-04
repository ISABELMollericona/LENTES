@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4" style="border-bottom: 2px solid #D4AF37; padding-bottom: 0.5rem;">
        <i class="bi bi-person-circle me-2" style="color: #D4AF37;"></i>Mi Perfil
    </h3>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm text-center" style="border-top: 3px solid #D4AF37;">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <i class="bi bi-person-circle" style="font-size: 5rem; color: #D4AF37;"></i>
                    </div>
                    <h5 class="fw-bold">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <span class="badge badge-gold px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>Cuenta activa
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#datos"
                                    style="color: #1a1a1a;" type="button">
                                <i class="bi bi-pencil me-1"></i>Datos personales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#password"
                                    style="color: #1a1a1a;" type="button">
                                <i class="bi bi-shield me-1"></i>Cambiar contraseña
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="datos">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre"
                                               value="{{ old('nombre', auth()->user()->nombre) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" class="form-control" name="apellido"
                                               value="{{ old('apellido', auth()->user()->apellido) }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                                    <div class="form-text">El correo no se puede modificar</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" name="telefono"
                                               value="{{ old('telefono', auth()->user()->telefono) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" name="direccion"
                                               value="{{ old('direccion', auth()->user()->direccion) }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gold px-4">
                                    <i class="bi bi-check-lg me-1"></i>Guardar cambios
                                </button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="password">
                            <form method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Contraseña actual</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nueva contraseña</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                        <div class="form-text">Mínimo 8 caracteres</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirmar nueva contraseña</label>
                                        <input type="password" class="form-control" name="new_password_confirmation" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gold px-4">
                                    <i class="bi bi-check-lg me-1"></i>Actualizar contraseña
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
