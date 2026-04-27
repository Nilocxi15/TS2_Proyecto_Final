@extends('layouts.app')
@section('title', 'Resultados del Simulacro')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary);">Resultados del Simulacro</h2>
        <a href="{{ route('student.mock-exams') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver al Historial
        </a>
    </div>

    <div class="row mb-4">
        <!-- Resumen General -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: none; overflow: hidden;">
                <div class="card-body text-center p-5 d-flex flex-column justify-content-center">
                    <h5 class="text-muted mb-3">Puntaje Obtenido</h5>
                    <div class="display-1 fw-bold {{ $simulacro->puntaje >= $puntajeAprobacion ? 'text-success' : 'text-danger' }}">
                        {{ $simulacro->puntaje }}%
                    </div>
                    <p class="mt-3 text-muted">
                        Puntaje mínimo de aprobación de referencia: <strong>{{ $puntajeAprobacion }}%</strong>
                    </p>
                    
                    @if($simulacro->puntaje >= $puntajeAprobacion)
                        <div class="alert alert-success mt-3" style="border-radius: 12px;">
                            <i class="bi bi-trophy-fill me-2"></i> ¡Excelente desempeño!
                        </div>
                    @else
                        <div class="alert alert-danger mt-3" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> Necesitas más preparación. Revisa tus errores.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Desglose por Módulo -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100" style="border-radius: 16px; border: none;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Desglose por Módulo Temático</h5>
                    <div class="row g-3">
                        @foreach($desgloseModulos as $mod)
                        <div class="col-md-6">
                            <div class="p-3 border rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-truncate" style="max-width: 70%;" title="{{ $mod['nombre'] }}">{{ $mod['nombre'] }}</span>
                                    <span class="badge {{ $mod['porcentaje'] >= 80 ? 'bg-success' : ($mod['porcentaje'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $mod['porcentaje'] }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $mod['porcentaje'] >= 80 ? 'bg-success' : ($mod['porcentaje'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $mod['porcentaje'] }}%"></div>
                                </div>
                                <div class="text-end mt-1 text-muted" style="font-size: 0.8rem;">
                                    {{ $mod['correctas'] }} / {{ $mod['total'] }} correctas
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preguntas Incorrectas -->
    @if($incorrectas->count() > 0)
    <div class="card shadow-sm mt-5" style="border-radius: 16px; border: none;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h4 class="fw-bold" style="color: var(--error);">
                <i class="bi bi-x-circle-fill me-2"></i>Preguntas Incorrectas o No Respondidas
            </h4>
            <p class="text-muted">Revisa las soluciones paso a paso para mejorar en estas áreas.</p>
        </div>
        <div class="card-body p-4">
            <div class="accordion" id="accordionErrores">
                @foreach($incorrectas as $index => $pregunta)
                    @php $ej = $pregunta->ejercicio; @endphp
                    <div class="accordion-item mb-3" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                <div>
                                    <span class="badge bg-secondary me-2">{{ $ej->modulo->nombre ?? 'General' }}</span>
                                    <strong>Ejercicio {{ $index + 1 }}</strong>
                                    @if(is_null($pregunta->es_correcta))
                                        <span class="badge bg-warning text-dark ms-2">No respondida</span>
                                    @endif
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#accordionErrores">
                            <div class="accordion-body p-4 bg-light">
                                <div class="mb-4">
                                    <h6 class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem;">Enunciado:</h6>
                                    <div class="fs-5 math-preview">{!! nl2br(e($ej->enunciado)) !!}</div>
                                    @if($ej->imagen)
                                        <div class="mt-3">
                                            <img src="{{ str_starts_with($ej->imagen, 'http') ? $ej->imagen : asset('storage/' . $ej->imagen) }}" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem;">Tu Respuesta:</h6>
                                        <div class="p-3 bg-white border rounded text-danger">
                                            {{ $pregunta->respuesta ?? 'Ninguna (Tiempo agotado)' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted text-uppercase" style="font-size: 0.8rem;">Respuesta Correcta:</h6>
                                        <div class="p-3 bg-white border rounded text-success">
                                            {{ $ej->respuesta_correcta }}
                                        </div>
                                    </div>
                                </div>

                                @if($ej->solucion || $ej->explicacion)
                                <div class="mt-4 p-4" style="background-color: #f8fafc; border-left: 4px solid var(--secondary); border-radius: 0 8px 8px 0;">
                                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.8rem;">Solución Paso a Paso:</h6>
                                    @if($ej->explicacion)
                                        <div class="mb-2 text-muted math-preview">{!! nl2br(e($ej->explicacion)) !!}</div>
                                    @endif
                                    @if($ej->solucion)
                                        <div class="math-preview">{!! nl2br(e($ej->solucion)) !!}</div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.MathJax) {
            MathJax.typesetPromise().catch((err) => console.log(err));
        }
    });
</script>
@endsection
