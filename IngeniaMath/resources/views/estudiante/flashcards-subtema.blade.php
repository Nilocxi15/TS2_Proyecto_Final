@extends('layouts.app')

{{-- Flujo Estudiante: detalle de flashcards por subtema con paginación. --}}
@section('title', 'Flashcards por subtema')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estudiante/flashcards-galery.css') }}">
@endpush

@section('content')
    <section class="flashcards-page">
        <div class="container py-4 py-lg-5">
            <div class="flashcards-hero">
                <div class="hero-copy">
                    <p class="eyebrow mb-2">{{ $modulo->nombre }}</p>
                    <h1 class="mb-3">{{ $subtema->nombre }}</h1>
                    <p class="resources-subtitle mb-0">
                        Revisa este subtema en bloques más manejables. La tarjeta muestra la pregunta y al tocarla revela la respuesta.
                    </p>
                </div>

                <div class="hero-actions">
                    <a href="{{ route('student.flashcards') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver
                    </a>
                    <a href="{{ route('student.resources', ['modulo' => $subtema->modulo_id, 'subtema' => $subtema->id]) }}" class="btn btn-primary">
                        <i class="fa-solid fa-book-open me-1"></i> Ver recursos
                    </a>
                </div>
            </div>

            <div class="flashcards-toolbar mt-4">
                <div>
                    <p class="toolbar-label mb-1">Navegación del módulo</p>
                    <h2 class="toolbar-title mb-0">Subtemas relacionados</h2>
                </div>

                <form method="GET" action="{{ route('student.flashcards.subtema', $subtema) }}" class="toolbar-form">
                    <label class="visually-hidden" for="per_page">Cantidad por página</label>
                    <select id="per_page" name="per_page" class="form-select">
                        @foreach ($allowedPerPage as $size)
                            <option value="{{ $size }}" @selected((int) $perPage === $size)>{{ $size }} por página</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-rotate me-1"></i> Actualizar
                    </button>
                </form>
            </div>

            <div class="subtopic-nav mt-4">
                {{-- Navegación lateral entre subtemas del mismo módulo. --}}
                @foreach ($subtemasDelModulo as $otroSubtema)
                    <a href="{{ route('student.flashcards.subtema', $otroSubtema) }}" class="subtopic-pill @if($otroSubtema->id === $subtema->id) active @endif">
                        {{ $otroSubtema->nombre }}
                    </a>
                @endforeach
            </div>

            <div class="flashcards-counter mt-4">
                <span>{{ $flashcards->total() }} flashcard{{ $flashcards->total() === 1 ? '' : 's' }}</span>
            </div>

            <div class="row g-4 mt-1">
                @forelse ($flashcards as $flashcard)
                    <div class="col-12 col-md-6 col-xl-4">
                        <button type="button" class="flashcard-shell" data-flashcard-toggle>
                            <span class="flashcard-inner">
                                <span class="flashcard-face flashcard-front">
                                    <span class="flashcard-label">Pregunta</span>
                                    <span class="flashcard-text">{{ $flashcard->pregunta }}</span>
                                    <span class="flashcard-hint">Toca para revelar la respuesta</span>
                                </span>
                                <span class="flashcard-face flashcard-back">
                                    <span class="flashcard-label success">Respuesta</span>
                                    <span class="flashcard-text">{{ $flashcard->respuesta }}</span>
                                    <span class="flashcard-hint">Toca para volver a la pregunta</span>
                                </span>
                            </span>
                        </button>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state mt-3">
                            <i class="fa-regular fa-folder-open"></i>
                            <h2>No hay flashcards en este subtema</h2>
                            <p>Este subtema no tiene tarjetas cargadas todavía.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $flashcards->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                // Voltea la tarjeta principal en cada clic.
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