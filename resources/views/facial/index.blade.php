@extends('layouts.app')

@section('title', 'Análisis Facial')

@push('styles')
<style>
    .facial-hero {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .camera-wrapper {
        position: relative;
        width: 100%;
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 4/3;
        max-width: 520px;
        margin: 0 auto;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }
    #face-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transform: scaleX(-1);
    }
    #face-canvas {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none;
        transform: scaleX(-1);
    }
    .camera-badge {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: #D4AF37;
        border-radius: 20px;
        padding: 5px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(212,175,55,0.3);
    }
    .live-shape-badge {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(212,175,55,0.92);
        color: #1a1a1a;
        border-radius: 20px;
        padding: 5px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
        display: none;
    }
    .tab-btn {
        border: 2px solid rgba(212,175,55,0.3);
        background: white;
        color: #555;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }
    .tab-btn.active {
        background: #D4AF37;
        border-color: #D4AF37;
        color: #1a1a1a;
    }
    .upload-dropzone {
        border: 2px dashed rgba(212,175,55,0.4);
        border-radius: 16px;
        padding: 2.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
        max-width: 520px;
        margin: 0 auto;
    }
    .upload-dropzone:hover {
        border-color: #D4AF37;
        background: rgba(212,175,55,0.05);
    }
    .upload-dropzone.has-image {
        padding: 0;
        border-style: solid;
        border-color: #D4AF37;
    }
    .upload-preview {
        width: 100%;
        border-radius: 14px;
        display: block;
        max-height: 380px;
        object-fit: contain;
    }
    .shape-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        background: rgba(212,175,55,0.12);
        border: 1px solid rgba(212,175,55,0.3);
        color: #B8962E;
    }
    .result-card-inner {
        background: linear-gradient(135deg, #1a1a1a 0%, #242424 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        text-align: center;
    }
    .forma-icon {
        font-size: 5rem;
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    .confidence-bar {
        height: 8px;
        border-radius: 4px;
        background: rgba(255,255,255,0.1);
        overflow: hidden;
        margin: 0.5rem auto;
        max-width: 260px;
    }
    .confidence-fill {
        height: 100%;
        background: linear-gradient(90deg, #D4AF37, #F5D06A);
        border-radius: 4px;
        transition: width 1s ease;
    }
    .montura-tag {
        display: inline-block;
        background: rgba(212,175,55,0.18);
        color: #D4AF37;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 3px;
    }
    .info-shape-card {
        border-radius: 12px;
        border: 1px solid rgba(212,175,55,0.2);
        padding: 0.75rem;
        text-align: center;
        background: white;
        transition: all 0.2s;
    }
    .info-shape-card:hover {
        border-color: #D4AF37;
        box-shadow: 0 4px 12px rgba(212,175,55,0.15);
    }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 960px;">

    {{-- Hero --}}
    <div class="facial-hero d-flex align-items-center gap-3">
        <div style="font-size: 2.5rem;">📷</div>
        <div>
            <h3 class="fw-bold mb-1">Análisis Facial</h3>
            <p class="mb-0 text-white-50 small">Detecta la forma de tu rostro con IA y recibe recomendaciones de montura personalizadas</p>
        </div>
    </div>

    @guest
    <div class="alert d-flex align-items-center gap-3 mb-4" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); border-radius: 12px; color: #e0e0e0;">
        <i class="bi bi-info-circle-fill" style="color: #D4AF37; font-size: 1.3rem; flex-shrink: 0;"></i>
        <div class="flex-grow-1">
            El análisis se realiza en tu dispositivo — <strong style="color: #D4AF37;">no necesitas cuenta</strong> para usarlo.
            Inicia sesión si quieres guardar tu análisis y usarlo en el Asesor Virtual.
        </div>
        <a href="{{ route('login') }}" class="btn btn-gold btn-sm ms-2" style="white-space: nowrap;">
            <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
        </a>
    </div>
    @endguest

    <div class="row g-4">
        {{-- Panel principal --}}
        <div class="col-lg-7">
            <div class="card shadow-sm" style="border-radius: 16px; border: 1px solid rgba(212,175,55,0.15);">
                <div class="card-body p-4">

                    {{-- Tabs: Cámara / Subir --}}
                    <div class="d-flex gap-2 mb-4">
                        <button class="tab-btn active" id="tab-camera" onclick="switchTab('camera')">
                            <i class="bi bi-webcam me-1"></i>Usar cámara
                        </button>
                        <button class="tab-btn" id="tab-upload" onclick="switchTab('upload')">
                            <i class="bi bi-upload me-1"></i>Subir foto
                        </button>
                    </div>

                    {{-- Vista Cámara --}}
                    <div id="panel-camera">
                        <p class="text-muted small mb-3 text-center">
                            <i class="bi bi-info-circle me-1" style="color:#D4AF37;"></i>
                            Centra tu rostro. Los puntos de color aparecen en tiempo real mientras se analiza.
                        </p>
                        <div class="camera-wrapper">
                            <video id="face-video" autoplay playsinline muted></video>
                            <canvas id="face-canvas"></canvas>
                            <div class="camera-badge" id="camera-status">
                                <span class="spinner-border spinner-border-sm me-1"></span>Iniciando cámara...
                            </div>
                            <div class="live-shape-badge" id="live-shape"></div>
                        </div>
                        <div class="text-center mt-3">
                            <button class="btn btn-gold px-4" id="btn-capture" disabled>
                                <i class="bi bi-camera me-1"></i>Capturar foto
                            </button>
                        </div>
                    </div>

                    {{-- Vista Upload --}}
                    <div id="panel-upload" style="display:none;">
                        <div class="upload-dropzone" id="dropzone" onclick="document.getElementById('file-upload').click()">
                            <div id="dropzone-placeholder">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #D4AF37;"></i>
                                <p class="mt-2 mb-1 fw-semibold text-dark">Haz clic para seleccionar una foto</p>
                                <p class="text-muted small mb-0">JPG o PNG · máx. 5 MB</p>
                            </div>
                            <img id="upload-preview" class="upload-preview" style="display:none;" alt="Vista previa">
                        </div>
                        <input type="file" id="file-upload" accept="image/jpeg,image/png" style="display:none;">
                        <div class="text-center mt-2" id="upload-change-btn" style="display:none;">
                            <button class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('file-upload').click()">
                                <i class="bi bi-arrow-repeat me-1"></i>Cambiar foto
                            </button>
                        </div>
                    </div>

                    {{-- Botón analizar --}}
                    <div class="text-center mt-4">
                        <button class="btn btn-gold px-5 py-2 fw-bold" id="btn-analyze" style="border-radius: 10px; font-size: 1rem;" disabled>
                            <i class="bi bi-stars me-2"></i>Analizar rostro
                        </button>
                        <div class="mt-2">
                            <small class="text-muted" id="analyze-hint">Captura o sube una foto para continuar</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resultado --}}
            <div id="result-section" style="display:none;" class="mt-4">
                <div class="result-card-inner">
                    <div class="forma-icon" id="result-icon">🥚</div>
                    <h4 class="fw-bold mb-1" id="result-titulo" style="color: #D4AF37;"></h4>
                    <div class="confidence-bar">
                        <div class="confidence-fill" id="result-confianza-bar" style="width:0%"></div>
                    </div>
                    <p class="text-white-50 small mb-3" id="result-confianza-label"></p>
                    <div class="p-3 rounded mb-3 text-start" style="background: rgba(255,255,255,0.06); border-left: 3px solid #D4AF37;">
                        <p class="fw-semibold mb-1" style="color:#D4AF37;"><i class="bi bi-lightbulb me-1"></i>Recomendación</p>
                        <p class="mb-2 text-white-75 small" id="result-mensaje"></p>
                        <div id="result-monturas"></div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center flex-wrap mt-3">
                        <a href="/asesor-virtual" class="btn btn-gold px-4">
                            <i class="bi bi-robot me-1"></i>Ir al asesor
                        </a>
                        <button class="btn btn-outline-light btn-sm" onclick="resetAnalysis()">
                            <i class="bi bi-arrow-repeat me-1"></i>Nuevo análisis
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel info lateral --}}
        <div class="col-lg-5">
            <div class="card shadow-sm" style="border-radius: 16px; border-top: 3px solid #D4AF37;">
                <div class="card-header" style="background: #1a1a1a; color: #D4AF37; font-weight: 600; border-radius: 13px 13px 0 0;">
                    <i class="bi bi-shapes me-1"></i> Formas de rostro
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        @foreach([
                            ['ovalado',     '🥚', 'Ovalado',     'Cualquier montura'],
                            ['redondo',     '⭕', 'Redondo',     'Completa, semi al aire'],
                            ['cuadrado',    '⬛', 'Cuadrado',    'Semi al aire, al aire'],
                            ['rectangular', '▬',  'Rectangular', 'Completa, semi al aire'],
                            ['corazon',     '🫀', 'Corazón',     'Al aire, semi al aire'],
                            ['diamante',    '💎', 'Diamante',    'Semi al aire, al aire'],
                        ] as [$key, $emoji, $label, $monturas])
                        <div class="col-6">
                            <div class="info-shape-card">
                                <div style="font-size: 1.8rem; line-height: 1.2;">{{ $emoji }}</div>
                                <div class="fw-semibold small mt-1">{{ $label }}</div>
                                <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">{{ $monturas }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-3 p-3 rounded" style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.2);">
                        <p class="small mb-0 text-muted">
                            <i class="bi bi-shield-check me-1" style="color: #D4AF37;"></i>
                            El análisis ocurre en tu navegador. No enviamos tu imagen a servidores externos.
                        </p>
                    </div>

                    <div class="mt-3 p-3 rounded" style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.2);">
                        <p class="small fw-semibold mb-2" style="color:#D4AF37;">
                            <i class="bi bi-lightbulb me-1"></i>Consejos para mejores resultados
                        </p>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Buena iluminación frontal</li>
                            <li>Rostro bien centrado en la cámara</li>
                            <li>Sin lentes puestos</li>
                            <li>Evita sombras fuertes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/mediapipe-face.js') }}?v={{ filemtime(public_path('js/mediapipe-face.js')) }}"></script>
