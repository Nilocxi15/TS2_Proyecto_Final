@extends('layouts.app')

@section('title', 'Detalle del Ejercicio #' . $ejercicio->id)

@section('content')
    <div class="page">
        <div class="container-fluid px-0">
            <!-- Header con navegación -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('tutor.exercises.index') }}" class="text-grey text-decoration-none">
                                    <i class="fas fa-database me-1"></i>Ejercicios
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-grey-50" aria-current="page">
                                Ejercicio #{{ $ejercicio->id }}
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-grey mb-2">
                        <i class="fas fa-file-alt me-3"></i>Detalle del Ejercicio
                    </h1>
                </div>
                <div>
                    <a href="{{ route('tutor.exercises.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    @if(in_array($ejercicio->estado, ['BORRADOR', 'DESHABILITADO']))
                        <a href="{{ route('tutor.exercises.edit', $ejercicio->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                    @endif
                </div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Columna principal: Información del ejercicio -->
                <div class="col-lg-8">
                    <!-- Estado y acciones rápidas -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary fs-6 me-2">ID: {{ $ejercicio->id }}</span>
                                    @php
                                        $estadoColors = [
                                            'BORRADOR' => 'secondary',
                                            'REVISION' => 'info',
                                            'APROBADO' => 'primary',
                                            'PUBLICADO' => 'success',
                                            'DESHABILITADO' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $estadoColors[$ejercicio->estado] ?? 'secondary' }} fs-6">
                                        <i class="fas fa-circle me-1"></i>
                                        {{ $estados[$ejercicio->estado] ?? $ejercicio->estado }}
                                    </span>
                                </div>
                                <div>
                                    <!-- Formulario para cambiar estado (flujo de aprobación) -->
                                    <form action="{{ route('tutor.exercises.cambiar-estado', $ejercicio->id) }}"
                                        method="POST" class="d-inline" id="estadoForm">
                                        @csrf
                                        @method('PATCH')

                                        @if($ejercicio->estado == 'BORRADOR')
                                            <button type="submit" name="estado" value="REVISION" class="btn btn-info">
                                                <i class="fas fa-paper-plane me-2"></i>Enviar a Revisión
                                            </button>
                                        @endif


                                        @if($ejercicio->estado == 'APROBADO')
                                            <button type="submit" name="estado" value="PUBLICADO" class="btn btn-success">
                                                <i class="fas fa-globe me-2"></i>Publicar Ejercicio
                                            </button>
                                        @endif


                                        @if($ejercicio->estado == 'PUBLICADO')
                                            <button type="submit" name="estado" value="DESHABILITADO" class="btn btn-danger"
                                                onclick="return confirm('¿Deshabilitar este ejercicio? Dejará de estar disponible.')">
                                                <i class="fas fa-ban me-2"></i>Deshabilitar
                                            </button>
                                        @endif

                                        @if($ejercicio->estado == 'DESHABILITADO')
                                            <button type="submit" name="estado" value="BORRADOR" class="btn btn-warning">
                                                <i class="fas fa-undo me-2"></i>Reactivar
                                            </button>
                                        @endif

                                        <input type="hidden" name="revisor_id" value="1">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enunciado -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle text-primary me-2"></i>Enunciado del Problema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="math-preview p-3 bg-light rounded">
                                {!! $ejercicio->enunciado !!}
                            </div>
                            @if($ejercicio->imagen)
                                <div class="mt-3 text-center">
                                    <img src="{{ $ejercicio->imagen }}" alt="Diagrama del ejercicio" class="img-fluid rounded"
                                        style="max-height: 300px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Respuesta Correcta -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>Respuesta Correcta
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-0">
                                <strong>{{ $ejercicio->respuesta_correcta }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Solución Paso a Paso -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-list-ol text-primary me-2"></i>Solución Paso a Paso
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="math-preview p-3 bg-light rounded">
                                {!! nl2br(e($ejercicio->solucion)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Explicación Conceptual -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap text-info me-2"></i>Explicación Conceptual
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="math-preview p-3 bg-light rounded">
                                {!! nl2br(e($ejercicio->explicacion)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Ejercicios Relacionados -->
                    @if($ejercicio->relacionados && $ejercicio->relacionados->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header bg-white border-0 pt-4">
                                <h5 class="mb-0">
                                    <i class="fas fa-link text-primary me-2"></i>Ejercicios Relacionados
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    @foreach($ejercicio->relacionados as $relacionado)
                                        <a href="{{ route('tutor.exercises.show', $relacionado->id) }}"
                                            class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>#{{ $relacionado->id }}</strong>
                                                    <span class="text-muted"> - </span>
                                                    {{ Str::limit($relacionado->enunciado, 100) }}
                                                </div>
                                                <span class="badge bg-{{ $difColors[$relacionado->dificultad] ?? 'secondary' }}">
                                                    {{ $dificultades[$relacionado->dificultad] ?? $relacionado->dificultad }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Columna derecha: Metadatos e información adicional -->
                <div class="col-lg-4">
                    <!-- Información básica -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>Información del Ejercicio
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 40%">
                                        <i class="fas fa-book me-2"></i>Módulo:
                                    </td>
                                    <td class="fw-bold">{{ $ejercicio->modulo->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-tag me-2"></i>Subtema:
                                    </td>
                                    <td class="fw-bold">{{ $ejercicio->subtema->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-chart-line me-2"></i>Dificultad:
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $difColors[$ejercicio->dificultad] ?? 'secondary' }}">
                                            {{ $dificultades[$ejercicio->dificultad] ?? $ejercicio->dificultad }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-question-circle me-2"></i>Tipo:
                                    </td>
                                    <td>{{ $tipos[$ejercicio->tipo] ?? $ejercicio->tipo }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-hourglass-half me-2"></i>Tiempo estimado:
                                    </td>
                                    <td>{{ $ejercicio->tiempo_estimado }} minutos</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Auditoría -->
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-history text-primary me-2"></i>Auditoría
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-user-plus me-2"></i>Creado por:
                                    </td>
                                    <td>
                                        {{ $ejercicio->creador->nombre ?? 'Usuario #' . $ejercicio->creado_por }}
                                        {{ $ejercicio->creador->apellido ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-calendar-alt me-2"></i>Fecha creación:
                                    </td>
                                    <td>{{ $ejercicio->created_at ? $ejercicio->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                                @if($ejercicio->revisado_por)
                                    <tr>
                                        <td class="text-muted">
                                            <i class="fas fa-user-check me-2"></i>Revisado por:
                                        </td>
                                        <td>
                                            {{ $ejercicio->revisor->nombre ?? 'Usuario #' . $ejercicio->revisado_por }}
                                        </td>
                                    </tr>
                                @endif
                                @if($ejercicio->estado == 'PUBLICADO' && $ejercicio->updated_at)
                                    <tr>
                                        <td class="text-muted">
                                            <i class="fas fa-clock me-2"></i>Última actualización:
                                        </td>
                                        <td>{{ $ejercicio->updated_at ? $ejercicio->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Estadísticas de uso (si hay datos) -->
                    <div class="card">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar text-primary me-2"></i>Estadísticas de Uso
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalRespuestas = $ejercicio->respuestasUsuario->count();
                                $respuestasCorrectas = $ejercicio->respuestasUsuario->where('es_correcta', true)->count();
                                $tasaAciertos = $totalRespuestas > 0 ? round(($respuestasCorrectas / $totalRespuestas) * 100, 1) : 0;
                            @endphp
                            <div class="text-center mb-3">
                                <div class="display-4 fw-bold text-primary">{{ $tasaAciertos }}%</div>
                                <p class="text-muted">Tasa de aciertos</p>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $tasaAciertos }}%"></div>
                            </div>
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-muted">Total de intentos:</td>
                                    <td class="fw-bold">{{ $totalRespuestas }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Respuestas correctas:</td>
                                    <td class="fw-bold text-success">{{ $respuestasCorrectas }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Respuestas incorrectas:</td>
                                    <td class="fw-bold text-danger">{{ $totalRespuestas - $respuestasCorrectas }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
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
            font-size: 1.1rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .table-borderless td {
            padding: 8px 0;
        }

        .progress {
            border-radius: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Renderizar matemáticas después de cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            if (window.MathJax) {
                MathJax.typesetPromise();
            }
        });

        // Confirmación antes de cambiar estado crítico
        const estadoForm = document.getElementById('estadoForm');
        if (estadoForm) {
            estadoForm.addEventListener('submit', function (e) {
                const submitter = e.submitter;
                if (submitter && submitter.value === 'DESHABILITADO') {
                    if (!confirm('¿Estás seguro de que quieres DESHABILITAR este ejercicio?\n\nLos estudiantes ya no podrán verlo en práctica ni simulacros.')) {
                        e.preventDefault();
                    }
                }
                if (submitter && submitter.value === 'REVISION') {
                    if (!confirm('Enviar este ejercicio a revisión?\n\nUn revisor evaluará el contenido antes de su publicación.')) {
                        e.preventDefault();
                    }
                }
            });
        }
    </script>
@endpush