<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatIA;
use App\Services\AI\AIProviderInterface;
use App\Services\RecommendationEngine;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AsesorController extends Controller
{
    use ApiResponse;

    private AIProviderInterface $aiProvider;

    public function __construct(
        private RecommendationEngine $recommendationEngine
    ) {
        $this->aiProvider = app(AIProviderInterface::class);
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mensaje' => 'required|string|max:1000',
            'sesion_id' => 'nullable|string',
        ]);

        $sesionId = $validated['sesion_id'] ?? Str::uuid()->toString();
        $userId = $request->user()->id;

        ChatIA::create([
            'usuario_id' => $userId,
            'sesion_id' => $sesionId,
            'mensaje' => $validated['mensaje'],
            'tipo' => 'usuario',
        ]);

        $historial = ChatIA::where('sesion_id', $sesionId)
            ->where('usuario_id', $userId)
            ->orderBy('created_at')
            ->get(['mensaje', 'respuesta', 'tipo'])
            ->toArray();

        $contexto = ['historial' => $historial];
        $respuesta = $this->aiProvider->chat($validated['mensaje'], $contexto);

        ChatIA::create([
            'usuario_id' => $userId,
            'sesion_id' => $sesionId,
            'mensaje' => $respuesta,
            'tipo' => 'sistema',
        ]);

        return $this->success([
            'respuesta' => $respuesta,
            'sesion_id' => $sesionId,
        ]);
    }

    public function recomendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uso_lentes' => 'required|in:computadora,lectura,estudio,conducir,uso_diario,deportes,moda',
            'presupuesto_max' => 'nullable|numeric|min:0',
            'estilo' => 'required|in:clasico,moderno,ejecutivo,deportivo,minimalista,casual',
            'color_favorito' => 'nullable|string|max:100',
            'tipo_montura' => 'required|in:completa,semi_al_aire,al_aire',
            'forma_rostro' => 'nullable|in:ovalado,redondo,cuadrado,rectangular,corazon,diamante',
            'analisis_facial_id' => 'nullable|exists:analisis_faciales,id',
            'genero' => 'nullable|in:hombre,mujer,unisex',
        ]);

        $preferencias = array_merge($validated, [
            'usuario_id' => $request->user()->id,
        ]);

        try {
            $resultados = $this->recommendationEngine->recomendar($preferencias);

            $recomendacion = $this->recommendationEngine->guardarRecomendacion(
                $request->user()->id,
                $preferencias,
                $resultados
            );

            return $this->success([
                'recomendacion_id' => $recomendacion->id,
                'resultados' => $recomendacion->detalles->map(function ($detalle) {
                    if (!$detalle->lente) return null;
                    return [
                        'lente' => $detalle->lente->load('imagenes', 'marca', 'categoria'),
                        'compatibilidad' => $detalle->compatibilidad,
                        'justificacion' => $detalle->justificacion,
                    ];
                })->filter()->values(),
            ]);
        } catch (\Exception $e) {
            return $this->error('Error al generar recomendaciones: ' . $e->getMessage(), 500);
        }
    }

    public function resultados(int $recomendacion): JsonResponse
    {
        $recomendacion = \App\Models\Recomendacion::with('detalles.lente.imagenes', 'detalles.lente.marca', 'detalles.lente.categoria')
            ->where('usuario_id', request()->user()->id)
            ->findOrFail($recomendacion);

        return $this->success($recomendacion);
    }
}