<script>
const FORMAS_INFO = {
    ovalado:     { icon: '🥚', label: 'Ovalado',     msg: 'Tu rostro ovalado es muy versátil: puedes usar casi cualquier tipo de montura.', monturas: ['Completa', 'Semi al aire', 'Al aire'] },
    redondo:     { icon: '⭕', label: 'Redondo',     msg: 'Las monturas rectangulares o angulares alargan visualmente tu rostro.', monturas: ['Completa', 'Semi al aire'] },
    cuadrado:    { icon: '⬛', label: 'Cuadrado',    msg: 'Las monturas ovaladas o redondas suavizan los ángulos de tu rostro.', monturas: ['Semi al aire', 'Al aire'] },
    rectangular: { icon: '▬',  label: 'Rectangular', msg: 'Las monturas redondeadas equilibran la longitud de tu rostro.', monturas: ['Completa', 'Semi al aire'] },
    corazon:     { icon: '🫀', label: 'Corazón',     msg: 'Las monturas ligeras sin borde inferior equilibran tu frente más ancha.', monturas: ['Al aire', 'Semi al aire'] },
    diamante:    { icon: '💎', label: 'Diamante',    msg: 'Las monturas con detalles en la parte superior realzan tus pómulos.', monturas: ['Semi al aire', 'Al aire'] },
};

