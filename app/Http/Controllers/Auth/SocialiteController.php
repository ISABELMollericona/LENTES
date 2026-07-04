<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Error al autenticar con Google.']);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'nombre' => $googleUser->getName() ?? explode('@', $googleUser->getEmail())[0],
                'apellido' => '',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'rol_id' => 2,
                'estado' => 'activo',
                'foto' => $googleUser->getAvatar(),
            ]);
        }

        if ($user->estado === 'suspendido') {
            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta ha sido suspendida.']);
        }

        Auth::login($user);

        $user->update(['ultimo_acceso' => now()]);

        return redirect()->intended(route('home'));
    }
}
