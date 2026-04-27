/**
 * practice.js
 * Maneja la l\u00f3gica interactiva del modulo de pr\u00e1ctica, diagn\u00f3stico y simulacros
 */

class EjercicioRenderer {
    static renderInputs(tipo, containerId) {
        const elArea = document.getElementById(containerId);
        if (!elArea) return;
        elArea.innerHTML = '';

        if (tipo === 'OPCION_MULTIPLE') {
            elArea.innerHTML = `
                <div class="mb-2 text-start">
                    <label class="form-label fw-bold text-muted">Ingresa el texto exacto de la opci\u00f3n correcta:</label>
                    <input type="text" class="form-control form-control-lg bg-light" id="ansVal" placeholder="Tu respuesta..." autocomplete="off">
                </div>
            `;
        }
        else if (tipo === 'VF') {
            elArea.innerHTML = `
                <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-3">
                    <input type="hidden" id="ansVal" value="">
                    <button class="btn btn-outline-success btn-lg px-5 fw-bold btn-vf" data-val="VERDADERO">VERDADERO</button>
                    <button class="btn btn-outline-danger btn-lg px-5 fw-bold btn-vf" data-val="FALSO">FALSO</button>
                </div>
            `;

            // Asignar eventos VERDADERO/FALSO
            document.querySelectorAll('.btn-vf').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.btn-vf').forEach(b => b.classList.remove('active', 'border-3'));
                    e.target.classList.add('active', 'border-3');
                    document.getElementById('ansVal').value = e.target.getAttribute('data-val');
                    // Disparar evento dinamico para avisar de cambio
                    document.getElementById('ansVal').dispatchEvent(new Event('input'));
                });
            });
        }
        else if (tipo === 'NUMERICO') {
            elArea.innerHTML = `
                <div class="mb-2 text-start">
                    <label class="form-label fw-bold text-muted">Ingresa tu respuesta num\u00e9rica:</label>
                    <input type="number" step="any" class="form-control form-control-lg bg-light w-50" id="ansVal" placeholder="0.0" autocomplete="off">
                </div>
            `;
        }
        else if (tipo === 'COMPLETAR') {
            elArea.innerHTML = `
                <div class="mb-2 text-start">
                    <label class="form-label fw-bold text-muted">Ingresa los t\u00e9rminos separados por coma:</label>
                    <input type="text" class="form-control form-control-lg bg-light" id="ansVal" placeholder="Ej: x, y, -2, verdadero" autocomplete="off">
                </div>
            `;
        }
    }
}

class SessionManager {
    constructor(config) {
        this.ejercicios = config.ejercicios || [];
        this.sesionId = config.sesionId || null;
        this.routeAnswer = config.routeAnswer || '';
        this.csrfToken = config.csrfToken || '';
        this.modo = config.modo || 'PRACTICA'; // PRACTICA, SIMULACRO, DIAGNOSTICO

        this.currentIndex = 0;
        this.timerSegundos = 0;
        this.timerInterval = null;
        this.startTimeQuestion = null;

        this.difColors = {
            'BASICO': 'success',
            'INTERMEDIO': 'warning',
            'AVANZADO': 'danger',
            'EXAMEN': 'dark'
        };

        this.bindElements();
        this.init();
    }

    bindElements() {
        this.elTimer = document.getElementById('timerDisplay');
        this.elProgress = document.getElementById('progressBar');

        this.elEnunciado = document.getElementById('enunciadoContainer');
        this.elImagenCont = document.getElementById('imagenContainer');
        this.elImagen = document.getElementById('exerciseImg');
        this.elModuloSub = document.getElementById('lblModuloSubtema');
        this.elBadgeDif = document.getElementById('badgeDif');

        this.btnComprobar = document.getElementById('btnComprobar');
        this.btnContinuar = document.getElementById('btnContinuar');
        this.btnFinalizar = document.getElementById('btnFinalizar');

        this.pnlFeedback = document.getElementById('feedbackPanel');
        this.bdFeedback = document.getElementById('feedbackBody');
        this.icoFeedback = document.getElementById('feedbackIcon');
        this.titFeedback = document.getElementById('feedbackTitle');
        this.expFeedback = document.getElementById('feedbackExplicacion');
        this.solFeedback = document.getElementById('feedbackSolucion');
        this.resCorrectaFeedback = document.getElementById('feedbackRespuestaCorrecta');
    }

    init() {
        if (this.ejercicios.length === 0) {
            document.getElementById('finishForm').submit();
            return;
        }

        // Timer
        this.timerInterval = setInterval(() => {
            this.timerSegundos++;
            const m = Math.floor(this.timerSegundos / 60).toString().padStart(2, '0');
            const s = (this.timerSegundos % 60).toString().padStart(2, '0');
            if (this.elTimer) this.elTimer.innerText = `${m}:${s}`;
        }, 1000);

        // Bind events to context
        if (this.btnComprobar) {
            this.btnComprobar.addEventListener('click', () => this.comprobarRespuesta());
        }

        if (this.btnContinuar) {
            this.btnContinuar.addEventListener('click', () => this.siguienteEjercicio());
        }

        if (this.btnFinalizar) {
            this.btnFinalizar.addEventListener('click', () => {
                document.getElementById('finishForm').submit();
            });
        }

        this.renderEjercicio(0);
    }

