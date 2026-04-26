@extends('layouts.app')

@section('title', 'Editar Ejercicio #' . $ejercicio->id)

@section('content')
<div class="page-two-columns">
    <!-- Columna izquierda: Formulario -->
    <div class="card">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h3 class="mb-0">
                <i class="fas fa-edit text-warning me-2"></i>Editar Ejercicio #{{ $ejercicio->id }}
            </h3>
            <p class="text-muted mt-2">Modifica los campos que necesites actualizar</p>
            <div class="alert alert-info mb-0 mt-2">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Estado actual:</strong>
                <span class="badge bg-{{ $ejercicio->estado == 'BORRADOR' ? 'secondary' : 'warning' }}">
                    {{ $ejercicio->estado == 'BORRADOR' ? 'Borrador' : 'Deshabilitado' }}
                </span>
                @if($ejercicio->estado == 'DESHABILITADO')
                    <span class="ms-2">Al guardar, el ejercicio seguirá deshabilitado. Deberás reactivarlo desde la vista de detalle.</span>
                @endif
            </div>
        </div>

        <div class="card-body">
            <!-- Alertas -->
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    @if(session('duplicados') && session('duplicados')->count() > 0)
                        <hr>
                        <strong>Ejercicios similares encontrados:</strong>
                        <ul class="mt-2">
                            @foreach(session('duplicados') as $duplicado)
                                <li>
                                    <a href="{{ route('tutor.exercises.show', $duplicado->id) }}" target="_blank">
                                        Ejercicio #{{ $duplicado->id }}
                                    </a>:
                                    {{ Str::limit($duplicado->enunciado, 100) }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mt-2 mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('tutor.exercises.update', $ejercicio->id) }}" method="POST" id="ejercicioForm">
                @csrf
                @method('PUT')

                <!-- Módulo y Subtema -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-book me-1 text-primary"></i>Módulo *
                        </label>
                        <select name="modulo_id" class="form-select @error('modulo_id') is-invalid @enderror"
                                id="modulo_id" required>
                            <option value="">Seleccione un módulo</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo->id }}" {{ old('modulo_id', $ejercicio->modulo_id) == $modulo->id ? 'selected' : '' }}>
                                    {{ $modulo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('modulo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-tag me-1 text-primary"></i>Subtema *
                        </label>
                        <select name="subtema_id" id="subtema_id" class="form-select @error('subtema_id') is-invalid @enderror" required>
                            <option value="">Seleccione un subtema</option>
                            @if($ejercicio->subtema_id)
                                @php
                                    $subtemasDelModulo = $ejercicio->modulo ? $ejercicio->modulo->subtemas : collect();
                                @endphp
                                @foreach($subtemasDelModulo as $subtema)
                                    <option value="{{ $subtema->id }}" {{ old('subtema_id', $ejercicio->subtema_id) == $subtema->id ? 'selected' : '' }}>
                                        {{ $subtema->nombre }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('subtema_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dificultad y Tipo -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-chart-line me-1 text-primary"></i>Nivel de Dificultad *
                        </label>
                        <select name="dificultad" class="form-select @error('dificultad') is-invalid @enderror" required>
                            <option value="">Seleccione dificultad</option>
                            @foreach($dificultades as $key => $nombre)
                                <option value="{{ $key }}" {{ old('dificultad', $ejercicio->dificultad) == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('dificultad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-question-circle me-1 text-primary"></i>Tipo de Ejercicio *
                        </label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccione tipo</option>
                            @foreach($tipos as $key => $nombre)
                                <option value="{{ $key }}" {{ old('tipo', $ejercicio->tipo) == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Enunciado -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file-alt me-1 text-primary"></i>Enunciado del Problema *
                    </label>
                    <textarea name="enunciado" rows="4"
                              class="form-control math-field @error('enunciado') is-invalid @enderror"
                              placeholder="Ej: Resuelve la siguiente ecuación: $$x^2 - 5x + 6 = 0$$"
                              required>{{ old('enunciado', $ejercicio->enunciado) }}</textarea>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Puedes usar LaTeX entre $$ $$ para fórmulas matemáticas
                    </small>
                    @error('enunciado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Previsualización del enunciado -->
                <div class="mb-3 math-preview" id="enunciadoPreview" style="{{ old('enunciado', $ejercicio->enunciado) ? 'display: block;' : 'display: none;' }}">
                    <strong>Vista previa:</strong>
                    <div id="previewContent">{{ old('enunciado', $ejercicio->enunciado) }}</div>
                </div>

                <!-- Imagen (opcional) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-image me-1 text-primary"></i>Imagen/Diagrama (opcional)
                    </label>
                    <input type="url" name="imagen" class="form-control @error('imagen') is-invalid @enderror"
                           placeholder="https://ejemplo.com/imagen.jpg" value="{{ old('imagen', $ejercicio->imagen) }}">
                    <small class="text-muted">URL de una imagen de apoyo para el ejercicio</small>
                    @error('imagen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Respuesta Correcta -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-check-circle me-1 text-success"></i>Respuesta Correcta *
                    </label>
                    <input type="text" name="respuesta_correcta"
                           class="form-control @error('respuesta_correcta') is-invalid @enderror"
                           placeholder="Ej: x = 2, x = 3"
                           value="{{ old('respuesta_correcta', $ejercicio->respuesta_correcta) }}" required>
                    @error('respuesta_correcta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Solución Paso a Paso -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-list-ol me-1 text-primary"></i>Solución Paso a Paso *
                    </label>
                    <textarea name="solucion" rows="5"
                              class="form-control math-field @error('solucion') is-invalid @enderror"
                              placeholder="1. Identificamos que es una ecuación cuadrática&#10;2. Aplicamos la fórmula general...&#10;3. Calculamos las raíces..."
                              required>{{ old('solucion', $ejercicio->solucion) }}</textarea>
                    <small class="text-muted">Explicación detallada de cada paso para resolver el ejercicio</small>
                    @error('solucion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Explicación Conceptual -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-graduation-cap me-1 text-primary"></i>Explicación Conceptual *
                    </label>
                    <textarea name="explicacion" rows="4"
                              class="form-control math-field @error('explicacion') is-invalid @enderror"
                              placeholder="Explica la teoría detrás del ejercicio: qué concepto se aplica, por qué funciona, etc."
                              required>{{ old('explicacion', $ejercicio->explicacion) }}</textarea>
                    <small class="text-muted">Material de apoyo que explica el tema relacionado</small>
                    @error('explicacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tiempo Estimado -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-hourglass-half me-1 text-primary"></i>Tiempo Estimado (minutos) *
                    </label>
                    <input type="number" name="tiempo_estimado"
                           class="form-control @error('tiempo_estimado') is-invalid @enderror"
                           placeholder="5" min="1" max="30"
                           value="{{ old('tiempo_estimado', $ejercicio->tiempo_estimado) }}" required>
                    @error('tiempo_estimado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ejercicios Relacionados -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-link me-1 text-primary"></i>Ejercicios Relacionados (opcional)
                    </label>
                    <select name="relacionados[]" id="relacionados" class="form-select" multiple size="5">
                        <option value="">Cargando ejercicios...</option>
                    </select>
                    <small class="text-muted">Ctrl+Click para seleccionar múltiples ejercicios relacionados</small>
                    @error('relacionados')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('tutor.exercises.show', $ejercicio->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Actualizar Ejercicio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Columna derecha: Guía y consejos -->
    <div class="card">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h4 class="mb-0">
                <i class="fas fa-lightbulb text-warning me-2"></i>Consejos de Edición
            </h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Notas importantes:</strong>
            </div>

            <ul class="text-muted mb-3">
                <li class="mb-2">✏️ Solo puedes editar ejercicios en estado <strong>Borrador</strong> o <strong>Deshabilitado</strong></li>
                <li class="mb-2">📝 Los cambios no afectan las respuestas de estudiantes que ya hayan resuelto el ejercicio</li>
                <li class="mb-2">🔄 Si necesitas cambiar el estado, ve a la <strong>vista de detalle</strong></li>
                <li class="mb-2">⚠️ Al editar un ejercicio deshabilitado, seguirá deshabilitado hasta que lo reactives</li>
            </ul>

            <h6 class="mt-3"><i class="fas fa-chart-line me-2 text-primary"></i>Niveles de Dificultad</h6>
            <ul class="text-muted mb-3">
                <li><strong>Básico:</strong> Ejercicios simples de un solo paso</li>
                <li><strong>Intermedio:</strong> Requiere 2-3 pasos o combinación de conceptos</li>
                <li><strong>Avanzado:</strong> Múltiples pasos, requiere análisis profundo</li>
                <li><strong>Examen Real:</strong> Mismo nivel que el examen de admisión USAC</li>
            </ul>

            <h6 class="mt-3"><i class="fas fa-code me-2 text-primary"></i>Usando LaTeX</h6>
            <div class="bg-light p-2 rounded">
                <code>$$x^2 + 2x + 1 = 0$$</code> → $$x^2 + 2x + 1 = 0$$<br>
                <code>$$\frac{a}{b}$$</code> → $$\frac{a}{b}$$<br>
                <code>$$\sqrt{x}$$</code> → $$\sqrt{x}$$
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .math-preview {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }
    select[multiple] {
        min-height: 150px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Variables globales
    let ejerciciosRelacionadosIds = {{ json_encode($ejercicio->relacionados->pluck('id')) }};

    // Cargar subtemas según el módulo seleccionado
    document.getElementById('modulo_id').addEventListener('change', function() {
        const moduloId = this.value;
        const subtemaSelect = document.getElementById('subtema_id');

        if (!moduloId) {
            subtemaSelect.innerHTML = '<option value="">Primero seleccione un módulo</option>';
            return;
        }

        // Mostrar loading
        subtemaSelect.innerHTML = '<option value="">Cargando subtemas...</option>';

        // Obtener subtemas vía AJAX
        fetch(`/subtemas/${moduloId}`)
            .then(response => response.json())
            .then(data => {
                subtemaSelect.innerHTML = '<option value="">Seleccione un subtema</option>';
                const currentSubtemaId = {{ $ejercicio->subtema_id }};
                data.forEach(subtema => {
                    const selected = (currentSubtemaId == subtema.id) ? 'selected' : '';
                    subtemaSelect.innerHTML += `<option value="${subtema.id}" ${selected}>${subtema.nombre}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                subtemaSelect.innerHTML = '<option value="">Error al cargar subtemas</option>';
            });
    });

    // Previsualizar enunciado
    const enunciadoTextarea = document.querySelector('textarea[name="enunciado"]');
    const previewDiv = document.getElementById('enunciadoPreview');
    const previewContent = document.getElementById('previewContent');

    enunciadoTextarea.addEventListener('input', function() {
        const value = this.value;
        if (value.trim()) {
            previewDiv.style.display = 'block';
            previewContent.innerHTML = value;
            if (window.MathJax) {
                MathJax.typesetPromise([previewContent]);
            }
        } else {
            previewDiv.style.display = 'none';
        }
    });

    // Disparar evento inicial si hay valor
    if (enunciadoTextarea.value.trim()) {
        enunciadoTextarea.dispatchEvent(new Event('input'));
    }

    // Cargar ejercicios existentes para relacionados
    function cargarEjerciciosRelacionados() {
        const relacionadosSelect = document.getElementById('relacionados');
        fetch('/ejercicios-publicados')
            .then(response => response.json())
            .then(data => {
                relacionadosSelect.innerHTML = '';
                if (data.length === 0) {
                    relacionadosSelect.innerHTML = '<option value="">No hay ejercicios publicados disponibles</option>';
                } else {
                    data.forEach(ejercicio => {
                        const selected = ejerciciosRelacionadosIds.includes(ejercicio.id) ? 'selected' : '';
                        relacionadosSelect.innerHTML += `<option value="${ejercicio.id}" ${selected}>
                            #${ejercicio.id} - ${ejercicio.modulo?.nombre || 'Sin módulo'} - ${ejercicio.enunciado.substring(0, 50)}...
                        </option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                relacionadosSelect.innerHTML = '<option value="">Error al cargar ejercicios</option>';
            });
    }

    // Cargar ejercicios relacionados al iniciar
    cargarEjerciciosRelacionados();

    // Si el módulo ya tiene un valor seleccionado y es diferente al actual, cargar subtemas
    const moduloActual = {{ $ejercicio->modulo_id }};
    if (document.getElementById('modulo_id').value != moduloActual) {
        document.getElementById('modulo_id').dispatchEvent(new Event('change'));
    }
</script>
@endpush
