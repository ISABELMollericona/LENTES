<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLenteDisponible
{
    public function handle(Request $request, Closure $next): Response
    {
        $lenteId = $request->route('lente') ?? $request->input('lente_id');

        if ($lenteId) {
            $lente = \App\Models\Lente::find($lenteId);
            if (!$lente || !$lente->estaDisponible()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Este lente no está disponible para la venta.'
                    ], 409);
                }
                return redirect()->back()->with('error', 'Este lente no está disponible.');
            }
        }

        return $next($request);
    }
}
