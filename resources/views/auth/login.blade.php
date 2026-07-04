@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm" style="border-top: 3px solid #D4AF37;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-eyeglasses" style="font-size: 3rem; color: #D4AF37;"></i>
                        <h3 class="fw-bold mt-2">Óptica Golden</h3>
                        <p class="text-muted">Inicia sesión en tu cuenta</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </button>

                        <div class="text-center mb-3">
                            <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #D4AF37;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <hr class="my-3">
                        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100 py-2 mb-3 d-flex align-items-center justify-content-center gap-2">
                            <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.78l7.98-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                            Iniciar sesión con Google
                        </a>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="text-muted">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #D4AF37;">Regístrate aquí</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
