<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService implements AIProviderInterface
{
    private string $apiKey;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    public function chat(string $mensaje, array $contexto = []): string
    {
        $messages = $this->construirMessagesAsesor($mensaje, $contexto);

        $response = Http::timeout(15)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if ($response->failed()) {
            Log::error('Groq API error: ' . $response->body());
            return 'Lo siento, tengo problemas para procesar tu solicitud. ¿Podrías intentarlo de nuevo?';
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'No pude generar una respuesta.';
    }

    public function generarRecomendacion(array $preferencias, array $catalogo): array
    {
        $messages = $this->construirMessagesRecomendacion($preferencias, $catalogo);

        $response = Http::timeout(20)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        if ($response->failed()) {
            Log::error('Groq API error (recomendacion): ' . $response->body());
            return [];
        }

        $data = $response->json();
        $texto = $data['choices'][0]['message']['content'] ?? '';

        return $this->parsearRecomendaciones($texto);
    }

    public function explicarRecomendacion(array $lente, array $preferencias): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Eres un asesor de óptica experto. Explica recomendaciones de lentes de forma breve y persuasiva.',
            ],
            [
                'role' => 'user',
                'content' => "Explica por qué estos lentes son ideales:\n\n" .
                    "LENTES: {$lente['nombre']} ({$lente['marca']}), montura {$lente['tipo_montura']}, color {$lente['color']}, \${$lente['precio']}\n" .
                    "USUARIO: uso={$preferencias['uso_lentes']}, estilo={$preferencias['estilo']}\n" .
                    "Máximo 3 oraciones en español."
            ]
        ];

        $response = Http::timeout(15)->withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.5,
            'max_tokens' => 200,
        ]);

        if ($response->failed()) {
            return 'Estos lentes coinciden con tus preferencias y estilo.';
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'Recomendación basada en tus preferencias.';
    }

    private function construirMessagesAsesor(string $mensaje, array $contexto): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Eres "Golden Assistant", un asesor virtual experto en óptica de Óptica Golden. Ayudas a clientes a encontrar lentes ideales. Sé amable, profesional. Haz preguntas sobre uso, presupuesto, estilo, colores y tipo de montura. Ofrece análisis facial. Responde en español.',
            ]
        ];

        if (!empty($contexto['historial'])) {
            foreach ($contexto['historial'] as $msg) {
                $messages[] = [
                    'role' => $msg['tipo'] === 'usuario' ? 'user' : 'assistant',
                    'content' => $msg['mensaje'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $mensaje];
        return $messages;
    }

    private function construirMessagesRecomendacion(array $prefs, array $catalogo): array
    {
        $catalogoStr = '';
        foreach ($catalogo as $l) {
            $catalogoStr .= "- {$l['codigo']}: {$l['nombre']} | {$l['marca']} | {$l['tipo_montura']} | {$l['color']} | \${$l['precio']}\n";
        }

        $presupuesto = $prefs['presupuesto_max'] ?? 'N/A';
        $color = $prefs['color_favorito'] ?? 'N/A';
        $formaRostro = $prefs['forma_rostro'] ?? 'N/A';

        return [
            [
                'role' => 'system',
                'content' => 'Eres un recomendador de lentes. Basado en las preferencias del usuario, selecciona los 5 mejores lentes del catálogo. Responde SOLO con los códigos separados por comas.',
            ],
            [
                'role' => 'user',
                'content' => "Preferencias:\n" .
                    "- Uso: {$prefs['uso_lentes']}\n" .
                    "- Presupuesto: \${$presupuesto}\n" .
                    "- Estilo: {$prefs['estilo']}\n" .
                    "- Color favorito: {$color}\n" .
                    "- Montura: {$prefs['tipo_montura']}\n" .
                    "- Rostro: {$formaRostro}\n\n" .
                    "Catálogo:\n{$catalogoStr}"
            ]
        ];
    }

    private function parsearRecomendaciones(string $texto): array
    {
        $codigos = array_map('trim', explode(',', $texto));
        return array_filter($codigos, fn($c) => !empty($c));
    }
}
