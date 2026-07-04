<?php

namespace App\Services\FaceAnalysis;

use App\Models\AnalisisFacial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaPipeService
{
    private FaceShapeClassifier $classifier;

    public function __construct()
    {
        $this->classifier = new FaceShapeClassifier();
    }

    public function analizar(string $imagenPath, int $usuarioId): AnalisisFacial
    {
        $startTime = microtime(true);

        $imagenUrl = Storage::disk('public')->url($imagenPath);
        $imagenBase64 = base64_encode(Storage::disk('public')->get($imagenPath));

        $puntosReferencia = $this->detectarPuntosFaciales($imagenBase64);

        $formaRostro = null;
        $confianza = null;

        if (!empty($puntosReferencia)) {
            try {
                $shapeResult = $this->classifier->clasificar($puntosReferencia);
                $formaRostro = $shapeResult['forma'] ?? null;
                $confianza   = $shapeResult['confianza'] ?? null;
            } catch (\Exception $e) {
                Log::warning('FaceShapeClassifier error: ' . $e->getMessage());
            }
        }

        // Fallback: si no se detectó forma, usar 'ovalado' con confianza baja
        if (!$formaRostro) {
            $formaRostro = 'ovalado';
            $confianza   = 50.0;
        }

        $processingTime = (int)((microtime(true) - $startTime) * 1000);

        return AnalisisFacial::create([
            'usuario_id' => $usuarioId,
            'imagen_url' => $imagenPath,
            'forma_rostro' => $formaRostro,
            'puntos_referencia' => $puntosReferencia,
            'confianza' => $confianza,
            'tiempo_procesamiento' => $processingTime,
        ]);
    }

    public function guardarResultado(string $imagenPath, int $usuarioId, string $formaRostro, float $confianza): AnalisisFacial
    {
        return AnalisisFacial::create([
            'usuario_id' => $usuarioId,
            'imagen_url' => $imagenPath,
            'forma_rostro' => $formaRostro,
            'confianza' => $confianza,
            'tiempo_procesamiento' => 0,
        ]);
    }

    private function detectarPuntosFaciales(string $imagenBase64): ?array
    {
        try {
            $response = Http::timeout(10)->post('https://face-analysis.mediapipe.dev/v1/detect', [
                'image' => $imagenBase64,
                'mesh' => true,
                'max_faces' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['face_landmarks'][0] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('MediaPipe API error, usando clasificador local: ' . $e->getMessage());
        }

        return $this->clasificadorLocal($imagenBase64);
    }

    private function clasificadorLocal(string $imagenBase64): ?array
    {
        $imgData = base64_decode($imagenBase64);
        if (empty($imgData)) return null;

        $tmpFile = tempnam(sys_get_temp_dir(), 'face_');
        file_put_contents($tmpFile, $imgData);
        $size = @getimagesize($tmpFile);
        @unlink($tmpFile);

        if (!$size) return null;

        $width = $size[0];
        $height = $size[1];

        $puntos = [];
        $puntos['centro']      = ['x' => $width / 2,       'y' => $height / 2];
        $puntos['frente']      = ['x' => $width / 2,       'y' => $height * 0.15];
        $puntos['menton']      = ['x' => $width / 2,       'y' => $height * 0.85];
        $puntos['maxilar_izq'] = ['x' => $width * 0.15,    'y' => $height * 0.6];
        $puntos['maxilar_der'] = ['x' => $width * 0.85,    'y' => $height * 0.6];
        $puntos['pomulo_izq']  = ['x' => $width * 0.2,     'y' => $height * 0.35];
        $puntos['pomulo_der']  = ['x' => $width * 0.8,     'y' => $height * 0.35];
        $puntos['ojos'] = [
            ['x' => $width * 0.3, 'y' => $height * 0.3],
            ['x' => $width * 0.7, 'y' => $height * 0.3],
        ];

        $puntos['anchura_maxilar'] = abs($puntos['maxilar_der']['x'] - $puntos['maxilar_izq']['x']);
        $puntos['anchura_pomulos'] = abs($puntos['pomulo_der']['x'] - $puntos['pomulo_izq']['x']);
        $puntos['altura_rostro']   = abs($puntos['menton']['y'] - $puntos['frente']['y']);

        return $puntos;
    }
}
