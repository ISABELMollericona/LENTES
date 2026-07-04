// Asesor Virtual - Chat IA Frontend con análisis facial integrado

class AsesorVirtual {
    constructor(options = {}) {
        this.chatContainer = document.getElementById(options.chatContainerId || 'chat-messages');
        this.inputElement = document.getElementById(options.inputId || 'chat-input');
        this.sendButton = document.getElementById(options.sendButtonId || 'chat-send');
        this.recommendButton = document.getElementById(options.recommendButtonId || 'btn-recommend');
        this.sessionId = this.loadSession();
        this.baseUrl = options.baseUrl || '/api/asesor';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.apiToken = document.querySelector('meta[name="api-token"]')?.content || '';

        this.conversationStep = 0;
        this.faceAnalyzer = null;
        this.faceModal = null;
        this.liveShapeInterval = null;

        this.init();
    }

    loadSession() {
        let session = sessionStorage.getItem('asesor_sesion_id');
        if (!session) {
            session = crypto.randomUUID();
            sessionStorage.setItem('asesor_sesion_id', session);
        }
        return session;
    }

    async init() {
        if (this.sendButton) {
            this.sendButton.addEventListener('click', () => this.sendMessage());
        }
        if (this.inputElement) {
            this.inputElement.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.sendMessage();
            });
        }
        if (this.recommendButton) {
            this.recommendButton.addEventListener('click', () => this.requestRecommendation());
        }

        // Event delegation para botones de carrito
        if (this.chatContainer) {
            this.chatContainer.addEventListener('click', async (e) => {
                const btn = e.target.closest('.add-to-cart');
                if (!btn || btn.disabled) return;
                await this.handleAddToCart(btn);
            });
        }

        // Pre-cargar scripts de MediaPipe
        try {
            await FaceAnalyzer.preloadScripts();
        } catch (e) {
            console.warn('No se pudieron precargar scripts MediaPipe:', e);
        }

        this.initFaceModal();
        this.startGuiado();
    }

    // ─── Flujo guiado ────────────────────────────────────────────────────

    startGuiado() {
        this.conversationStep = 0;
        this.addSystemMessage('¡Hola! Soy <strong>Golden Assistant</strong>, tu asesor virtual de Óptica Golden. Te ayudaré a encontrar los lentes perfectos para ti. 👓', true);
        setTimeout(() => this.askGenero(), 600);
    }

    askGenero() {
        this.conversationStep = 0;
        this.addSystemMessage('Para comenzar, ¿cuál es tu género? Esto me ayuda a recomendarte los lentes más adecuados.');
        this.showQuickReplies([
            { label: '👨 Hombre', value: 'hombre' },
            { label: '👩 Mujer', value: 'mujer' },
            { label: '🤝 Prefiero no decir', value: 'unisex' }
        ], (value) => {
            this.setPreference('genero', value);
            const labels = { hombre: 'Hombre', mujer: 'Mujer', unisex: 'Prefiero no decirlo' };
            this.addUserMessage(labels[value] || value);
            setTimeout(() => this.askUso(), 400);
        });
    }

    askUso() {
        this.conversationStep = 1;
        this.addSystemMessage('¿Para qué usarás principalmente tus lentes?');
        this.showQuickReplies([
            { label: '💻 Computadora', value: 'computadora' },
            { label: '📖 Lectura', value: 'lectura' },
            { label: '📚 Estudio', value: 'estudio' },
            { label: '🚗 Conducir', value: 'conducir' },
            { label: '☀️ Uso diario', value: 'uso_diario' },
            { label: '⚽ Deportes', value: 'deportes' },
            { label: '👔 Moda', value: 'moda' }
        ], (value) => {
            this.setPreference('uso_lentes', value);
            this.addUserMessage(this.getLabelForValue(value, 'uso'));
            setTimeout(() => this.askPresupuesto(), 400);
        });
    }

    askPresupuesto() {
        this.conversationStep = 2;
        this.addSystemMessage('¿Cuál es tu presupuesto máximo? (Opcional — escríbelo o selecciona una opción)');
        this.showQuickReplies([
            { label: 'Bs 0 - 500', value: '500' },
            { label: 'Bs 500 - 1000', value: '1000' },
            { label: 'Bs 1000 - 2000', value: '2000' },
            { label: 'Sin límite', value: '' }
        ], (value) => {
            this.setPreference('presupuesto_max', value);
            const label = value ? `Hasta Bs ${value}` : 'Sin límite de presupuesto';
            this.addUserMessage(label);
            setTimeout(() => this.askFaceAnalysis(), 400);
        });
    }

    askFaceAnalysis() {
        this.conversationStep = 3;
        this.addSystemMessage(
            '📷 Puedo analizar la <strong>forma de tu rostro en tiempo real</strong> con tu cámara para recomendarte la montura ideal. ' +
            'Verás tus puntos faciales en vivo mientras proceso tu análisis. ¿Lo hacemos?', true
        );
        this.showQuickReplies([
            { label: '📷 Analizar mi rostro ahora', value: 'si' },
            { label: '⏭️ Omitir este paso', value: 'no' }
        ], (value) => {
            if (value === 'si') {
                this.addUserMessage('Sí, analizar mi rostro');
                setTimeout(() => this.openFaceModal(), 300);
            } else {
                this.addUserMessage('Omitir análisis facial');
                setTimeout(() => this.askEstilo(), 400);
            }
        });
    }

    askEstilo() {
        this.conversationStep = 4;
        this.addSystemMessage('¿Qué estilo prefieres?');
        this.showQuickReplies([
            { label: '🎩 Clásico', value: 'clasico' },
            { label: '✨ Moderno', value: 'moderno' },
            { label: '💼 Ejecutivo', value: 'ejecutivo' },
            { label: '🏃 Deportivo', value: 'deportivo' },
            { label: '🤍 Minimalista', value: 'minimalista' },
            { label: '👕 Casual', value: 'casual' }
        ], (value) => {
            this.setPreference('estilo', value);
            this.addUserMessage(this.getLabelForValue(value, 'estilo'));
            setTimeout(() => this.askMontura(), 400);
        });
    }

    askMontura() {
        this.conversationStep = 5;

        // Si ya tenemos forma de rostro, hacer la sugerencia
        const forma = this.getPreference('forma_rostro');
        const sugerencias = {
            redondo: 'completa o semi al aire',
            cuadrado: 'semi al aire o al aire',
            ovalado: 'cualquier tipo',
            rectangular: 'completa o semi al aire',
            corazon: 'al aire o semi al aire',
            diamante: 'semi al aire o al aire',
        };
        const sugerencia = forma ? ` (para tu rostro ${forma} se recomiendan monturas ${sugerencias[forma] || 'variadas'})` : '';

        this.addSystemMessage(`¿Qué tipo de montura prefieres?${sugerencia}`);
        this.showQuickReplies([
            { label: '⬛ Montura completa', value: 'completa' },
            { label: '🔲 Semi al aire', value: 'semi_al_aire' },
            { label: '⬜ Al aire (sin marco)', value: 'al_aire' }
        ], (value) => {
            this.setPreference('tipo_montura', value);
            const labels = { completa: 'Montura completa', semi_al_aire: 'Semi al aire', al_aire: 'Al aire' };
            this.addUserMessage(labels[value] || value);
            setTimeout(() => this.askColor(), 400);
        });
    }

    askColor() {
        this.conversationStep = 6;
        this.addSystemMessage('¿Tienes algún color favorito? (Opcional)');
        this.showQuickReplies([
            { label: '⚫ Negro', value: 'negro' },
            { label: '🟤 Marrón/Carey', value: 'carey' },
            { label: '🥇 Dorado', value: 'dorado' },
            { label: '⚪ Plateado', value: 'plateado' },
            { label: '🔵 Azul', value: 'azul' },
            { label: 'Sin preferencia', value: '' }
        ], (value) => {
            this.setPreference('color_favorito', value);
            const label = value ? `Color: ${value}` : 'Sin preferencia de color';
            this.addUserMessage(label);
            setTimeout(() => this.mostrarResumen(), 400);
        });
    }

    mostrarResumen() {
        this.conversationStep = 7;
        const genero = this.getPreference('genero');
        const uso = this.getPreference('uso_lentes');
        const presupuesto = this.getPreference('presupuesto_max');
        const forma = this.getPreference('forma_rostro');
        const estilo = this.getPreference('estilo');
        const montura = this.getPreference('tipo_montura');
        const color = this.getPreference('color_favorito');

        const generolabel = { hombre: 'Hombre', mujer: 'Mujer', unisex: 'No especificado' };

        const resumen = `
            <strong>¡Perfecto! Aquí está tu perfil:</strong><br>
            <ul class="mb-2 mt-1" style="padding-left: 1.2rem;">
                <li>Género: <strong>${generolabel[genero] || '-'}</strong></li>
                <li>Uso: <strong>${this.getLabelForValue(uso, 'uso')}</strong></li>
                <li>Presupuesto: <strong>${presupuesto ? 'Hasta Bs ' + presupuesto : 'Sin límite'}</strong></li>
                ${forma ? `<li>Forma de rostro: <strong>${forma.charAt(0).toUpperCase() + forma.slice(1)}</strong> (detectada)</li>` : ''}
                <li>Estilo: <strong>${this.getLabelForValue(estilo, 'estilo')}</strong></li>
                <li>Montura: <strong>${this.getLabelForValue(montura, 'montura')}</strong></li>
                ${color ? `<li>Color favorito: <strong>${color}</strong></li>` : ''}
            </ul>
        `;
        this.addSystemMessage(resumen, true);

        setTimeout(() => {
            this.addSystemMessage('¡Todo listo! Genera tus recomendaciones personalizadas.', false);
            this.showActionButtons();
        }, 500);
    }

    showActionButtons() {
        const html = `
            <div class="d-flex flex-wrap gap-2 mb-3 justify-content-center">
                <button class="btn btn-gold fw-semibold px-4" id="btn-gen-rec" style="border-radius: 8px;">
                    <i class="bi bi-stars me-1"></i>Ver mis recomendaciones
                </button>
            </div>
        `;
        this.chatContainer.innerHTML += html;
        this.scrollToBottom();

        document.getElementById('btn-gen-rec')?.addEventListener('click', () => {
            this.requestRecommendation();
        });
    }

    // ─── Análisis facial integrado en el chat ────────────────────────────

    initFaceModal() {
        const modalEl = document.getElementById('faceModal');
        if (!modalEl) return;

        this.faceModal = new bootstrap.Modal(modalEl);

        // Cuando se abre el modal, iniciar cámara
        modalEl.addEventListener('shown.bs.modal', () => this.startFaceCamera());

        // Cuando se cierra, detener cámara
        modalEl.addEventListener('hidden.bs.modal', () => this.stopFaceCamera());

        // Botón omitir
        document.getElementById('btn-skip-face')?.addEventListener('click', () => {
            this.faceModal.hide();
            setTimeout(() => {
                this.addSystemMessage('Análisis facial omitido. Continuamos con el resto de preguntas.');
                this.askEstilo();
            }, 400);
        });

        // Botón cerrar (X)
        document.getElementById('btn-close-face-modal')?.addEventListener('click', () => {
            this.faceModal.hide();
            setTimeout(() => {
                this.addSystemMessage('Análisis facial cancelado. Continuamos.');
                this.askEstilo();
            }, 400);
        });

        // Botón capturar
        document.getElementById('btn-capture-face')?.addEventListener('click', () => this.captureFace());

        // Subir imagen
        document.getElementById('face-file-input')?.addEventListener('change', (e) => {
            if (e.target.files?.[0]) this.analyzeFaceFile(e.target.files[0]);
        });
    }

    openFaceModal() {
        // Reset vistas
        document.getElementById('face-camera-view').style.display = '';
        document.getElementById('face-result-view').style.display = 'none';
        document.getElementById('face-modal-footer').style.display = '';
        document.getElementById('btn-capture-face').style.display = '';
        document.getElementById('face-overlay-status').innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span>Iniciando cámara...';
        document.getElementById('face-live-shape').style.display = 'none';

        this.faceModal?.show();
    }

    async startFaceCamera() {
        this.faceAnalyzer = new FaceAnalyzer();
        const result = await this.faceAnalyzer.initCamera('face-video', 'face-canvas');

        const statusEl = document.getElementById('face-overlay-status');
        if (!result.ok) {
            let msg = result.error || 'Sin acceso a cámara';
            if (result.errorCode === 'NotAllowedError' || result.errorCode === 'PermissionDeniedError') {
                msg = 'Permiso de cámara denegado — sube una foto';
            }
            statusEl.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${msg}`;
            document.getElementById('btn-capture-face').style.display = 'none';
        } else {
            statusEl.innerHTML = '<i class="bi bi-circle-fill me-1" style="color:#2b8a3e; font-size:0.6rem;"></i>Centra tu rostro en el recuadro';
            this.startLiveShapeDetection();
        }
    }

    startLiveShapeDetection() {
        const liveEl = document.getElementById('face-live-shape');

        this.liveShapeInterval = setInterval(() => {
            if (!this.faceAnalyzer?.currentLandmarks) return;
            const result = this.faceAnalyzer.estimateFaceShape(this.faceAnalyzer.currentLandmarks);
            if (result?.shape) {
                liveEl.style.display = '';
                const labels = {
                    ovalado: 'Ovalado', redondo: 'Redondo', cuadrado: 'Cuadrado',
                    rectangular: 'Rectangular', corazon: 'Corazón', diamante: 'Diamante'
                };
                liveEl.innerHTML = `<i class="bi bi-emoji-smile me-1"></i>${labels[result.shape] || result.shape} · ${result.confidence}%`;

                const statusEl = document.getElementById('face-overlay-status');
                if (statusEl) statusEl.innerHTML = '✓ Rostro detectado — haz clic en Capturar';
            }
        }, 800);
    }

    stopFaceCamera() {
        if (this.liveShapeInterval) {
            clearInterval(this.liveShapeInterval);
            this.liveShapeInterval = null;
        }
        this.faceAnalyzer?.stopCamera();
        this.faceAnalyzer = null;
    }

    async captureFace() {
        if (!this.faceAnalyzer) return;

        const captureBtn = document.getElementById('btn-capture-face');
        captureBtn.disabled = true;
        captureBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Analizando...';

        // Intentar con landmarks en vivo primero (más rápido)
        let shapeResult = null;
        if (this.faceAnalyzer.currentLandmarks) {
            shapeResult = this.faceAnalyzer.estimateFaceShape(this.faceAnalyzer.currentLandmarks);
        }

        const imageData = this.faceAnalyzer.captureImage();
        const analyzerRef = this.faceAnalyzer;
        this.stopFaceCamera();

        if (shapeResult?.shape) {
            // Análisis local directo — enviar al backend para guardar
            this.showFaceResult(shapeResult.shape, shapeResult.confidence, imageData);
            this.uploadFaceToBackend(imageData, shapeResult.shape, shapeResult.confidence);
        } else if (imageData) {
            // Fallback: analizar imagen con MediaPipe
            try {
                const serverResult = await analyzerRef.uploadForAnalysis(imageData);
                const forma = serverResult?.data?.forma_rostro || 'ovalado';
                const confianza = serverResult?.data?.confianza || 70;
                const analisisId = serverResult?.data?.analisis_id;
                if (analisisId) this.setPreference('analisis_facial_id', analisisId);
                this.showFaceResult(forma, confianza, imageData);
            } catch {
                this.showFaceResult('ovalado', 60, imageData);
            }
        } else {
            captureBtn.disabled = false;
            captureBtn.innerHTML = '<i class="bi bi-camera me-1"></i>Capturar y analizar';
            this.addSystemMessage('No se pudo capturar la imagen. Intenta subir una foto.');
        }
    }

    async uploadFaceToBackend(imageData, forma, confianza) {
        try {
            const blob = await fetch(imageData).then(r => r.blob());
            const formData = new FormData();
            formData.append('imagen', blob, 'face.jpg');
            formData.append('forma_rostro', forma);
            formData.append('confianza', confianza.toString());
            formData.append('analisis_local', '1');

            const res = await fetch('/api/analisis-facial', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.apiToken}`,
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: formData
            });
            const data = await res.json();
            if (data?.success && data?.data?.analisis_id) {
                this.setPreference('analisis_facial_id', data.data.analisis_id);
            }
        } catch { /* silencioso */ }
    }

    async analyzeFaceFile(file) {
        const statusEl = document.getElementById('face-overlay-status');
        if (statusEl) statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando imagen...';

        const reader = new FileReader();
        reader.onload = async (e) => {
            const imageData = e.target.result;
            if (!this.faceAnalyzer) this.faceAnalyzer = new FaceAnalyzer();
            try {
                const result = await this.faceAnalyzer.analyzeImage(imageData);
                this.stopFaceCamera();
                if (result?.detected && result?.shape) {
                    this.showFaceResult(result.shape, result.confidence, imageData);
                    this.uploadFaceToBackend(imageData, result.shape, result.confidence);
                } else {
                    this.showFaceResult('ovalado', 60, imageData);
                }
            } catch {
                this.showFaceResult('ovalado', 60, imageData);
            }
        };
        reader.readAsDataURL(file);
    }

    showFaceResult(forma, confianza, imagenSrc) {
        // Ocultar vista de cámara
        document.getElementById('face-camera-view').style.display = 'none';

        const labels = {
            ovalado: 'Ovalado', redondo: 'Redondo', cuadrado: 'Cuadrado',
            rectangular: 'Rectangular', corazon: 'Corazón', diamante: 'Diamante'
        };
        const recomMonturas = {
            redondo: 'Montura completa o Semi al aire',
            cuadrado: 'Semi al aire o Al aire',
            ovalado: 'Cualquier montura le sienta bien',
            rectangular: 'Montura completa o Semi al aire',
            corazon: 'Al aire o Semi al aire',
            diamante: 'Semi al aire o Al aire',
        };

        document.getElementById('face-result-content').innerHTML = `
            <div class="text-center py-2">
                <div style="font-size: 4rem; margin-bottom: 0.5rem;">
                    ${forma === 'redondo' ? '⭕' : forma === 'cuadrado' ? '⬛' : forma === 'corazon' ? '🫀' : forma === 'diamante' ? '💎' : forma === 'rectangular' ? '▬' : '🥚'}
                </div>
                <h4 style="color: #D4AF37; font-weight: 700;">Rostro ${labels[forma] || forma}</h4>
                <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                    <div class="progress" style="width: 120px; height: 8px;">
                        <div class="progress-bar" style="width:${confianza}%; background:#D4AF37;"></div>
                    </div>
                    <span class="fw-bold" style="color:#D4AF37;">${Math.round(confianza)}% confianza</span>
                </div>
                <div class="alert" style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); border-radius: 10px; text-align: left;">
                    <strong><i class="bi bi-lightbulb me-1" style="color:#D4AF37;"></i>Montura recomendada:</strong><br>
                    <span class="text-muted">${recomMonturas[forma] || 'Variada'}</span>
                </div>
            </div>
        `;
        document.getElementById('face-result-view').style.display = '';

        // Cambiar footer
        document.getElementById('face-modal-footer').innerHTML = `
            <button class="btn btn-outline-secondary btn-sm" id="btn-retry-face">
                <i class="bi bi-arrow-repeat me-1"></i>Repetir
            </button>
            <button class="btn btn-gold px-4" id="btn-use-face-result">
                <i class="bi bi-check-circle me-1"></i>Usar este resultado
            </button>
        `;

        document.getElementById('btn-retry-face')?.addEventListener('click', () => {
            document.getElementById('face-camera-view').style.display = '';
            document.getElementById('face-result-view').style.display = 'none';
            document.getElementById('face-modal-footer').innerHTML = `
                <button class="btn btn-outline-secondary btn-sm" id="btn-skip-face">
                    <i class="bi bi-skip-forward me-1"></i>Omitir análisis
                </button>
                <button class="btn btn-gold px-4" id="btn-capture-face">
                    <i class="bi bi-camera me-1"></i>Capturar y analizar
                </button>
            `;
            document.getElementById('btn-skip-face')?.addEventListener('click', () => {
                this.faceModal.hide();
                setTimeout(() => { this.addSystemMessage('Análisis omitido.'); this.askEstilo(); }, 400);
            });
            document.getElementById('btn-capture-face')?.addEventListener('click', () => this.captureFace());
            this.startFaceCamera();
        });

        document.getElementById('btn-use-face-result')?.addEventListener('click', () => {
            this.setPreference('forma_rostro', forma);
            this.faceModal.hide();
            setTimeout(() => {
                this.addSystemMessage(
                    `✅ ¡Rostro <strong>${labels[forma]}</strong> detectado con ${Math.round(confianza)}% de confianza! ` +
                    `Esto me ayudará a recomendarte la montura ideal.`, true
                );
                setTimeout(() => this.askEstilo(), 600);
            }, 400);
        });
    }

    // ─── Recomendaciones ─────────────────────────────────────────────────

    async requestRecommendation() {
        const uso = this.getPreference('uso_lentes');
        const estilo = this.getPreference('estilo');
        const montura = this.getPreference('tipo_montura');

        if (!uso || !estilo || !montura) {
            this.addSystemMessage('Primero necesito conocer tus preferencias. ¡Empecemos de nuevo!');
            setTimeout(() => this.startGuiado(), 800);
            return;
        }

        this.addSystemMessage('<i class="bi bi-stars me-1" style="color:#D4AF37;"></i>Generando recomendaciones personalizadas...', true);
        this.showTypingIndicator();

        try {
            const body = {
                uso_lentes: uso,
                estilo: estilo,
                tipo_montura: montura,
            };

            const presupuesto = this.getPreference('presupuesto_max');
            if (presupuesto) body.presupuesto_max = presupuesto;

            const color = this.getPreference('color_favorito');
            if (color) body.color_favorito = color;

            const formaRostro = this.getPreference('forma_rostro');
            if (formaRostro) body.forma_rostro = formaRostro;

            const genero = this.getPreference('genero');
            if (genero) body.genero = genero;

            const analisisFacialId = this.getPreference('analisis_facial_id');
            if (analisisFacialId) body.analisis_facial_id = analisisFacialId;

            const response = await fetch(`${this.baseUrl}/recomendar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.apiToken}`,
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify(body)
            });

            this.hideTypingIndicator();

            if (!response.ok) {
                let errorMsg = 'Error al generar recomendaciones. Intenta de nuevo.';
                try {
                    const errData = await response.json();
                    if (errData?.message) errorMsg = errData.message;
                } catch (_) {}
                this.addSystemMessage(errorMsg);
                return;
            }

            const data = await response.json();

            if (data.success && data.data.resultados?.length) {
                this.displayRecommendations(data.data.resultados);
            } else {
                this.addSystemMessage('No encontramos lentes con exactamente tus preferencias. Intenta ampliar el presupuesto o cambiar el tipo de montura.');
            }
        } catch (err) {
            this.hideTypingIndicator();
            this.addSystemMessage('Error al generar recomendaciones. Intenta de nuevo.');
        }
    }

    resolveImageUrl(lente) {
        if (lente.imagen_principal && lente.imagen_principal.startsWith('img/')) {
            return '/' + lente.imagen_principal;
        }
        if (Array.isArray(lente.imagenes) && lente.imagenes.length > 0) {
            const url = lente.imagenes[0].url;
            if (url && url.startsWith('img/')) return '/' + url;
        }
        if (lente.imagen_url) return lente.imagen_url;
        const index = (lente.id ?? 0) % 60;
        const carpeta = lente.tipo_lente === 'sol' ? 'sunglasses/S' : 'eyeglasses/E';
        return `/img/lentes/dataset/${carpeta}_${index}.jpg`;
    }

    displayRecommendations(results) {
        const genero = this.getPreference('genero');
        const forma = this.getPreference('forma_rostro');

        let intro = `<i class="bi bi-check-circle me-1" style="color:#D4AF37;"></i><strong>¡Aquí están tus ${results.length} recomendaciones!</strong>`;
        if (forma) intro += ` Optimizadas para tu rostro <strong>${forma}</strong>`;
        if (genero && genero !== 'unisex') intro += ` para <strong>${genero}</strong>`;
        this.addSystemMessage(intro + '.', true);

        results.forEach((item, index) => {
            const lente = item.lente;
            if (!lente) return;
            const compatibilidad = Math.round(item.compatibilidad);
            const badgeColor = lente.estado === 'disponible' ? '#2b8a3e' : '#c92a2a';
            const imagenSrc = this.resolveImageUrl(lente);
            const fallbackIdx = (lente.id ?? 0) % 60;
            const fallbackDir = lente.tipo_lente === 'sol' ? 'sunglasses/S' : 'eyeglasses/E';
            const fallbackSrc = `/img/lentes/dataset/${fallbackDir}_${fallbackIdx}.jpg`;

            this.chatContainer.innerHTML += `
                <div class="card mb-2 shadow-sm recommendation-card" style="border-left: 3px solid #D4AF37; animation: slideInMessage 0.4s ease ${index * 0.1}s both;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:90px; height:70px; flex-shrink:0; border-radius:8px; overflow:hidden; background:#f5f5f5; display:flex; align-items:center; justify-content:center;">
                                <img src="${imagenSrc}" alt="${this.escapeHtml(lente.nombre)}"
                                     onerror="this.onerror=null;this.src='${fallbackSrc}';"
                                     style="max-width:90px; max-height:70px; width:auto; height:auto; object-fit:contain; display:block;">
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge text-white me-1" style="background:${badgeColor}; font-size:0.7rem;">${this.escapeHtml(lente.estado)}</span>
                                        <small class="text-muted">${this.escapeHtml(lente.marca?.nombre || '')}</small>
                                        ${lente.genero ? `<span class="badge bg-light text-dark ms-1" style="font-size:0.65rem;">${lente.genero}</span>` : ''}
                                    </div>
                                    <span class="fw-bold" style="color:#D4AF37; white-space:nowrap;">Bs ${parseFloat(lente.precio || 0).toLocaleString('es', {minimumFractionDigits:2})}</span>
                                </div>
                                <div class="fw-semibold" style="font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">#${index+1} ${this.escapeHtml(lente.nombre)}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="progress flex-grow-1" style="height:6px; min-width:60px;">
                                        <div class="progress-bar" style="width:${compatibilidad}%; background:#D4AF37;"></div>
                                    </div>
                                    <small style="color:#D4AF37; white-space:nowrap;">${compatibilidad}% compatible</small>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size:0.75rem;">${this.escapeHtml(item.justificacion || '')}</small>
                            </div>
                            <div class="d-flex flex-column gap-1 ms-2" style="flex-shrink:0;">
                                <a href="/lentes/${lente.id}" class="btn btn-sm btn-outline-gold" style="border-radius:6px;" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                ${lente.estado === 'disponible'
                                    ? `<button class="btn btn-sm btn-gold add-to-cart" data-lente-id="${lente.id}" style="border-radius:6px;" title="Agregar al carrito">
                                           <i class="bi bi-cart-plus"></i>
                                       </button>`
                                    : `<span class="badge bg-secondary text-center" style="font-size:0.65rem;">Vendido</span>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        this.scrollToBottom();

        this.chatContainer.innerHTML += `
            <div class="d-flex gap-2 flex-wrap justify-content-center my-3">
                <button class="btn btn-outline-gold btn-sm" onclick="Object.keys(sessionStorage).filter(k=>k.startsWith('asesor_')).forEach(k=>sessionStorage.removeItem(k)); location.reload();">
                    <i class="bi bi-arrow-repeat me-1"></i>Nueva asesoría
                </button>
                <a href="/carrito" class="btn btn-gold btn-sm">
                    <i class="bi bi-cart3 me-1"></i>Ver carrito
                </a>
            </div>
        `;
        this.scrollToBottom();
    }

    async handleAddToCart(btn) {
        const lenteId = btn.dataset.lenteId;
        if (!lenteId) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const response = await fetch(`/carrito/agregar/${lenteId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            let data = {};
            try { data = await response.json(); } catch (_) {}

            if (response.ok && data.success) {
                btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                btn.classList.replace('btn-gold', 'btn-success');
                btn.disabled = true;
                const badge = document.getElementById('cart-count');
                if (badge) badge.textContent = data.count ?? (parseInt(badge.textContent || '0') + 1);
                this.addSystemMessage('✅ Lente agregado al carrito. <a href="/carrito" class="text-gold fw-bold">Ver carrito →</a>', true);
            } else {
                btn.innerHTML = '<i class="bi bi-cart-plus"></i>';
                btn.disabled = false;
                const msg = data?.message || 'No se pudo agregar al carrito.';
                this.addSystemMessage('⚠️ ' + msg);
            }
        } catch (err) {
            btn.innerHTML = '<i class="bi bi-cart-plus"></i>';
            btn.disabled = false;
            this.addSystemMessage('⚠️ Error de conexión al agregar al carrito.');
        }
    }

    // ─── Chat libre ──────────────────────────────────────────────────────

    async sendMessage(message = null) {
        const msg = message || this.inputElement?.value.trim();
        if (!msg) return;

        this.addUserMessage(msg);
        if (this.inputElement) this.inputElement.value = '';

        this.showTypingIndicator();

        try {
            const response = await fetch(`${this.baseUrl}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.apiToken}`,
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ mensaje: msg, sesion_id: this.sessionId })
            });

            this.hideTypingIndicator();

            if (!response.ok) { this.addSystemMessage('Lo siento, tuve un problema. ¿Puedes repetirlo?'); return; }

            const data = await response.json();
            if (data.success) {
                this.addSystemMessage(data.data.respuesta);
            } else {
                this.addSystemMessage('Lo siento, tuve un problema. ¿Puedes repetirlo?');
            }
        } catch {
            this.hideTypingIndicator();
            this.addSystemMessage('Error de conexión. Verifica tu internet e intenta de nuevo.');
        }
    }

    // ─── Utilidades ──────────────────────────────────────────────────────

    getLabelForValue(value, type) {
        const maps = {
            uso: {
                computadora: 'Computadora', lectura: 'Lectura', estudio: 'Estudio',
                conducir: 'Conducir', uso_diario: 'Uso diario', deportes: 'Deportes', moda: 'Moda'
            },
            estilo: {
                clasico: 'Clásico', moderno: 'Moderno', ejecutivo: 'Ejecutivo',
                deportivo: 'Deportivo', minimalista: 'Minimalista', casual: 'Casual'
            },
            montura: {
                completa: 'Montura completa', semi_al_aire: 'Semi al aire', al_aire: 'Al aire'
            }
        };
        return maps[type]?.[value] || value || '-';
    }

    getPreference(key) {
        return sessionStorage.getItem(`asesor_pref_${key}`);
    }

    setPreference(key, value) {
        if (value) {
            sessionStorage.setItem(`asesor_pref_${key}`, value);
        } else {
            sessionStorage.removeItem(`asesor_pref_${key}`);
        }
    }

    addUserMessage(text) {
        this.chatContainer.innerHTML += `
            <div class="d-flex justify-content-end mb-2" style="animation: slideInMessage 0.3s ease both;">
                <div class="user-message text-white p-2 px-3 rounded-4" style="max-width: 75%; background: linear-gradient(135deg, #D4AF37, #B8962E); color:#1a1a1a !important;">
                    ${text}
                </div>
            </div>`;
        this.scrollToBottom();
    }

    addSystemMessage(text, isHtml = false) {
        const content = isHtml ? text : this.escapeHtml(text);
        this.chatContainer.innerHTML += `
            <div class="d-flex mb-2" style="animation: slideInMessage 0.3s ease both;">
                <div class="p-2 px-3 rounded-4" style="max-width: 85%; background: #f8f9fa; border: 1px solid rgba(212,175,55,0.2);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-robot" style="color:#D4AF37; font-size:0.85rem;"></i>
                        <strong style="font-size:0.8rem; color:#D4AF37;">Golden Assistant</strong>
                    </div>
                    <div style="font-size:0.9rem;">${content}</div>
                </div>
            </div>`;
        this.scrollToBottom();
    }

    showQuickReplies(options, callback) {
        let html = '<div class="d-flex flex-wrap gap-2 mb-3">';
        options.forEach(opt => {
            html += `<button class="btn btn-outline-secondary btn-sm quick-reply"
                        style="border-color: rgba(212,175,55,0.4); color: #555; border-radius: 20px; font-size:0.85rem;"
                        data-value="${this.escapeHtml(opt.value)}">${opt.label}</button>`;
        });
        html += '</div>';

        this.chatContainer.innerHTML += html;
        this.scrollToBottom();

        const container = this.chatContainer.lastElementChild;
        container.querySelectorAll('.quick-reply').forEach(btn => {
            btn.addEventListener('click', () => {
                container.querySelectorAll('.quick-reply').forEach(b => {
                    b.disabled = true;
                    b.style.opacity = '0.5';
                });
                btn.style.opacity = '1';
                btn.style.borderColor = '#D4AF37';
                btn.style.color = '#D4AF37';
                if (callback) callback(btn.dataset.value);
            });
        });
    }

    showTypingIndicator() {
        this.chatContainer.innerHTML += `
            <div id="typing-indicator" class="d-flex mb-2">
                <div class="p-2 px-3 rounded-4" style="background:#f8f9fa; border:1px solid rgba(212,175,55,0.2);">
                    <i class="bi bi-robot me-1" style="color:#D4AF37; font-size:0.8rem;"></i>
                    <span style="letter-spacing:3px; color:#D4AF37; font-size:1.2rem;">...</span>
                </div>
            </div>`;
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        document.getElementById('typing-indicator')?.remove();
    }

    scrollToBottom() {
        this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }
}

window.AsesorVirtual = AsesorVirtual;
