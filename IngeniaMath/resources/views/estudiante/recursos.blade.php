@extends('layouts.app')

{{-- Flujo Estudiante: catálogo de recursos publicados con filtros de consulta. --}}
@section('title', 'Recursos Educativos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estudiante/recursos.css') }}">
@endpush

@section('content')
    <section class="resources-page py-4">
        <div class="container">
            <div class="resources-header">
                <div class="hero-copy">
                    <p class="eyebrow mb-2">Biblioteca Académica</p>
                    <h1 class="mb-2">Recursos Educativos</h1>
                    <p class="resources-subtitle mb-0">
                        Explora diversos materiales por tema. Cada recurso se abre en una pestaña nueva para que mantengas
                        tu
                        progreso aquí.

                    </p>
                    <p></p>
                    <p class="resources-subtitle mb-0">Y recuerda "¡Id y enseñad a todos!"</p>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('student.flashcards') }}" class="btn btn-ui-inverse">
                        <i class="fa-solid fa-layer-group me-1"></i> Flashcards
                    </a>
                </div>
            </div>

            <div class="filters-card mt-4">
                {{-- Filtros de búsqueda para explorar recursos sin modificar contenido. --}}
                <form method="GET" action="{{ route('student.resources') }}" id="filtrosRecursosForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label for="q" class="form-label">Buscar</label>
                            <input type="search" class="form-control" id="q" name="q" value="{{ $filtros['q'] }}"
                                placeholder="Título o descripción">
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option value="">Todos</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo }}" @selected($filtros['tipo'] === $tipo)>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="modulo" class="form-label">Módulo</label>
                            <select class="form-select" id="modulo" name="modulo">
                                <option value="">Todos</option>
                                @foreach ($modulos as $modulo)
                                    <option value="{{ $modulo->id }}" @selected((string) $filtros['modulo'] === (string) $modulo->id)>
                                        {{ $modulo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="subtema" class="form-label">Subtema</label>
                            <select class="form-select" id="subtema" name="subtema">
                                <option value="">Todos</option>
                                @foreach ($subtemas as $subtema)
                                    <option value="{{ $subtema->id }}" data-modulo-id="{{ $subtema->modulo_id }}"
                                        @selected((string) $filtros['subtema'] === (string) $subtema->id)>
                                        {{ $subtema->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="orden" class="form-label">Ordenar por</label>
                            <select class="form-select" id="orden" name="orden">
                                <option value="recientes" @selected($filtros['orden'] === 'recientes')>Más recientes</option>
                                <option value="antiguos" @selected($filtros['orden'] === 'antiguos')>Más antiguos</option>
                                <option value="titulo_asc" @selected($filtros['orden'] === 'titulo_asc')>Título A-Z</option>
                                <option value="titulo_desc" @selected($filtros['orden'] === 'titulo_desc')>Título Z-A</option>
                                <option value="tipo_asc" @selected($filtros['orden'] === 'tipo_asc')>Tipo</option>
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="per_page" class="form-label">Mostrar</label>
                            <select class="form-select" id="per_page" name="per_page">
                                @foreach ($allowedPerPage as $size)
                                    <option value="{{ $size }}" @selected((int) $filtros['per_page'] === $size)>
                                        {{ $size }} por página
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-lg-10 d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa-solid fa-filter me-1"></i> Aplicar filtros
                            </button>
                            <a href="{{ route('student.resources') }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-rotate-left me-1"></i> Limpiar
                            </a>
                        </div>

                        <div class="col-12 col-lg-2 text-lg-end">
                            <p class="results-count mb-0">
                                {{ $recursos->total() }} resultado{{ $recursos->total() === 1 ? '' : 's' }}
                            </p>
                        </div>
                    </div>
                </form>
            </div>

            @if ($recursos->count() > 0)
                {{-- Tarjetas de recursos publicados. --}}
                <div class="row g-4 mt-1">
                    @foreach ($recursos as $recurso)
                        @php
                            $tipo = strtoupper((string) $recurso->tipo);
                            $tipoClasses = [
                                'VIDEO' => 'chip-video',
                                'PDF' => 'chip-pdf',
                                'SIMULADOR' => 'chip-simulador',
                            ];
                            $chipClass = $tipoClasses[$tipo] ?? 'chip-default';

                            $iconMap = [
                                'VIDEO' => 'fa-solid fa-circle-play',
                                'PDF' => 'fa-solid fa-file-pdf',
                                'SIMULADOR' => 'fa-solid fa-flask-vial',
                            ];
                            $iconClass = $iconMap[$tipo] ?? 'fa-solid fa-book';
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4">
                            <a href="{{ $recurso->url }}" target="_blank" rel="noopener noreferrer" class="resource-card"
                                title="Abrir recurso en nueva pestaña">
                                <div class="resource-card-top">
                                    <span class="resource-chip {{ $chipClass }}">
                                        <i class="{{ $iconClass }} me-1"></i>{{ $tipo ?: 'RECURSO' }}
                                    </span>
                                    <i class="fa-solid fa-arrow-up-right-from-square external-icon"></i>
                                </div>

                                <h3 class="resource-title">{{ $recurso->titulo }}</h3>
                                <p class="resource-description">{{ $recurso->descripcion }}</p>

                                <div class="resource-meta">
                                    <span>
                                        <i class="fa-solid fa-cubes-stacked me-1"></i>
                                        {{ optional($recurso->modulo)->nombre ?? 'Módulo no especificado' }}
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-bookmark me-1"></i>
                                        {{ optional($recurso->subtema)->nombre ?? 'Subtema no especificado' }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $recursos->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="empty-state mt-4">
                    <i class="fa-regular fa-folder-open"></i>
                    <h2>No se encontraron recursos</h2>
                    <p>Ajusta los filtros o limpia la búsqueda para ver más resultados.</p>
                    <a href="{{ route('student.resources') }}" class="btn btn-outline-primary">Ver todos los recursos</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            // Mantiene subtemas sincronizados con el módulo seleccionado.
            const moduloSelect = document.getElementById('modulo');
            const subtemaSelect = document.getElementById('subtema');
            if (!moduloSelect || !subtemaSelect) {
                return;
            }

            const allSubtemaOptions = Array.from(subtemaSelect.querySelectorAll('option'));

            function syncSubtemaOptions() {
                const selectedModulo = moduloSelect.value;
                const currentSubtema = subtemaSelect.value;

                allSubtemaOptions.forEach((option) => {
                    if (option.value === '') {
                        option.hidden = false;
                        return;
                    }

                    const belongsToModulo = option.dataset.moduloId === selectedModulo;
                    option.hidden = Boolean(selectedModulo) && !belongsToModulo;
                });

                const selectedOption = subtemaSelect.querySelector(`option[value="${currentSubtema}"]`);
                if (selectedOption && selectedOption.hidden) {
                    subtemaSelect.value = '';
                }
            }

            moduloSelect.addEventListener('change', syncSubtemaOptions);
            syncSubtemaOptions();
        })();
    </script>
@endpush