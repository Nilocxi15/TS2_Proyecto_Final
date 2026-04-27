@extends('layouts.app')

{{-- Flujo Estudiante: galería de flashcards publicadas por módulo/subtema. --}}
@section('title', 'Flashcards')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estudiante/flashcards-galery.css') }}">
@endpush

@section('content')
    <section class="flashcards-page">
        <div class="container py-4 py-lg-5">
            <div class="flashcards-hero">
                <div class="hero-copy">
                    <p class="eyebrow mb-2">Biblioteca Académica</p>
                    <h1 class="mb-3">Flashcards</h1>
                    <p class="resources-subtitle mb-0">
                        Estudia por módulo o entra a un subtema específico cuando quieras ir directo al repaso.
                        Toca la pregunta para revelar la respuesta.
                    </p>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('student.resources') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-book-open me-1"></i> Recursos
                    </a>
                </div>
            </div>

            <div class="flashcards-toolbar mt-4">
                <div>
                    <p class="toolbar-label mb-1">Exploración rápida</p>
                    <h2 class="toolbar-title mb-0">Filtra por módulo</h2>
                </div>

                <form method="GET" action="{{ route('student.flashcards') }}" class="toolbar-form">
                    <label class="visually-hidden" for="modulo">Módulo</label>
                    <select id="modulo" name="modulo" class="form-select">
                        <option value="">Todos los módulos</option>
                        @foreach ($modulos as $modulo)
                            <option value="{{ $modulo->id }}" @selected((string) $moduloSeleccionado === (string) $modulo->id)>
                                {{ $modulo->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter me-1"></i> Aplicar
                    </button>
                </form>
            </div>

            @forelse ($modulos as $modulo)
                {{-- Sección por módulo con vista previa de tarjetas por subtema. --}}
                <section class="module-section mt-5">
                    <div class="section-header">
                        <div>
                            <p class="section-kicker mb-1">Módulo</p>
                            <h2 class="mb-0">{{ $modulo->nombre }}</h2>
                        </div>
                    </div>

                    <div class="subtopic-grid mt-4">
                        @forelse ($modulo->subtemas as $subtema)
                            <article class="subtopic-card">
                                <div class="subtopic-card-header">
                                    <div>
                                        <p class="subtopic-label mb-1">Subtema</p>
                                        <h3 class="subtopic-title mb-0">{{ $subtema->nombre }}</h3>
                                    </div>

                                    <a href="{{ route('student.flashcards.subtema', $subtema) }}" class="btn btn-outline-primary btn-sm">
                                        Ver todo
                                    </a>
                                </div>

                                <p class="subtopic-meta mb-3">
                                    {{ $subtema->flashcards->count() }} flashcard{{ $subtema->flashcards->count() === 1 ? '' : 's' }} disponibles
                                </p>

                                <div class="mini-grid">
                                    @foreach ($subtema->flashcards->take(3) as $flashcard)
                                        <button type="button" class="mini-flashcard" data-flashcard-toggle>
                                            <span class="mini-flashcard-side mini-flashcard-front">
                                                <span class="mini-label">Pregunta</span>
                                                <span class="mini-text">{{ $flashcard->pregunta }}</span>
                                                <span class="mini-hint">Toca para ver la respuesta</span>
                                            </span>
                                            <span class="mini-flashcard-side mini-flashcard-back">
                                                <span class="mini-label success">Respuesta</span>
                                                <span class="mini-text">{{ $flashcard->respuesta }}</span>
                                                <span class="mini-hint">Toca para volver</span>
                                            </span>
                                        </button>
                                    @endforeach
                                </div>

                                @if ($subtema->flashcards->count() > 3)
                                    <div class="subtopic-footer mt-3">
                                        <a href="{{ route('student.flashcards.subtema', $subtema) }}" class="text-link">
                                            Ver las {{ $subtema->flashcards->count() - 3 }} restantes
                                        </a>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="empty-state mt-3">
                                <i class="fa-regular fa-folder-open"></i>
                                <h2>No hay subtemas con flashcards</h2>
                                <p>Cuando se carguen flashcards para este módulo, aparecerán agrupadas aquí.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @empty
                <div class="empty-state mt-4">
                    <i class="fa-regular fa-folder-open"></i>
                    <h2>No se encontraron flashcards</h2>
                    <p>Aún no hay flashcards publicadas para mostrar.</p>
                </div>
            @endforelse
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                // Voltea cada mini tarjeta para alternar pregunta/respuesta.
                const toggles = document.querySelectorAll('[data-flashcard-toggle]');

                toggles.forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        toggle.classList.toggle('is-flipped');
                    });
                });
            })();
        </script>
    @endpush
@endsection