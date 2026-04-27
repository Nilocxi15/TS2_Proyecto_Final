{{-- Tabla Tutor: recursos propios con filtros y acciones de estado/revisión. --}}
<section id="recursos" class="data-panel mt-4">
    <div class="panel-head">
        <div>
            <h2 class="panel-title mb-0">Recursos del tutor</h2>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="panel-chip">PDF / VIDEO / SIMULADOR</span>
            <button
                type="button"
                class="btn btn-sm btn-primary js-open-action-modal"
                data-action="crear"
                data-entity-type="recurso"
            >
                <i class="fa-solid fa-plus me-1"></i> Crear recurso
            </button>
        </div>
    </div>

    <div class="filters-card">
        <form method="GET" action="{{ route('tutor.resources.flashcards.show') }}" class="row g-3 align-items-end js-tutor-filter-form">
            <input type="hidden" name="flash_q" value="{{ $flashcardsFiltros['q'] }}">
            <input type="hidden" name="flash_estado" value="{{ $flashcardsFiltros['estado'] }}">
            <input type="hidden" name="flash_modulo" value="{{ $flashcardsFiltros['modulo'] }}">
            <input type="hidden" name="flash_subtema" value="{{ $flashcardsFiltros['subtema'] }}">
            <input type="hidden" name="flash_orden" value="{{ $flashcardsFiltros['orden'] }}">
            <input type="hidden" name="flash_per_page" value="{{ $flashcardsFiltros['per_page'] }}">

            <div class="col-12 col-lg-4">
                <label for="rec_q" class="form-label">Buscar</label>
                <input type="search" class="form-control" id="rec_q" name="rec_q"
                    value="{{ $recursosFiltros['q'] }}" placeholder="Título o descripción">
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_tipo" class="form-label">Tipo</label>
                <select class="form-select" id="rec_tipo" name="rec_tipo">
                    <option value="">Todos</option>
                    @foreach ($recursosTipos as $tipo)
                        <option value="{{ $tipo }}" @selected($recursosFiltros['tipo'] === $tipo)>
                            {{ $resourceTypeLabels[$tipo] ?? $tipo }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_estado" class="form-label">Estado</label>
                <select class="form-select" id="rec_estado" name="rec_estado">
                    <option value="">Todos</option>
                    @foreach ($recursosEstados as $estado)
                        <option value="{{ $estado }}" @selected($recursosFiltros['estado'] === $estado)>
                            {{ $resourceStateLabels[$estado] ?? $estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_modulo" class="form-label">Módulo</label>
                <select class="form-select" id="rec_modulo" name="rec_modulo" data-module-select>
                    <option value="">Todos</option>
                    @foreach ($recursosModulos as $modulo)
                        <option value="{{ $modulo->id }}" @selected((string) $recursosFiltros['modulo'] === (string) $modulo->id)>
                            {{ $modulo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_subtema" class="form-label">Subtema</label>
                <select class="form-select" id="rec_subtema" name="rec_subtema" data-subtema-select>
                    <option value="">Todos</option>
                    @foreach ($recursosSubtemas as $subtema)
                        <option value="{{ $subtema->id }}" data-modulo-id="{{ $subtema->modulo_id }}"
                            @selected((string) $recursosFiltros['subtema'] === (string) $subtema->id)>
                            {{ $subtema->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_orden" class="form-label">Ordenar</label>
                <select class="form-select" id="rec_orden" name="rec_orden">
                    <option value="recientes" @selected($recursosFiltros['orden'] === 'recientes')>Más recientes</option>
                    <option value="antiguos" @selected($recursosFiltros['orden'] === 'antiguos')>Más antiguos</option>
                    <option value="titulo_asc" @selected($recursosFiltros['orden'] === 'titulo_asc')>Título A-Z</option>
                    <option value="titulo_desc" @selected($recursosFiltros['orden'] === 'titulo_desc')>Título Z-A</option>
                    <option value="tipo_asc" @selected($recursosFiltros['orden'] === 'tipo_asc')>Tipo</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="rec_per_page" class="form-label">Mostrar</label>
                <select class="form-select" id="rec_per_page" name="rec_per_page">
                    @foreach ($recursosAllowedPerPage as $size)
                        <option value="{{ $size }}" @selected((int) $recursosFiltros['per_page'] === $size)>
                            {{ $size }} por página
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter me-1"></i> Aplicar filtros
                </button>
                <a href="{{ route('tutor.resources.flashcards.show', array_merge(request()->except('rec_q', 'rec_tipo', 'rec_estado', 'rec_modulo', 'rec_subtema', 'rec_orden', 'rec_per_page'), [
                    'flash_q' => $flashcardsFiltros['q'],
                    'flash_estado' => $flashcardsFiltros['estado'],
                    'flash_modulo' => $flashcardsFiltros['modulo'],
                    'flash_subtema' => $flashcardsFiltros['subtema'],
                    'flash_orden' => $flashcardsFiltros['orden'],
                    'flash_per_page' => $flashcardsFiltros['per_page'],
                ])) }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="table-card mt-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tutor-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Módulo</th>
                        <th>Subtema</th>
                        <th>Tipo</th>
                        <th>Url</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recursosTutor as $recurso)
                        @php
                            $tipo = strtoupper((string) $recurso->tipo);
                            $chipClass = $resourceTypeClasses[$tipo] ?? 'chip-default';
                            $iconClass = $resourceIconClasses[$tipo] ?? 'fa-solid fa-book';
                            $puedeEditar = in_array((string) $recurso->estado, ['BORRADOR', 'PUBLICADO', 'RECHAZADO'], true);
                            $puedeCambiarDisponibilidad = ! in_array((string) $recurso->estado, ['PENDIENTE', 'REVISION'], true);
                            $puedeEnviarRevision = in_array((string) $recurso->estado, ['BORRADOR', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'], true);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $recurso->titulo }}</strong>
                            </td>
                            <td class="table-description">
                                {{ \Illuminate\Support\Str::limit((string) $recurso->descripcion, 120) }}
                            </td>
                            <td>{{ optional($recurso->modulo)->nombre ?? 'Sin módulo' }}</td>
                            <td>{{ optional($recurso->subtema)->nombre ?? 'Sin subtema' }}</td>
                            <td>
                                <span class="resource-chip {{ $chipClass }}">
                                    <i class="{{ $iconClass }} me-1"></i>{{ $tipo ?: 'RECURSO' }}
                                </span>
                            </td>
                            <td>
                                @if ($recurso->url)
                                    <a href="{{ $recurso->url }}" target="_blank" rel="noopener noreferrer" class="resource-url">
                                        Abrir enlace
                                    </a>
                                @else
                                    <span class="text-muted">Sin URL</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $statusClasses[$recurso->estado] ?? 'status-badge' }}">
                                    {{ $resourceStateLabels[$recurso->estado] ?? ($recurso->estado ?: 'Sin estado') }}
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary js-open-action-modal"
                                        title="{{ $puedeEditar ? 'Editar recurso' : 'No editable hasta publicarse nuevamente' }}"
                                        data-action="editar"
                                        data-entity-type="recurso"
                                        data-entity-id="{{ $recurso->id }}"
                                        data-entity-name="{{ $recurso->titulo ?: 'Recurso sin título' }}"
                                        data-update-url="{{ route('tutor.resources.recursos.update', $recurso->id) }}"
                                        data-can-edit="{{ $puedeEditar ? '1' : '0' }}"
                                        data-titulo="{{ $recurso->titulo }}"
                                        data-descripcion="{{ $recurso->descripcion }}"
                                        data-modulo-id="{{ $recurso->modulo_id }}"
                                        data-subtema-id="{{ $recurso->subtema_id }}"
                                        data-tipo="{{ $recurso->tipo }}"
                                        data-url="{{ $recurso->url }}"
                                        data-estado="{{ $recurso->estado }}"
                                        @disabled(! $puedeEditar)
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger js-open-action-modal"
                                        title="Eliminar recurso"
                                        data-action="eliminar"
                                        data-entity-type="recurso"
                                        data-entity-id="{{ $recurso->id }}"
                                        data-entity-name="{{ $recurso->titulo ?: 'Recurso sin título' }}"
                                        data-delete-url="{{ route('tutor.resources.recursos.destroy', $recurso->id) }}"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info js-open-action-modal"
                                        title="{{ $puedeEnviarRevision ? 'Enviar recurso a revisión' : 'Solo se pueden enviar a revisión recursos en BORRADOR, PUBLICADO, DESHABILITADO o RECHAZADO' }}"
                                        data-action="revisar"
                                        data-entity-type="recurso"
                                        data-entity-id="{{ $recurso->id }}"
                                        data-entity-name="{{ $recurso->titulo ?: 'Recurso sin título' }}"
                                        data-state-url="{{ route('tutor.resources.recursos.cambiar-estado', $recurso->id) }}"
                                        data-state-target="REVISION"
                                        @disabled(! $puedeEnviarRevision)
                                    >
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning js-open-action-modal"
                                        title="{{ $puedeCambiarDisponibilidad ? 'Deshabilitar recurso' : 'No disponible mientras está en PENDIENTE/REVISION' }}"
                                        data-action="deshabilitar"
                                        data-entity-type="recurso"
                                        data-entity-id="{{ $recurso->id }}"
                                        data-entity-name="{{ $recurso->titulo ?: 'Recurso sin título' }}"
                                        data-state-url="{{ route('tutor.resources.recursos.cambiar-estado', $recurso->id) }}"
                                        data-state-target="DESHABILITADO"
                                        @disabled(! $puedeCambiarDisponibilidad)
                                    >
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success js-open-action-modal"
                                        title="{{ $puedeCambiarDisponibilidad ? 'Habilitar recurso' : 'No disponible mientras está en PENDIENTE/REVISION' }}"
                                        data-action="habilitar"
                                        data-entity-type="recurso"
                                        data-entity-id="{{ $recurso->id }}"
                                        data-entity-name="{{ $recurso->titulo ?: 'Recurso sin título' }}"
                                        data-state-url="{{ route('tutor.resources.recursos.cambiar-estado', $recurso->id) }}"
                                        data-state-target="PUBLICADO"
                                        @disabled(! $puedeCambiarDisponibilidad)
                                    >
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-table">
                                    <i class="fa-regular fa-folder-open"></i>
                                    <h3>No hay recursos para mostrar</h3>
                                    <p>Prueba con otros filtros o agrega nuevos recursos para tus subtemas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recursosTutor->hasPages())
            <div class="table-footer">
                {{ $recursosTutor->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</section>
