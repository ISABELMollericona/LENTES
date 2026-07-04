class FaceAnalyzer {
    constructor() {
        this.video = null;
        this.canvas = null;
        this.ctx = null;
        this.stream = null;
        this.faceMesh = null;
        this.currentLandmarks = null;
        this.scriptsLoaded = false;
        this.isProcessing = false;
    }

    async loadMediaPipeScripts() {
        if (window.FaceMesh) {
            this.scriptsLoaded = true;
            return;
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js';
            script.crossOrigin = 'anonymous';
            script.onload = async () => {
                try {
                    await this.loadDrawingUtils();
                    this.scriptsLoaded = true;
                    resolve();
                } catch (e) {
                    reject(new Error('Error al cargar drawing_utils: ' + e.message));
                }
            };
            script.onerror = () => reject(new Error('No se pudo cargar face_mesh.js desde CDN. Revisa tu conexión a internet.'));
            document.head.appendChild(script);
        });
    }

    loadDrawingUtils() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js';
            script.crossOrigin = 'anonymous';
            script.onload = resolve;
            script.onerror = () => reject(new Error('No se pudo cargar drawing_utils.js desde CDN.'));
            document.head.appendChild(script);
        });
    }

    static async preloadScripts() {
        const temp = new FaceAnalyzer();
        try {
            await temp.loadMediaPipeScripts();
        } catch (e) {
            console.warn('FaceAnalyzer preload failed:', e);
            throw e;
        }
    }

    static async checkCameraSupport() {
        const errors = [];
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            errors.push('API de cámara no disponible');
            return { supported: false, errors };
        }
        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            errors.push('La cámara requiere HTTPS (conexión segura)');
        }
        if (navigator.permissions) {
            try {
                const result = await navigator.permissions.query({ name: 'camera' });
                if (result.state === 'denied') {
                    errors.push('Permiso de cámara denegado. Cambia esto en la configuración de tu navegador.');
                }
            } catch {}
        }
        return { supported: errors.length === 0, errors };
    }

    async initCamera(videoElementId, canvasElementId) {
        this.video = document.getElementById(videoElementId);
        this.canvas = document.getElementById(canvasElementId);
        if (!this.video) throw new Error('Elemento de video no encontrado: ' + videoElementId);
        if (!this.canvas) throw new Error('Elemento de canvas no encontrado: ' + canvasElementId);
        this.ctx = this.canvas.getContext('2d');

        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 640, height: 480, facingMode: 'user' }
            });
            this.video.srcObject = this.stream;
            await this.video.play();
            await this.videoReady();

            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;

            await this.initFaceMesh();
            return { ok: true };
        } catch (err) {
            console.error('initCamera error:', err);
            let message = 'Error al acceder a la cámara';
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                message = 'Permiso de cámara denegado. Permite el acceso en la configuración del navegador o usa "Subir foto".';
            } else if (err.name === 'NotFoundError') {
                message = 'No se encontró una cámara en este dispositivo. Usa "Subir foto" en su lugar.';
            } else if (err.name === 'NotReadableError') {
                message = 'La cámara está siendo usada por otra aplicación. Ciérrala e intenta de nuevo.';
            } else if (err.name === 'OverconstrainedError') {
                message = 'La cámara no soporta la resolución requerida.';
            } else if (err.message && err.message.includes('HTTPS')) {
                message = 'La cámara solo funciona en sitios seguros (HTTPS).';
            } else if (err.message && err.message.includes('CDN')) {
                message = err.message;
            }
            return { ok: false, error: message, errorCode: err.name || 'unknown' };
        }
    }

    videoReady() {
        return new Promise((resolve) => {
            if (this.video.readyState >= 2) {
                resolve();
            } else {
                this.video.addEventListener('loadeddata', resolve, { once: true });
            }
        });
    }

    initFaceMesh() {
        return new Promise((resolve) => {
            this.faceMesh = new FaceMesh({
                locateFile: (file) =>
                    `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
            });

            this.faceMesh.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });

            this.faceMesh.onResults((results) => {
                if (results.multiFaceLandmarks && results.multiFaceLandmarks.length > 0) {
                    this.currentLandmarks = results.multiFaceLandmarks[0];
                }
                this.drawResults(results);
            });

            const processFrame = async () => {
                if (!this.stream) return;
                if (this.video && this.video.readyState >= 2 && !this.isProcessing) {
                    this.isProcessing = true;
                    await this.faceMesh.send({ image: this.video });
                    this.isProcessing = false;
                }
                requestAnimationFrame(processFrame);
            };

            if (this.video.readyState >= 2) {
                processFrame();
                resolve();
            } else {
                this.video.addEventListener('loadeddata', () => {
                    processFrame();
                    resolve();
                }, { once: true });
            }
        });
    }

    drawResults(results) {
        if (!this.ctx) return;
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        if (results.multiFaceLandmarks) {
            for (const landmarks of results.multiFaceLandmarks) {
                if (typeof drawConnectors !== 'undefined') {
                    drawConnectors(this.ctx, landmarks, FACEMESH_TESSELATION, { color: '#C0C0C030', lineWidth: 1 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_RIGHT_EYE, { color: '#FF3030', lineWidth: 2 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_LEFT_EYE, { color: '#30FF30', lineWidth: 2 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_FACE_OVAL, { color: '#D4AF37', lineWidth: 2 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_LIPS, { color: '#FF69B4', lineWidth: 2 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_RIGHT_EYEBROW, { color: '#FFA500', lineWidth: 2 });
                    drawConnectors(this.ctx, landmarks, FACEMESH_LEFT_EYEBROW, { color: '#FFA500', lineWidth: 2 });
                }
            }
        }
    }

    captureImage() {
        if (!this.video || !this.canvas || !this.ctx) return null;

        const captureCanvas = document.createElement('canvas');
        captureCanvas.width = this.video.videoWidth;
        captureCanvas.height = this.video.videoHeight;
        const captureCtx = captureCanvas.getContext('2d');
        captureCtx.drawImage(this.video, 0, 0);
        return captureCanvas.toDataURL('image/jpeg', 0.9);
    }

    getKeyLandmarks(landmarks) {
        if (!landmarks || landmarks.length < 468) return null;

        const toPoint = (lm) => ({ x: lm.x, y: lm.y, z: lm.z });

        return {
            forehead: toPoint(landmarks[10]),
            chin: toPoint(landmarks[152]),
            leftJaw: toPoint(landmarks[172]),
            rightJaw: toPoint(landmarks[397]),
            leftCheek: toPoint(landmarks[50]),
            rightCheek: toPoint(landmarks[280]),
            noseBridge: toPoint(landmarks[1]),
            leftEye: toPoint(landmarks[33]),
            rightEye: toPoint(landmarks[263]),
        };
    }

    estimateFaceShape(landmarks) {
        const key = this.getKeyLandmarks(landmarks);
        if (!key) return null;

        const d2 = (a, b) => Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);

        const faceHeight = d2(key.forehead, key.chin);
        const faceWidth = d2(key.leftJaw, key.rightJaw);
        const cheekWidth = d2(key.leftCheek, key.rightCheek);
        const jawWidth = d2(key.leftJaw, key.rightJaw);

        const ratio = faceHeight / faceWidth;
        const jawCheekRatio = jawWidth / Math.max(cheekWidth, 0.001);

        let shape = 'ovalado';
        let confidence = 70;

        if (ratio < 1.1 && jawCheekRatio > 0.9) {
            shape = 'redondo';
            confidence = 80;
        } else if (ratio < 1.15 && jawCheekRatio > 0.95) {
            shape = 'cuadrado';
            confidence = 75;
        } else if (ratio > 1.3 && jawCheekRatio > 0.85) {
            shape = 'rectangular';
            confidence = 75;
        } else if (ratio < 1.15 && jawCheekRatio < 0.8) {
            shape = 'corazon';
            confidence = 70;
        } else if (jawCheekRatio < 0.75) {
            shape = 'diamante';
            confidence = 70;
        } else if (ratio >= 1.1 && ratio <= 1.3) {
            shape = 'ovalado';
            confidence = 85;
        }

        return { shape, confidence };
    }

    async analyzeImage(imageSrc) {
        if (!this.scriptsLoaded) {
            await this.loadMediaPipeScripts();
        }

        const img = new Image();
        img.crossOrigin = 'anonymous';
        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
            img.src = imageSrc;
        });

        // Instancia separada para no interferir con el stream de cámara
        const fm = new FaceMesh({
            locateFile: (file) =>
                `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
        });
        fm.setOptions({
            maxNumFaces: 1,
            refineLandmarks: true,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });

        try {
            const results = await new Promise((resolve, reject) => {
                const timeout = setTimeout(() => reject(new Error('MediaPipe timeout')), 20000);
                fm.onResults((r) => {
                    clearTimeout(timeout);
                    resolve(r);
                });
                fm.send({ image: img }).catch(reject);
            });

            if (results.multiFaceLandmarks && results.multiFaceLandmarks.length > 0) {
                const landmarks = results.multiFaceLandmarks[0];
                const shapeResult = this.estimateFaceShape(landmarks);
                return {
                    detected: true,
                    landmarks: Array.from(landmarks).map(l => ({ x: l.x, y: l.y, z: l.z })),
                    shape: shapeResult.shape,
                    confidence: shapeResult.confidence,
                };
            }

            return { detected: false, shape: null, confidence: 0, landmarks: null };
        } catch (err) {
            console.error('MediaPipe analysis error:', err);
            return { detected: false, shape: null, confidence: 0, landmarks: null };
        }
    }

    async uploadForAnalysis(imageData) {
        let localResult = null;

        try {
            localResult = await this.analyzeImage(imageData);
        } catch (e) {
            console.warn('Local analysis failed, sending to server:', e);
        }

        if (localResult && localResult.detected && localResult.shape) {
            const formData = new FormData();
            const blob = await fetch(imageData).then(r => r.blob());
            formData.append('imagen', blob, 'face-analysis.jpg');
            formData.append('forma_rostro', localResult.shape);
            formData.append('confianza', localResult.confidence.toString());
            formData.append('analisis_local', '1');

            try {
                const res = await fetch('/api/analisis-facial', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content || ''}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) return data;
            } catch (err) {
                console.error('Server upload error:', err);
            }

            return {
                success: true,
                data: {
                    analisis_id: null,
                    forma_rostro: localResult.shape,
                    confianza: localResult.confidence,
                    tiempo_procesamiento: 0,
                    recomendacion_montura: null
                }
            };
        }

        const formData = new FormData();
        const blob = await fetch(imageData).then(r => r.blob());
        formData.append('imagen', blob, 'face-analysis.jpg');

        try {
            const res = await fetch('/api/analisis-facial', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${document.querySelector('meta[name="api-token"]')?.content || ''}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: formData
            });
            return await res.json();
        } catch (err) {
            console.error('Upload error:', err);
            return { success: false, message: 'Error al procesar la imagen' };
        }
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        this.currentLandmarks = null;
        this.faceMesh = null;
    }
}

window.FaceAnalyzer = FaceAnalyzer;
