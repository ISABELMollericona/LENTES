<?php

return [
    'default' => env('AI_PROVIDER', 'gemini'),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 500),
        'timeout' => env('GEMINI_TIMEOUT', 15),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'temperature' => env('GROQ_TEMPERATURE', 0.7),
        'max_tokens' => env('GROQ_MAX_TOKENS', 500),
        'timeout' => env('GROQ_TIMEOUT', 15),
    ],

    'fallback' => [
        'enabled' => env('AI_FALLBACK_ENABLED', true),
        'messages' => [
            'default' => '¿Podrías darme más detalles sobre lo que buscas?',
            'error' => 'Lo siento, tengo problemas para procesar tu solicitud. ¿Podrías intentarlo de nuevo?',
        ],
    ],
];
