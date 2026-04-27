@extends('layouts.app')

{{-- Flujo Tutor: panel para gestionar recursos/flashcards y enviarlos a revisión. --}}
@section('title', 'Panel de Recursos')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tutor/flashcards.css') }}">
@endpush

@section('content')
    @php
        $resourceTypeLabels = [
            'PDF' => 'PDF',
            'VIDEO' => 'VIDEO',
            'SIMULADOR' => 'SIMULADOR',
        ];

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

        $resourceTypeClasses = [
            'PDF' => 'chip-pdf',
            'VIDEO' => 'chip-video',
            'SIMULADOR' => 'chip-simulador',
        ];

        $resourceIconClasses = [
            'PDF' => 'fa-solid fa-file-pdf',
            'VIDEO' => 'fa-solid fa-circle-play',
            'SIMULADOR' => 'fa-solid fa-flask-vial',
        ];

        $resourceActionConfig = [
            'createResourceUrl' => route('tutor.resources.recursos.store'),
            'createFlashcardUrl' => route('tutor.resources.flashcards.store'),
            'recursosTipos' => $recursosTipos,
            'recursosEstados' => $recursosEstados,
            'flashcardsEstados' => $flashcardsEstados,
            'recursosModulos' => $recursosModulos->map(function ($modulo) {
                return ['id' => $modulo->id, 'nombre' => $modulo->nombre];
            })->values()->all(),
            'recursosSubtemas' => $recursosSubtemas->map(function ($subtema) {
                return ['id' => $subtema->id, 'nombre' => $subtema->nombre, 'modulo_id' => $subtema->modulo_id];
            })->values()->all(),
            'flashcardsSubtemas' => $flashcardsSubtemas->map(function ($subtema) {
                return ['id' => $subtema->id, 'nombre' => $subtema->nombre, 'modulo_id' => $subtema->modulo_id];
            })->values()->all(),
        ];
    @endphp

    <section class="tutor-resources-page py-4 py-lg-5">
        <div class="container">
            <div class="tutor-hero">
                <div class="hero-copy">
                    <p class="eyebrow mb-2">Panel del tutor</p>
                    <h1 class="mb-3">Gestiona tus recursos y flashcards</h1>
                    <p class="resources-subtitle mb-0">
                        Revisa únicamente el contenido asociado a tus subtemas. Usa los filtros para localizar rápido lo que
                        necesitas y mantén el control sobre tus materiales.
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
                    <span class="stat-label">Recursos visibles</span>
                    <strong>{{ $recursosTutor->total() }}</strong>
                    <small>PDF, VIDEO y SIMULADOR</small>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Flashcards visibles</span>
                    <strong>{{ $flashcardsTutor->total() }}</strong>
                    <small>Vinculadas a tus subtemas</small>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Subtemas de trabajo</span>
                    <strong>{{ $recursosSubtemas->count() }}</strong>
                    <small>Base para ambos listados</small>
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

            @include('tutor.recursos_educativos.partials.resources_table')

            @include('tutor.recursos_educativos.partials.flashcards_table')
        </div>
    </section>

    {{-- Modal unificado de acciones (crear/editar/eliminar/estado). --}}
    @include('tutor.recursos_educativos.partials.action_modal')

    @push('scripts')
        <script src="{{ asset('js/tutor/flashcards-table-actions.js') }}"></script>
    @endpush
@endsection
