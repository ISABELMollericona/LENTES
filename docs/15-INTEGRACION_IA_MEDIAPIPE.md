# INTEGRACIÓN IA + MEDIAPIPE - Óptica Golden

## Arquitectura de Integración

```
┌─────────────────────────────────────────────────────────────────┐
│                        LARAVEL BACKEND                          │
│                                                                 │
│  ┌─────────────────┐    ┌──────────────────────────────────┐   │
│  │ AsesorController │───▶│       AIProviderInterface       │   │
│  └─────────────────┘    │  (GeminiService | GroqService)   │   │
│                         │                                  │   │
│  ┌─────────────────┐    │  - chat()                        │   │
│  │FaceAnalysisCtrl │    │  - generarRecomendacion()        │   │
│  └─────────────────┘    │  - explicarRecomendacion()       │   │
│                         └──────────────────────────────────┘   │
│  ┌─────────────────┐    ┌──────────────────────────────────┐   │
│  │ MediaPipeService│───▶│    FaceShapeClassifier           │   │
│  └─────────────────┘    │  - clasificar(puntos) → forma    │   │
│                         └──────────────────────────────────┘   │
│  ┌─────────────────┐    ┌──────────────────────────────────┐   │
│  │Recommendation   │───▶│  Reglas Forma→Montura            │   │
│  │Engine            │    │  Reglas Uso→TipoLente            │   │
│  └─────────────────┘    │  Reglas Estilo→Color             │   │
│                         └──────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## 1. Integración Gemini API

### Configuración (.env)
```env
AI_PROVIDER=gemini
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-2.0-flash
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=500
```

### Uso en Asesor Virtual
```php
// El servicio se inyecta automáticamente via Service Container
$respuesta = $aiProvider->chat($mensajeUsuario, $contextoHistorial);

// Para recomendaciones, combina IA + motor local
$recomendacionesIA = $aiProvider->generarRecomendacion($prefs, $catalogo);
$recomendacionesLocal = $recommendationEngine->recomendar($prefs);
```

### Fallback
Si Gemini no está disponible, se usa Groq como alternativa secundaria:
```php
// config/ai.php
'default' => env('AI_PROVIDER', 'gemini'),
```

## 2. Integración Groq API

### Configuración (.env)
```env
AI_PROVIDER=groq
GROQ_API_KEY=gsk_...
GROQ_MODEL=llama-3.3-70b-versatile
```

### Ventajas de Groq
- Inferencia extremadamente rápida (tokens/s muy alto)
- Modelo Llama 3.3 70B de alta calidad
- Bueno para español

## 3. Integración MediaPipe

### Frontend (JavaScript)
```javascript
// Carga dinámica del CDN de MediaPipe
const faceMesh = new FaceMesh({
    locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
    }
});

faceMesh.setOptions({
    maxNumFaces: 1,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5,
});

// 468 puntos de referencia facial
faceMesh.onResults((results) => {
    if (results.multiFaceLandmarks.length > 0) {
        const landmarks = results.multiFaceLandmarks[0];
        const keyPoints = getKeyLandmarks(landmarks);
        const shape = estimateFaceShape(keyPoints);
    }
});
```

### Backend (PHP - Fallback)
```php
// Cuando MediaPipe CDN no está disponible,
// se usa el clasificador local basado en proporciones faciales
$points = $this->detectarPuntosFaciales($imageBase64);
$shapeResult = $this->classifier->clasificar($points);
// Resultado: ['forma' => 'ovalado', 'confianza' => 85.5]
```

## 4. Motor de Recomendación Híbrido

### Capa 1: Reglas de Negocio (Mandatorio)
```php
// Siempre se aplican estas reglas
- Solo lentes disponibles (estado = 'disponible')
- Presupuesto ≤ presupuesto_max
- Tipo de lente según uso (lectura→optical, deportes→sol)
- Tipo de montura según forma de rostro
```

### Capa 2: Scoring por Preferencias
```php
// Pesos configurables
'forma_rostro' => 30%  // Compatibilidad forma→montura
'uso_lentes'   => 25%  // Coincidencia tipo de lente
'estilo'       => 20%  // Coincidencia colores del estilo
'presupuesto'  => 15%  // Cercanía al presupuesto
'tipo_montura' => 10%  // Coincidencia de montura
```

### Capa 3: IA Generativa (Opcional)
```php
// Gemini/Groq genera explicaciones personalizadas
// y puede sugerir reordenamientos basados en理解 contextual
```

## 5. Flujo Completo de Recomendación

```
Usuario → Chat IA → Responde preferencias
                         │
                         ▼
                 ¿Análisis Facial?
                    /        \
                  Sí          No
                  │            │
           MediaPipe      Usar preferencias
           Detecta forma  manuales del chat
                  │            │
                  └─────┬──────┘
                        ▼
              RecommendationEngine
                        │
              ┌─────────┼─────────┐
              │         │         │
          Reglas    Scoring    IA (Gemini/Groq)
          Negocio   Matching   Explicaciones
              │         │         │
              └─────────┼─────────┘
                        ▼
          Top 10 lentes recomendados
          con % de compatibilidad
          y justificación IA
```

## 6. Mapeo Forma de Rostro → Tipo de Montura

| Forma Rostro | Monturas Recomendadas | Monturas a Evitar |
|-------------|----------------------|-------------------|
| Ovalado | Completa, Semi al aire, Al aire | Ninguna en particular |
| Redondo | Completa, Semi al aire (angulares) | Al aire (redondas) |
| Cuadrado | Semi al aire, Al aire (ovaladas) | Completa (angulares) |
| Rectangular | Completa, Semi al aire | Al aire |
| Corazón | Al aire, Semi al aire | Completa (pesadas arriba) |
| Diamante | Semi al aire, Al aire | Completa (anchas) |

## 7. Variables de Entorno Requeridas

```env
# AI Provider Selection
AI_PROVIDER=gemini|groq

# Gemini
GEMINI_API_KEY=your_key
GEMINI_MODEL=gemini-2.0-flash
GEMINI_TEMPERATURE=0.7
GEMINI_TIMEOUT=15

# Groq
GROQ_API_KEY=your_key
GROQ_MODEL=llama-3.3-70b-versatile
GROQ_TEMPERATURE=0.7
GROQ_TIMEOUT=15

# MediaPipe / Face Analysis
FACE_IMAGE_MAX_SIZE=5120
MEDIAPIPE_MIN_CONFIDENCE=0.5
FACE_PROCESSING_TIMEOUT=10
FACE_QUEUE_ENABLED=true
```
