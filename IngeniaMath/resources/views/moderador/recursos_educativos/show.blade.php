@extends('layouts.app')

{{-- Flujo Moderador: revisión global de recursos/flashcards enviados por tutores. --}}
@section('title', 'Panel de Recursos')

@push('styles')
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/tutor/flashcards.css') }}">
@endpush

@section('content')
	@php
		$statusClasses = [
			'BORRADOR' => 'status-badge status-draft',
			'PENDIENTE' => 'status-badge status-review',
			'REVISION' => 'status-badge status-review',
			'APROBADO' => 'status-badge status-approved',
			'PUBLICADO' => 'status-badge status-published',
			'DESHABILITADO' => 'status-badge status-disabled',
			'RECHAZADO' => 'status-badge status-rejected',
		];

		$resourceStateLabels = [
			'BORRADOR' => 'Borrador',
			'PENDIENTE' => 'Pendiente',
			'REVISION' => 'En revisión',
			'APROBADO' => 'Aprobado',
			'PUBLICADO' => 'Publicado',
			'DESHABILITADO' => 'Deshabilitado',
			'RECHAZADO' => 'Rechazado',
		];
	@endphp

	<section class="tutor-resources-page py-4 py-lg-5">
		<div class="container">
			<div class="tutor-hero">
				<div class="hero-copy">
					<p class="eyebrow mb-2">Panel del moderador</p>
					<h1 class="mb-3">Revisión de recursos y flashcards</h1>
					<p class="resources-subtitle mb-0">
						Revisa el contenido enviado por tutores, aprueba para PUBLICADO o rechaza para RECHAZADO.
					</p>
				</div>

				<div class="hero-actions">
					<a href="#recursos" class="btn btn-light">
						<i class="fa-solid fa-book me-1"></i> Recursos
					</a>
					<a href="#flashcards" class="btn btn-outline-light">
						<i class="fa-solid fa-layer-group me-1"></i> Flashcards
					</a>
				</div>
			</div>

			<div class="resource-stats mt-4">
				<div class="stat-card">
					<span class="stat-label">Recursos</span>
					<strong>{{ $moderadorRecursos->total() }}</strong>
					<small>Total en el panel</small>
				</div>
				<div class="stat-card">
					<span class="stat-label">Flashcards</span>
					<strong>{{ $moderadorFlashcards->total() }}</strong>
					<small>Total en el panel</small>
				</div>
			</div>

			@if (session('success'))
				<div class="alert alert-success mt-4 mb-0">
					<i class="fa-solid fa-circle-check me-1"></i>{{ session('success') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger mt-4 mb-0">
					<i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $errors->first() }}
				</div>
			@endif

			<section id="recursos" class="data-panel mt-4">
				{{-- Tabla de recursos para aprobar/rechazar/eliminar con filtros. --}}
				<div class="panel-head">
					<h2 class="panel-title mb-0">Recursos</h2>
				</div>

				<div class="filters-card">
					<form method="GET" action="{{ route('moderador.resources.revisions') }}" class="row g-3 align-items-end">
						<input type="hidden" name="flash_q" value="{{ $moderadorFlashcardsFiltros['q'] }}">
						<input type="hidden" name="flash_estado" value="{{ $moderadorFlashcardsFiltros['estado'] }}">
						<input type="hidden" name="flash_modulo" value="{{ $moderadorFlashcardsFiltros['modulo'] }}">
						<input type="hidden" name="flash_subtema" value="{{ $moderadorFlashcardsFiltros['subtema'] }}">
						<input type="hidden" name="flash_orden" value="{{ $moderadorFlashcardsFiltros['orden'] }}">
						<input type="hidden" name="flash_per_page" value="{{ $moderadorFlashcardsFiltros['per_page'] }}">

						<div class="col-12 col-lg-4">
							<label for="rec_q" class="form-label">Buscar</label>
							<input type="search" class="form-control" id="rec_q" name="rec_q" value="{{ $moderadorRecursosFiltros['q'] }}" placeholder="Título o descripción">
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_tipo" class="form-label">Tipo</label>
							<select class="form-select" id="rec_tipo" name="rec_tipo">
								<option value="">Todos</option>
								@foreach ($moderadorRecursosTipos as $tipo)
									<option value="{{ $tipo }}" @selected($moderadorRecursosFiltros['tipo'] === $tipo)>{{ $tipo }}</option>
								@endforeach
							</select>
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_estado" class="form-label">Estado</label>
							<select class="form-select" id="rec_estado" name="rec_estado">
								<option value="">Todos</option>
								@foreach ($moderadorRecursosEstados as $estado)
									<option value="{{ $estado }}" @selected($moderadorRecursosFiltros['estado'] === $estado)>
										{{ $resourceStateLabels[$estado] ?? $estado }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_modulo" class="form-label">Módulo</label>
							<select class="form-select" id="rec_modulo" name="rec_modulo">
								<option value="">Todos</option>
								@foreach ($moderadorRecursosModulos as $modulo)
									<option value="{{ $modulo->id }}" @selected((string) $moderadorRecursosFiltros['modulo'] === (string) $modulo->id)>
										{{ $modulo->nombre }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_subtema" class="form-label">Subtema</label>
							<select class="form-select" id="rec_subtema" name="rec_subtema">
								<option value="">Todos</option>
								@foreach ($moderadorRecursosSubtemas as $subtema)
									<option value="{{ $subtema->id }}" @selected((string) $moderadorRecursosFiltros['subtema'] === (string) $subtema->id)>
										{{ $subtema->nombre }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_orden" class="form-label">Ordenar</label>
							<select class="form-select" id="rec_orden" name="rec_orden">
								<option value="recientes" @selected($moderadorRecursosFiltros['orden'] === 'recientes')>Más recientes</option>
								<option value="antiguos" @selected($moderadorRecursosFiltros['orden'] === 'antiguos')>Más antiguos</option>
								<option value="titulo_asc" @selected($moderadorRecursosFiltros['orden'] === 'titulo_asc')>Título A-Z</option>
								<option value="titulo_desc" @selected($moderadorRecursosFiltros['orden'] === 'titulo_desc')>Título Z-A</option>
								<option value="tipo_asc" @selected($moderadorRecursosFiltros['orden'] === 'tipo_asc')>Tipo</option>
							</select>
						</div>

						<div class="col-6 col-lg-2">
							<label for="rec_per_page" class="form-label">Mostrar</label>
							<select class="form-select" id="rec_per_page" name="rec_per_page">
								@foreach ($moderadorRecursosAllowedPerPage as $size)
									<option value="{{ $size }}" @selected((int) $moderadorRecursosFiltros['per_page'] === $size)>{{ $size }} por página</option>
								@endforeach
							</select>
						</div>

						<div class="col-12 d-flex flex-wrap gap-2">
							<button type="submit" class="btn btn-primary">
								<i class="fa-solid fa-filter me-1"></i> Aplicar filtros
							</button>
							<a href="{{ route('moderador.resources.revisions') }}" class="btn btn-outline-secondary">
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
									<th>Módulo</th>
									<th>Subtema</th>
									<th>Autor</th>
                                    <th>URL</th>
									<th>Estado</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($moderadorRecursos as $recurso)
									@php
										$puedeResolver = in_array((string) $recurso->estado, ['PENDIENTE', 'REVISION'], true);
									@endphp
									<tr>
										<td>{{ $recurso->titulo }}</td>
										<td>{{ optional($recurso->modulo)->nombre ?? 'Sin módulo' }}</td>
										<td>{{ optional($recurso->subtema)->nombre ?? 'Sin subtema' }}</td>
										<td>{{ optional($recurso->creador)->nombre_usuario ?? ('ID ' . (string) $recurso->creado_por) }}</td>
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
												{{ $resourceStateLabels[$recurso->estado] ?? $recurso->estado }}
											</span>
										</td>
										<td>
											<div class="action-group d-flex flex-wrap">
												<form method="POST" action="{{ route('moderador.resources.recursos.approve', $recurso->id) }}">
													@csrf
													@method('PATCH')
													<button type="submit" class="btn btn-sm btn-outline-success" @disabled(! $puedeResolver)>
														<i class="fa-solid fa-circle-check"></i>
													</button>
												</form>
												<form method="POST" action="{{ route('moderador.resources.recursos.reject', $recurso->id) }}">
													@csrf
													@method('PATCH')
													<button type="submit" class="btn btn-sm btn-outline-warning" @disabled(! $puedeResolver)>
														<i class="fa-solid fa-circle-xmark"></i>
													</button>
												</form>
												<form method="POST" action="{{ route('moderador.resources.recursos.destroy', $recurso->id) }}" class="js-confirm-delete" data-label="{{ $recurso->titulo }}">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-outline-danger">
														<i class="fa-regular fa-trash-can"></i>
													</button>
												</form>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">No hay recursos para revisar.</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					@if ($moderadorRecursos->hasPages())
						<div class="table-footer">
							{{ $moderadorRecursos->links('pagination::bootstrap-5') }}
						</div>
					@endif
				</div>
			</section>

			<section id="flashcards" class="data-panel mt-4">
				{{-- Tabla de flashcards para resolución por moderación. --}}
				<div class="panel-head">
					<h2 class="panel-title mb-0">Flashcards</h2>
				</div>

                <div class="filters-card">
                    <form method="GET" action="{{ route('moderador.resources.revisions') }}" class="row g-3 align-items-end">
                        <input type="hidden" name="rec_q" value="{{ $moderadorRecursosFiltros['q'] }}">
                        <input type="hidden" name="rec_tipo" value="{{ $moderadorRecursosFiltros['tipo'] }}">
                        <input type="hidden" name="rec_estado" value="{{ $moderadorRecursosFiltros['estado'] }}">
                        <input type="hidden" name="rec_modulo" value="{{ $moderadorRecursosFiltros['modulo'] }}">
                        <input type="hidden" name="rec_subtema" value="{{ $moderadorRecursosFiltros['subtema'] }}">
                        <input type="hidden" name="rec_orden" value="{{ $moderadorRecursosFiltros['orden'] }}">
                        <input type="hidden" name="rec_per_page" value="{{ $moderadorRecursosFiltros['per_page'] }}">

                        <div class="col-12 col-lg-4">
                            <label for="flash_q" class="form-label">Buscar</label>
                            <input type="search" class="form-control" id="flash_q" name="flash_q" value="{{ $moderadorFlashcardsFiltros['q'] }}" placeholder="Pregunta o respuesta">
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="flash_estado" class="form-label">Estado</label>
                            <select class="form-select" id="flash_estado" name="flash_estado">
                                <option value="">Todos</option>
                                @foreach ($moderadorFlashcardsEstados as $estado)
                                    <option value="{{ $estado }}" @selected($moderadorFlashcardsFiltros['estado'] === $estado)>
                                        {{ $resourceStateLabels[$estado] ?? $estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="flash_modulo" class="form-label">Módulo</label>
                            <select class="form-select" id="flash_modulo" name="flash_modulo">
                                <option value="">Todos</option>
                                @foreach ($moderadorFlashcardsModulos as $modulo)
                                    <option value="{{ $modulo->id }}" @selected((string) $moderadorFlashcardsFiltros['modulo'] === (string) $modulo->id)>
                                        {{ $modulo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="flash_subtema" class="form-label">Subtema</label>
                            <select class="form-select" id="flash_subtema" name="flash_subtema">
                                <option value="">Todos</option>
                                @foreach ($moderadorFlashcardsSubtemas as $subtema)
                                    <option value="{{ $subtema->id }}" @selected((string) $moderadorFlashcardsFiltros['subtema'] === (string) $subtema->id)>
                                        {{ $subtema->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="flash_orden" class="form-label">Ordenar</label>
                            <select class="form-select" id="flash_orden" name="flash_orden">
                                <option value="recientes" @selected($moderadorFlashcardsFiltros['orden'] === 'recientes')>Más recientes</option>
                                <option value="antiguos" @selected($moderadorFlashcardsFiltros['orden'] === 'antiguos')>Más antiguos</option>
                                <option value="pregunta_asc" @selected($moderadorFlashcardsFiltros['orden'] === 'pregunta_asc')>Pregunta A-Z</option>
                                <option value="pregunta_desc" @selected($moderadorFlashcardsFiltros['orden'] === 'pregunta_desc')>Pregunta Z-A</option>
                            </select>
                        </div>
                        <div class="col-6 col-lg-2">
                            <label for="flash_per_page" class="form-label">Mostrar</label>
                            <select class="form-select" id="flash_per_page" name="flash_per_page">
                                @foreach ($moderadorFlashcardsAllowedPerPage as $size)
                                    <option value="{{ $size }}" @selected((int) $moderadorFlashcardsFiltros['per_page'] === $size)>{{ $size }} por página</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-filter me-1"></i> Aplicar filtros
                            </button>
                            <a href="{{ route('moderador.resources.revisions') }}" class="btn btn-outline-secondary">
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
									<th>Pregunta</th>
									<th>Subtema</th>
									<th>Autor</th>
									<th>Estado</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								@forelse ($moderadorFlashcards as $flashcard)
									@php
										$puedeResolver = in_array((string) $flashcard->estado, ['PENDIENTE', 'REVISION'], true);
									@endphp
									<tr>
										<td>{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 100) }}</td>
										<td>{{ optional($flashcard->subtema)->nombre ?? 'Sin subtema' }}</td>
										<td>{{ optional($flashcard->creador)->nombre_usuario ?? ('ID ' . (string) $flashcard->creado_por) }}</td>
										<td>
											<span class="{{ $statusClasses[$flashcard->estado] ?? 'status-badge' }}">
												{{ $resourceStateLabels[$flashcard->estado] ?? $flashcard->estado }}
											</span>
										</td>
										<td>
											<div class="action-group d-flex flex-wrap">
												<form method="POST" action="{{ route('moderador.resources.flashcards.approve', $flashcard->id) }}">
													@csrf
													@method('PATCH')
													<button type="submit" class="btn btn-sm btn-outline-success" @disabled(! $puedeResolver)>
														<i class="fa-solid fa-circle-check"></i>
													</button>
												</form>
												<form method="POST" action="{{ route('moderador.resources.flashcards.reject', $flashcard->id) }}">
													@csrf
													@method('PATCH')
													<button type="submit" class="btn btn-sm btn-outline-warning" @disabled(! $puedeResolver)>
														<i class="fa-solid fa-circle-xmark"></i>
													</button>
												</form>
												<form method="POST" action="{{ route('moderador.resources.flashcards.destroy', $flashcard->id) }}" class="js-confirm-delete" data-label="{{ \Illuminate\Support\Str::limit((string) $flashcard->pregunta, 70) }}">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-outline-danger">
														<i class="fa-regular fa-trash-can"></i>
													</button>
												</form>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="5" class="text-center text-muted py-4">No hay flashcards para revisar.</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					@if ($moderadorFlashcards->hasPages())
						<div class="table-footer">
							{{ $moderadorFlashcards->links('pagination::bootstrap-5') }}
						</div>
					@endif
				</div>
			</section>
		</div>
	</section>

	@push('scripts')
		<script>
			// Solicita confirmación antes de eliminar desde el panel moderador.
			document.addEventListener('submit', function (event) {
				var form = event.target.closest('.js-confirm-delete');
				if (! form) {
					return;
				}

				var label = form.dataset.label || 'este elemento';
				var confirmed = window.confirm('¿Seguro que deseas eliminar "' + label + '"? Esta acción no se puede deshacer.');

				if (! confirmed) {
					event.preventDefault();
				}
			});
		</script>
	@endpush
@endsection