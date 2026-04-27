{{-- Tabla Tutor: flashcards propias con filtros y acciones de revisión/disponibilidad. --}}
<section id="flashcards" class="data-panel mt-4">
    <div class="panel-head">
        <div>
            <h2 class="panel-title mb-0">Flashcards del tutor</h2>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="panel-chip">Pregunta / Respuesta</span>
            <button
                type="button"
                class="btn btn-sm btn-primary js-open-action-modal"
                data-action="crear"
                data-entity-type="flashcard"
            >
                <i class="fa-solid fa-plus me-1"></i> Crear flashcard
            </button>
        </div>
    </div>

    <div class="filters-card">
        <form method="GET" action="{{ route('tutor.resources.flashcards.show') }}" class="row g-3 align-items-end js-tutor-filter-form">
            <input type="hidden" name="rec_q" value="{{ $recursosFiltros['q'] }}">
            <input type="hidden" name="rec_tipo" value="{{ $recursosFiltros['tipo'] }}">
            <input type="hidden" name="rec_estado" value="{{ $recursosFiltros['estado'] }}">
            <input type="hidden" name="rec_modulo" value="{{ $recursosFiltros['modulo'] }}">
            <input type="hidden" name="rec_subtema" value="{{ $recursosFiltros['subtema'] }}">
            <input type="hidden" name="rec_orden" value="{{ $recursosFiltros['orden'] }}">
            <input type="hidden" name="rec_per_page" value="{{ $recursosFiltros['per_page'] }}">

            <div class="col-12 col-lg-4">
                <label for="flash_q" class="form-label">Buscar</label>
                <input type="search" class="form-control" id="flash_q" name="flash_q"
                    value="{{ $flashcardsFiltros['q'] }}" placeholder="Pregunta o respuesta">
            </div>

            <div class="col-6 col-lg-2">
                <label for="flash_estado" class="form-label">Estado</label>
                <select class="form-select" id="flash_estado" name="flash_estado">
                    <option value="">Todos</option>
                    @foreach ($flashcardsEstados as $estado)
                        <option value="{{ $estado }}" @selected($flashcardsFiltros['estado'] === $estado)>
                            {{ $resourceStateLabels[$estado] ?? $estado }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="flash_modulo" class="form-label">Módulo</label>
                <select class="form-select" id="flash_modulo" name="flash_modulo" data-module-select>
                    <option value="">Todos</option>
                    @foreach ($flashcardsModulos as $modulo)
                        <option value="{{ $modulo->id }}" @selected((string) $flashcardsFiltros['modulo'] === (string) $modulo->id)>
                            {{ $modulo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="flash_subtema" class="form-label">Subtema</label>
                <select class="form-select" id="flash_subtema" name="flash_subtema" data-subtema-select>
                    <option value="">Todos</option>
                    @foreach ($flashcardsSubtemas as $subtema)
                        <option value="{{ $subtema->id }}" data-modulo-id="{{ $subtema->modulo_id }}"
                            @selected((string) $flashcardsFiltros['subtema'] === (string) $subtema->id)>
                            {{ $subtema->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="flash_orden" class="form-label">Ordenar</label>
                <select class="form-select" id="flash_orden" name="flash_orden">
                    <option value="recientes" @selected($flashcardsFiltros['orden'] === 'recientes')>Más recientes</option>
                    <option value="antiguos" @selected($flashcardsFiltros['orden'] === 'antiguos')>Más antiguas</option>
                    <option value="pregunta_asc" @selected($flashcardsFiltros['orden'] === 'pregunta_asc')>Pregunta A-Z</option>
                    <option value="pregunta_desc" @selected($flashcardsFiltros['orden'] === 'pregunta_desc')>Pregunta Z-A</option>
                    <option value="estado_asc" @selected($flashcardsFiltros['orden'] === 'estado_asc')>Estado</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="flash_per_page" class="form-label">Mostrar</label>
                <select class="form-select" id="flash_per_page" name="flash_per_page">
                    @foreach ($flashcardsAllowedPerPage as $size)
                        <option value="{{ $size }}" @selected((int) $flashcardsFiltros['per_page'] === $size)>
                            {{ $size }} por página
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter me-1"></i> Aplicar filtros
                </button>
                <a href="{{ route('tutor.resources.flashcards.show', array_merge(request()->except('flash_q', 'flash_estado', 'flash_modulo', 'flash_subtema', 'flash_orden', 'flash_per_page'), [
                    'rec_q' => $recursosFiltros['q'],
                    'rec_tipo' => $recursosFiltros['tipo'],
                    'rec_estado' => $recursosFiltros['estado'],
                    'rec_modulo' => $recursosFiltros['modulo'],
                    'rec_subtema' => $recursosFiltros['subtema'],
                    'rec_orden' => $recursosFiltros['orden'],
                    'rec_per_page' => $recursosFiltros['per_page'],
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
                        <th>Subtema</th>
                        <th>Pregunta</th>
                        <th>Respuesta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($flashcardsTutor as $flashcard)
                        @php
                            $puedeEditar = in_array((string) $flashcard->estado, ['BORRADOR', 'PUBLICADO', 'RECHAZADO'], true);
                            $estadoActual = (string) $flashcard->estado;
                            $puedeEnviarRevision = in_array($estadoActual, ['BORRADOR', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'], true);
                            $puedeDeshabilitar = $estadoActual === 'PUBLICADO';
                            $puedeHabilitar = $estadoActual === 'DESHABILITADO';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ optional($flashcard->subtema)->nombre ?? 'Sin subtema' }}</strong>
                                @if (optional($flashcard->subtema)->modulo)
                                    <div class="text-muted small">
                                        {{ optional($flashcard->subtema->modulo)->nombre }}
                                    </div>
                                @endif
                            </td>
                            <td class="table-description">{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 110) }}</td>
                            <td class="table-description">{{ \Illuminate\Support\Str::limit((string) $flashcard->respuesta, 110) }}</td>
                            <td>
                                <span class="{{ $statusClasses[$flashcard->estado] ?? 'status-badge' }}">
                                    {{ $resourceStateLabels[$flashcard->estado] ?? ($flashcard->estado ?: 'Sin estado') }}
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary js-open-action-modal"
                                        title="{{ $puedeEditar ? 'Editar flashcard' : 'No editable hasta publicarse nuevamente' }}"
                                        data-action="editar"
                                        data-entity-type="flashcard"
                                        data-entity-id="{{ $flashcard->id }}"
                                        data-entity-name="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}"
                                        data-update-url="{{ route('tutor.resources.flashcards.update', $flashcard->id) }}"
                                        data-can-edit="{{ $puedeEditar ? '1' : '0' }}"
                                        data-subtema-id="{{ $flashcard->subtema_id }}"
                                        data-pregunta="{{ $flashcard->pregunta }}"
                                        data-respuesta="{{ $flashcard->respuesta }}"
                                        data-estado="{{ $flashcard->estado }}"
                                        @disabled(! $puedeEditar)
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger js-open-action-modal"
                                        title="Eliminar flashcard"
                                        data-action="eliminar"
                                        data-entity-type="flashcard"
                                        data-entity-id="{{ $flashcard->id }}"
                                        data-entity-name="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}"
                                        data-delete-url="{{ route('tutor.resources.flashcards.destroy', $flashcard->id) }}"
                                    >
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info js-open-action-modal"
                                        title="{{ $puedeEnviarRevision ? 'Enviar flashcard a revisión' : 'Solo se pueden enviar a revisión flashcards en BORRADOR, PUBLICADO, DESHABILITADO o RECHAZADO' }}"
                                        data-action="revisar"
                                        data-entity-type="flashcard"
                                        data-entity-id="{{ $flashcard->id }}"
                                        data-entity-name="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}"
                                        data-state-url="{{ route('tutor.resources.flashcards.cambiar-estado', $flashcard->id) }}"
                                        data-state-target="REVISION"
                                        @disabled(! $puedeEnviarRevision)
                                    >
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning js-open-action-modal"
                                        title="{{ $puedeDeshabilitar ? 'Deshabilitar flashcard' : 'Solo puedes deshabilitar flashcards en estado PUBLICADO' }}"
                                        data-action="deshabilitar"
                                        data-entity-type="flashcard"
                                        data-entity-id="{{ $flashcard->id }}"
                                        data-entity-name="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}"
                                        data-state-url="{{ route('tutor.resources.flashcards.cambiar-estado', $flashcard->id) }}"
                                        data-state-target="DESHABILITADO"
                                        @disabled(! $puedeDeshabilitar)
                                    >
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success js-open-action-modal"
                                        title="{{ $puedeHabilitar ? 'Habilitar flashcard' : 'Solo puedes habilitar flashcards en estado DESHABILITADO' }}"
                                        data-action="habilitar"
                                        data-entity-type="flashcard"
                                        data-entity-id="{{ $flashcard->id }}"
                                        data-entity-name="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}"
                                        data-state-url="{{ route('tutor.resources.flashcards.cambiar-estado', $flashcard->id) }}"
                                        data-state-target="PUBLICADO"
                                        @disabled(! $puedeHabilitar)
                                    >
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-table">
                                    <i class="fa-regular fa-folder-open"></i>
                                    <h3>No hay flashcards para mostrar</h3>
                                    <p>Prueba con otros filtros o crea flashcards en tus subtemas asociados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($flashcardsTutor->hasPages())
            <div class="table-footer">
                {{ $flashcardsTutor->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</section>
