@extends('layouts.app')

@section('title', 'Revisar Ejercicio #' . $ejercicio->id)

@section('content')
    <div class="page">
        <div class="container-fluid px-0">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('moderador.exercises.revisions') }}" class="text-decoration-none">
                                    <i class="fas fa-clipboard-list me-1"></i>Revisiones
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Ejercicio #{{ $ejercicio->id }}</li>
                        </ol>
                    </nav>
                    <h1 class="text-grey mb-2">
                        <i class="fas fa-file-alt me-3"></i>Revisar Ejercicio
                    </h1>
                </div>
                <div>
                    <a href="{{ route('moderador.exercises.revisions') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
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

            <!-- Advertencia de duplicados -->
            <div id="duplicados-container" style="display: none;">
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Ejercicios similares detectados</strong>
                    </div>
                    <div class="card-body" id="duplicados-list">
                        <p class="text-muted">Buscando ejercicios similares...</p>
                    </div>
                </div>
            </div>

            <!-- Botones de acción principales -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center gap-3">
                        <form action="{{ route('moderador.exercises.approve', $ejercicio->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-lg px-5"
                                onclick="return confirm('¿Aprobar este ejercicio? Pasará a estado APROBADO.')">
                                <i class="fas fa-check-circle me-2"></i>Aprobar Ejercicio
                            </button>
                        </form>

                        <form action="{{ route('moderador.exercises.reject', $ejercicio->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-lg px-5"
                                onclick="return confirm('¿Rechazar este ejercicio? Volverá a BORRADOR.')">
                                <i class="fas fa-times-circle me-2"></i>Rechazar Ejercicio
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Columna principal -->
                <div class="col-lg-8">
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
                                    <img src="{{ $ejercicio->imagen }}" alt="Diagrama" class="img-fluid rounded"
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

                    <!-- Solución -->
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
                </div>

                <!-- Columna derecha: Metadatos -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pt-4">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>Información
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted">Módulo</th>
                                    <td>{{ $ejercicio->modulo->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Subtema</th>
                                    <td>{{ $ejercicio->subtema->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Dificultad</th>
                                    <td>{{ $ejercicio->dificultad }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tipo</th>
                                    <td>{{ $ejercicio->tipo }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tiempo estimado</th>
                                    <td>{{ $ejercicio->tiempo_estimado }} min</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Creado por</th>
                                    <td>{{ $ejercicio->creador->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Fecha</th>
                                    <td>{{ $ejercicio->created_at ? $ejercicio->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function buscarDuplicados() {
            const ejercicioId = {{ $ejercicio->id }};
            const container = document.getElementById('duplicados-container');
            const listContainer = document.getElementById('duplicados-list');

            console.log('=== BUSCANDO DUPLICADOS ===');
            console.log('Ejercicio ID:', ejercicioId);

            listContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Buscando ejercicios similares...</div>';

            try {
                const response = await fetch(`/ejercicios/${ejercicioId}/duplicados`);
                const data = await response.json();

                console.log('Respuesta del servidor:', data);
                console.log('Cantidad de duplicados:', data.length);

                if (data.length > 0) {
                    console.log('DUPLICADOS ENCONTRADOS - Mostrando alerta');
                    container.style.display = 'block';
                    listContainer.innerHTML = '';

                    listContainer.innerHTML += `
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Se encontraron ${data.length} ejercicio(s) similar(es):</strong>
                        </div>
                    `;

                    data.forEach(duplicado => {
                        listContainer.innerHTML += `
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="/tutor/exercises/${duplicado.id}" target="_blank" class="fw-bold">
                                            Ejercicio #${duplicado.id}
                                        </a>
                                        <p class="text-muted small mb-0 mt-1">${duplicado.enunciado.substring(0, 150)}...</p>
                                    </div>
                                    <span class="badge bg-${duplicado.estado === 'PUBLICADO' ? 'success' : 'primary'}">
                                        ${duplicado.estado}
                                    </span>
                                </div>
                            </div>
                        `;
                    });

                    listContainer.innerHTML += `
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('duplicados-container').style.display='none'">
                                <i class="fas fa-times me-1"></i>Ocultar
                            </button>
                        </div>
                    `;


                } else {
                    console.log('NO se encontraron duplicados');
                    container.style.display = 'none';
                }
            } catch (error) {
                console.error('Error al buscar duplicados:', error);
                listContainer.innerHTML = '<p class="text-danger">Error al buscar ejercicios similares</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM cargado - ejecutando buscarDuplicados()');
            buscarDuplicados();
        });
    </script>
@endpush

@push('styles')
    <style>
        .math-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }
    </style>
@endpush