let analyzer = new FaceAnalyzer();
let capturedImage = null;
let activeTab = 'camera';
let liveInterval = null;

// ─── Tabs ─────────────────────────────────────────────────────────────────────

function switchTab(tab) {
    activeTab = tab;
    document.getElementById('tab-camera').classList.toggle('active', tab === 'camera');
    document.getElementById('tab-upload').classList.toggle('active', tab === 'upload');
    document.getElementById('panel-camera').style.display = tab === 'camera' ? '' : 'none';
    document.getElementById('panel-upload').style.display = tab === 'upload' ? '' : 'none';

    if (tab === 'camera') {
        startCamera();
    } else {
        stopCamera();
    }
}

// ─── Cámara ───────────────────────────────────────────────────────────────────

async function startCamera() {
    analyzer = new FaceAnalyzer();
    const statusEl = document.getElementById('camera-status');
    statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Iniciando cámara...';

    const result = await analyzer.initCamera('face-video', 'face-canvas');
    if (!result.ok) {
        let msg = result.error || 'Sin acceso a cámara';
        if (result.errorCode === 'NotAllowedError' || result.errorCode === 'PermissionDeniedError') {
            msg = 'Permiso de cámara denegado — usa "Subir foto"';
        }
        statusEl.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${msg}`;
        document.getElementById('btn-capture').disabled = true;
        document.getElementById('tab-upload').click();
        return;
    }

    statusEl.innerHTML = '<i class="bi bi-circle-fill me-1" style="font-size:0.5rem; color:#2b8a3e;"></i>Cámara activa · Centra tu rostro';
    document.getElementById('btn-capture').disabled = false;

    // Detección en vivo de forma
    liveInterval = setInterval(() => {
        if (!analyzer.currentLandmarks) return;
        const res = analyzer.estimateFaceShape(analyzer.currentLandmarks);
        if (res?.shape) {
            const liveEl = document.getElementById('live-shape');
            liveEl.style.display = '';
            liveEl.textContent = `${FORMAS_INFO[res.shape]?.icon || ''} ${FORMAS_INFO[res.shape]?.label || res.shape} · ${res.confidence}%`;
            statusEl.innerHTML = '✓ Rostro detectado — haz clic en Capturar';
        }
    }, 800);
}

function stopCamera() {
    if (liveInterval) { clearInterval(liveInterval); liveInterval = null; }
    analyzer.stopCamera();
    document.getElementById('live-shape').style.display = 'none';
}

document.getElementById('btn-capture').addEventListener('click', function () {
    const img = analyzer.captureImage();
    if (!img) return;

    capturedImage = img;
    stopCamera();

    // Mostrar preview en lugar de cámara
    const wrapper = document.querySelector('.camera-wrapper');
    wrapper.innerHTML = `<img src="${img}" style="width:100%;height:100%;object-fit:cover;border-radius:16px;display:block;">
        <div class="camera-badge" style="cursor:pointer;" onclick="resetCamera()">
            <i class="bi bi-arrow-repeat me-1"></i>Recapturar
        </div>`;

    enableAnalyzeBtn();
});

function resetCamera() {
    capturedImage = null;
    const wrapper = document.querySelector('.camera-wrapper');
    wrapper.innerHTML = `
        <video id="face-video" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;transform:scaleX(-1);"></video>
        <canvas id="face-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;transform:scaleX(-1);"></canvas>
        <div class="camera-badge" id="camera-status"><span class="spinner-border spinner-border-sm me-1"></span>Iniciando...</div>
        <div class="live-shape-badge" id="live-shape"></div>
    `;
    document.getElementById('btn-analyze').disabled = true;
    document.getElementById('analyze-hint').textContent = 'Captura o sube una foto para continuar';
    document.getElementById('result-section').style.display = 'none';
    startCamera();
}

// ─── Upload ───────────────────────────────────────────────────────────────────

document.getElementById('file-upload').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (ev) {
        capturedImage = ev.target.result;
        const preview = document.getElementById('upload-preview');
        const placeholder = document.getElementById('dropzone-placeholder');
        const dropzone = document.getElementById('dropzone');

        preview.src = capturedImage;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
        dropzone.classList.add('has-image');
        document.getElementById('upload-change-btn').style.display = '';

        enableAnalyzeBtn();
    };
    reader.readAsDataURL(file);
});

// ─── Analizar ─────────────────────────────────────────────────────────────────

function enableAnalyzeBtn() {
    document.getElementById('btn-analyze').disabled = false;
    document.getElementById('analyze-hint').textContent = 'Todo listo, haz clic en Analizar rostro';
}

document.getElementById('btn-analyze').addEventListener('click', async function () {
    if (!capturedImage) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analizando...';
    document.getElementById('result-section').style.display = 'none';

    try {
        const result = await analyzer.uploadForAnalysis(capturedImage);

        if (result && result.success && result.data) {
            showResult(result.data.forma_rostro, result.data.confianza, result.data.analisis_id);
        } else {
            // Si falla, intenta análisis local directo
            const localRes = await analyzer.analyzeImage(capturedImage).catch(() => null);
            if (localRes?.detected && localRes?.shape) {
                showResult(localRes.shape, localRes.confidence, null);
            } else {
                showResult('ovalado', 55, null); // Fallback
            }
        }
    } catch (err) {
        showResult('ovalado', 55, null);
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-stars me-2"></i>Analizar rostro';
});

function showResult(forma, confianza, analisisId) {
    const info = FORMAS_INFO[forma] || FORMAS_INFO['ovalado'];
    const conf = Math.round(parseFloat(confianza) || 55);

    // Guardar en sessionStorage para el asesor
    sessionStorage.setItem('asesor_pref_forma_rostro', forma);
    if (analisisId) sessionStorage.setItem('asesor_pref_analisis_facial_id', analisisId);

    document.getElementById('result-icon').textContent = info.icon;
    document.getElementById('result-titulo').textContent = `Rostro ${info.label}`;
    document.getElementById('result-confianza-bar').style.width = conf + '%';
    document.getElementById('result-confianza-label').textContent = `Confianza: ${conf}%`;
    document.getElementById('result-mensaje').textContent = info.msg;

    const monturasHtml = info.monturas.map(m =>
        `<span class="montura-tag">${m}</span>`
    ).join('');
    document.getElementById('result-monturas').innerHTML = '<div class="mt-1">' + monturasHtml + '</div>';

    const section = document.getElementById('result-section');
    section.style.display = '';
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetAnalysis() {
    capturedImage = null;
    document.getElementById('result-section').style.display = 'none';
    document.getElementById('btn-analyze').disabled = true;
    document.getElementById('analyze-hint').textContent = 'Captura o sube una foto para continuar';

    if (activeTab === 'upload') {
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('upload-preview').src = '';
        document.getElementById('dropzone-placeholder').style.display = '';
        document.getElementById('dropzone').classList.remove('has-image');
        document.getElementById('upload-change-btn').style.display = 'none';
        document.getElementById('file-upload').value = '';
    } else {
        resetCamera();
    }
}

// ─── Inicio ───────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', async function () {
    // Pre-cargar scripts de MediaPipe antes de pedir la cámara
    try {
        await FaceAnalyzer.preloadScripts();
    } catch (e) {
        console.warn('No se pudieron precargar scripts MediaPipe:', e);
    }
    // Verificar soporte de cámara
    const support = await FaceAnalyzer.checkCameraSupport();
    if (!support.supported && support.errors.some(e => e.includes('HTTPS'))) {
        document.getElementById('camera-status').innerHTML =
            '<i class="bi bi-shield-lock me-1"></i>Cámara requiere HTTPS';
        document.getElementById('btn-capture').disabled = true;
        document.getElementById('tab-upload').click();
        return;
    }
    startCamera();
});
</script>
@endpush
