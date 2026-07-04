<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'pedidos']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function suspender(User $usuario)
    {
        $usuario->update(['estado' => 'suspendido']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario suspendido.');
    }

    public function activar(User $usuario)
    {
        $usuario->update(['estado' => 'activo']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario activado.');
    }
}
