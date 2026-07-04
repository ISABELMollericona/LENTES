<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['rol_id'] = 2;

        $user = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Registro exitoso', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        if ($user->estado === 'suspendido') {
            return $this->error('Tu cuenta ha sido suspendida.', 403);
        }

        $user->update(['ultimo_acceso' => now()]);

        $token = $user->createToken('auth_token', [
            $user->esAdmin() ? 'admin' : 'cliente'
        ])->plainTextToken;

        return $this->success([
            'user' => $user->load('role'),
            'token' => $token,
            'role' => $user->esAdmin() ? 'admin' : 'cliente',
        ], 'Inicio de sesión exitoso');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Sesión cerrada exitosamente');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user()->load('role'));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $user->update($validated);

        return $this->success($user, 'Perfil actualizado');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error('La contraseña actual es incorrecta.', 422);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return $this->success(null, 'Contraseña actualizada exitosamente');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Enlace de recuperación enviado a tu correo.');
        }

        return $this->error('No pudimos enviar el enlace de recuperación.', 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, 'Contraseña restablecida exitosamente.');
        }

        return $this->error('Token de restablecimiento inválido.', 400);
    }
}
