@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-key" style="font-size: 3rem; color: #D4AF37;"></i>
                        <h3 class="fw-bold mt-2">Recuperar Contraseña</h3>
                        <p class="text-muted">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña</p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2">
                            <i class="bi bi-send me-2"></i>Enviar enlace
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none" style="color: #D4AF37;">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
