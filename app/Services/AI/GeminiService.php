<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat(string $mensaje, array $contexto = []): string
    {
        $prompt = $this->construirPromptAsesor($mensaje, $contexto);

        $response = Http::timeout(15)->post("{$this->apiUrl}?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 500,
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini API error: ' . $response->body());
            return 'Lo siento, tengo problemas para procesar tu solicitud. ¿Podrías intentarlo de nuevo?';
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No pude generar una respuesta.';
    }

    public function generarRecomendacion(array $preferencias, array $catalogo): array
    {
        $prompt = $this->construirPromptRecomendacion($preferencias, $catalogo);

        $response = Http::timeout(20)->post("{$this->apiUrl}?key={$this->apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 800,
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini API error (recomendacion): ' . $response->body());
            return [];
        }

        $data = $response->json();
        $texto = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return $this->parsearRecomendaciones($texto);
    }

    public function explicarRecomendacion(array $lente, array $preferencias): string
    {
        $presupuesto = $preferencias['presupuesto_max'] ?? 'No especificado';
        $prompt = "Eres un asesor de óptica. Explica por qué estos lentes son ideales para el usuario:

LENTES:
- Nombre: {$lente['nombre']}
- Marca: {$lente['marca']}
- Tipo montura: {$lente['tipo_montura']}
- Color: {$lente['color']}
- Precio: \${$lente['precio']}

PREFERENCIAS DEL USUARIO:
- Uso: {$preferencias['uso_lentes']}
- Estilo: {$preferencias['estilo']}
- Presupuesto máximo: \${$presupuesto}
- Tipo montura preferido: {$preferencias['tipo_montura']}

Genera una explicación corta y persuasiva (máximo 3 oraciones) de por qué estos lentes son perfectos para él/ella.";

        $response = Http::timeout(15)->post("{$this->apiUrl}?key={$this->apiKey}", [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.5,
                'maxOutputTokens' => 200,
            ]
        ]);

        if ($response->failed()) {
            return 'Estos lentes coinciden con tus preferencias y estilo.';
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Recomendación basada en tus preferencias.';
    }

    private function construirPromptAsesor(string $mensaje, array $contexto): string
    {
        $historial = '';
        if (!empty($contexto['historial'])) {
            foreach ($contexto['historial'] as $msg) {
                $rol = $msg['tipo'] === 'usuario' ? 'Usuario' : 'Asesor';
                $historial .= "{$rol}: {$msg['mensaje']}\n";
            }
        }

        return "Eres un asesor virtual experto en óptica y lentes. Tu nombre es 'Golden Assistant'.
Ayudas a los clientes de Óptica Golden a encontrar los lentes perfectos.

REGLAS:
1. Sé amable y profesional.
2. Haz preguntas sobre: uso, presupuesto, estilo, colores, tipo de montura.
3. Ofrece la opción de análisis facial.
4. No recomiendes lentes sin conocer las preferencias.
5. Responde en español.

HISTORIAL DE CONVERSACIÓN:
{$historial}

Usuario: {$mensaje}
Asesor:";
    }

    private function construirPromptRecomendacion(array $prefs, array $catalogo): string
    {
        $catalogoStr = '';
        foreach ($catalogo as $lente) {
            $catalogoStr .= "- {$lente['codigo']}: {$lente['nombre']} | {$lente['marca']} | {$lente['tipo_montura']} | {$lente['color']} | \${$lente['precio']}\n";
        }

        $presupuesto = $prefs['presupuesto_max'] ?? 'No especificado';
        $color = $prefs['color_favorito'] ?? 'No especificado';
        $formaRostro = $prefs['forma_rostro'] ?? 'No especificada';

        return "Eres un recomendador experto de lentes. Basado en las preferencias del usuario, selecciona los 5 mejores lentes del catálogo.

PREFERENCIAS:
- Uso: {$prefs['uso_lentes']}
- Presupuesto máximo: \${$presupuesto}
- Estilo: {$prefs['estilo']}
- Color favorito: {$color}
- Tipo de montura: {$prefs['tipo_montura']}
- Forma de rostro: {$formaRostro}

CATÁLOGO DISPONIBLE:
{$catalogoStr}

Responde ÚNICAMENTE con los códigos de los lentes recomendados separados por comas. Ejemplo: LEN-001, LEN-005, LEN-010";
    }

    private function parsearRecomendaciones(string $texto): array
    {
        $codigos = array_map('trim', explode(',', $texto));
        return array_filter($codigos, fn($c) => !empty($c));
    }
}
