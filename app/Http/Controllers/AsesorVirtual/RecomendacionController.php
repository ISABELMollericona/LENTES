<?php

namespace App\Http\Controllers\AsesorVirtual;

use App\Http\Controllers\Controller;
use App\Models\Recomendacion;

class RecomendacionController extends Controller
{
    public function resultados(Recomendacion $recomendacion)
    {
        if ($recomendacion->usuario_id !== auth()->id()) {
            abort(403);
        }

        $recomendacion->load(['detalles.lente.categoria', 'detalles.lente.marca']);

        return view('asesor.resultados', compact('recomendacion'));
    }
}
