<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FaceAnalysis\MediaPipeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceAnalysisController extends Controller
{
    use ApiResponse;

    public function __construct(
        private MediaPipeService $mediaPipeService
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'forma_rostro' => 'nullable|string|in:ovalado,redondo,cuadrado,rectangular,corazon,diamante',
            'confianza' => 'nullable|numeric|min:0|max:100',
            'analisis_local' => 'nullable|boolean',
        ]);

        $path = $request->file('imagen')->store('analisis-facial', 'public');

        try {
            if ($request->boolean('analisis_local') && $request->filled('forma_rostro')) {
                $analisis = $this->mediaPipeService->guardarResultado(
                    $path,
                    $request->user()->id,
                    $request->input('forma_rostro'),
                    (float) $request->input('confianza', 0)
                );
            } else {
                $analisis = $this->mediaPipeService->analizar($path, $request->user()->id);
            }

            return $this->success([
                'analisis_id' => $analisis->id,
                'forma_rostro' => $analisis->forma_rostro,
                'confianza' => $analisis->confianza,
                'tiempo_procesamiento' => $analisis->tiempo_procesamiento,
                'recomendacion_montura' => $this->getRecomendacionMontura($analisis->forma_rostro),
            ], 'Análisis facial completado');
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return $this->error('Error al procesar la imagen: ' . $e->getMessage(), 500);
        }
    }

    public function resultado(int $analisis): JsonResponse
    {
        $analisis = \App\Models\AnalisisFacial::where('usuario_id', request()->user()->id)
            ->findOrFail($analisis);

        return $this->success([
            'analisis' => $analisis,
            'recomendacion_montura' => $this->getRecomendacionMontura($analisis->forma_rostro),
        ]);
    }

    private function getRecomendacionMontura(?string $formaRostro): ?array
    {
        $recomendaciones = [
            'ovalado' => [
                'monturas' => ['completa', 'semi_al_aire', 'al_aire'],
                'mensaje' => 'Tu rostro ovalado permite usar la mayoría de monturas.',
            ],
            'redondo' => [
                'monturas' => ['completa', 'semi_al_aire'],
                'mensaje' => 'Recomendamos monturas rectangulares o angulares para alargar visualmente tu rostro.',
            ],
            'cuadrado' => [
                'monturas' => ['semi_al_aire', 'al_aire'],
                'mensaje' => 'Recomendamos monturas ovaladas o redondas para suavizar tu rostro.',
            ],
            'rectangular' => [
                'monturas' => ['completa', 'semi_al_aire'],
                'mensaje' => 'Recomendamos monturas con formas redondeadas para equilibrar tu rostro.',
            ],
            'corazon' => [
                'monturas' => ['al_aire', 'semi_al_aire'],
                'mensaje' => 'Recomendamos monturas ligeras sin borde inferior para equilibrar tu frente.',
            ],
            'diamante' => [
                'monturas' => ['semi_al_aire', 'al_aire'],
                'mensaje' => 'Recomendamos monturas con detalles en la parte superior.',
            ],
        ];

        return $recomendaciones[$formaRostro] ?? null;
    }
}