    renderEjercicio(index) {
        if (index >= this.ejercicios.length) {
            document.getElementById('finishForm').submit();
            return;
        }

        this.startTimeQuestion = new Date();
        const ej = this.ejercicios[index];

        if (this.elProgress) {
            const pct = (index / this.ejercicios.length) * 100;
            this.elProgress.style.width = `${pct}%`;
        }

        if (this.elModuloSub) {
            this.elModuloSub.innerText = `${ej.modulo || 'General'} • ${ej.subtema || 'General'}`;
        }
        if (this.elBadgeDif) {
            const cClass = this.difColors[ej.dificultad] || 'primary';
            this.elBadgeDif.className = `badge bg-${cClass}`;
            this.elBadgeDif.innerText = ej.dificultad || 'NORMAL';
        }

        if (this.elEnunciado) {
            this.elEnunciado.innerHTML = ej.enunciado.replace(/\\n/g, '<br>');
        }

        // === CORRECCIÓN DE IMAGEN ===
        if (this.elImagenCont && this.elImagen) {
            if (ej.imagen && ej.imagen.trim() !== '') {
                this.elImagenCont.classList.remove('d-none');
                // Verificar si es URL externa o ruta local
                if (ej.imagen.startsWith('http://') || ej.imagen.startsWith('https://')) {
                    this.elImagen.src = ej.imagen;  // URL externa directa
                } else {
                    this.elImagen.src = `/storage/${ej.imagen}`;  // Ruta local
                }
            } else {
                this.elImagenCont.classList.add('d-none');
                this.elImagen.src = '';
            }
        }

        EjercicioRenderer.renderInputs(ej.tipo, 'answerArea');
        this.bindInputDinamico();

        this.btnComprobar.classList.remove('d-none');
        this.btnComprobar.disabled = true;
        this.btnContinuar.classList.add('d-none');
        this.btnFinalizar.classList.add('d-none');

        if (this.pnlFeedback) {
            this.pnlFeedback.classList.add('d-none');
        }

        if (window.MathJax) {
            setTimeout(() => {
                MathJax.typesetPromise([this.elEnunciado, document.getElementById('answerArea')]).catch((err) => console.log(err));
            }, 100);
        }
    }

    bindInputDinamico() {
        const inp = document.getElementById('ansVal');
        if (inp) {
            inp.addEventListener('input', () => {
                this.btnComprobar.disabled = (inp.value.trim() === '');
            });
        }
    }

    async comprobarRespuesta() {
        const ej = this.ejercicios[this.currentIndex];
        const res = document.getElementById('ansVal').value.trim();
        const sec = Math.floor((new Date() - this.startTimeQuestion) / 1000);

        this.btnComprobar.disabled = true;
        this.btnComprobar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> VALIDANDO...';

        try {
            const rq = await fetch(this.routeAnswer, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({
                    ejercicio_id: ej.id,
                    respuesta: res,
                    tiempo: sec
                })
            });

            const ds = await rq.json();
            this.mostrarFeedback(ds);

        } catch (error) {
            console.error("Error validando", error);
            this.btnComprobar.disabled = false;
            this.btnComprobar.innerHTML = 'COMPROBAR';
            alert("Ocurri\u00f3 un error conectando al servidor.");
        }
    }

    mostrarFeedback(payload) {
        this.btnComprobar.classList.add('d-none');
        this.btnComprobar.innerHTML = 'COMPROBAR';

        const inp = document.getElementById('ansVal');
        if (inp) inp.disabled = true;
        document.querySelectorAll('.btn-vf').forEach(b => b.disabled = true);

        if (!this.pnlFeedback) return;

        this.pnlFeedback.classList.remove('d-none');

        if (payload.es_correcta) {
            this.bdFeedback.style.backgroundColor = '#f0fdf4';
            this.bdFeedback.style.border = '2px solid #22c55e';
            this.icoFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
            this.titFeedback.innerText = '\u00a1Correcto!';
            this.titFeedback.className = 'fw-bold mb-2 text-success';
        } else {
            this.bdFeedback.style.backgroundColor = '#fef2f2';
            this.bdFeedback.style.border = '2px solid #ef4444';
            this.icoFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
            this.titFeedback.innerText = 'Respuesta incorrecta';
            this.titFeedback.className = 'fw-bold mb-2 text-danger';
        }

        if (this.modo === 'PRACTICA') {
            this.resCorrectaFeedback.innerHTML = `<strong>Respuesta esperada:</strong> ${payload.respuesta_correcta}`;

            if (payload.explicacion || payload.solucion) {
                document.getElementById('feedbackDivider').classList.remove('d-none');
                this.expFeedback.innerHTML = payload.explicacion ? payload.explicacion.replace(/\\n/g, '<br>') : '';
                this.solFeedback.innerHTML = payload.solucion ? payload.solucion.replace(/\\n/g, '<br>') : '';

                if (window.MathJax) {
                    MathJax.typesetPromise([this.expFeedback, this.solFeedback]).catch((err) => console.log(err));
                }
            } else {
                document.getElementById('feedbackDivider').classList.add('d-none');
                this.expFeedback.innerHTML = '';
                this.solFeedback.innerHTML = '';
            }
        } else {
            // Simulacros o diag: usualmente no se muestra respuesta correcta de inmediato
            this.resCorrectaFeedback.innerHTML = "Respuesta guardada. Ver\u00e1s tu nota al final.";
        }

        if (this.currentIndex < this.ejercicios.length - 1) {
            this.btnContinuar.classList.remove('d-none');
        } else {
            if (this.elProgress) this.elProgress.style.width = `100%`;
            this.btnFinalizar.classList.remove('d-none');
        }
    }

    siguienteEjercicio() {
        this.currentIndex++;
        this.renderEjercicio(this.currentIndex);
    }
}
