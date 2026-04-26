@php use App\Enums\RolEnum; @endphp

@extends('layouts.app')

@section('title', 'Foro')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/forum/forum.css') }}">
@endpush

@section('content')

    <div class="forum-page">
        <div class="forum-container">

            <div class="row justify-content-center">

                <div class="col-lg-10">

                    @if(auth()->user()->tieneRol(RolEnum::ESTUDIANTE->value))
                        {{-- Crear publicación --}}
                        <div class="forum-card mb-4">

                            <form method="POST" action="{{ route('forum.store') }}">
                                @csrf

                                <h5 class="mb-3 fw-bold">
                                    Publica tu duda
                                </h5>

                                <div class="mb-3">
                                    <select
                                        name="modulo_id"
                                        id="moduleSelect"
                                        class="form-select"
                                        required>

                                        <option value="">
                                            Selecciona módulo
                                        </option>

                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}">
                                                {{ $module->nombre }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <select
                                        name="subtema_id"
                                        id="subtopicSelect"
                                        class="form-select"
                                        required>

                                        <option value="">
                                            Selecciona subtema
                                        </option>

                                    </select>
                                </div>

                                <div class="mb-3">
                        <textarea
                            name="contenido"
                            rows="3"
                            class="form-control"
                            placeholder="Escribe aquí tu duda matemática..."
                            required></textarea>
                                </div>

                                <div class="text-end">
                                    <button class="btn forum-btn">
                                        Publicar
                                    </button>
                                </div>

                            </form>

                        </div>
                    @endif

                    {{-- FILTROS --}}
                    <div class="forum-card mb-4">

                        <form method="GET"
                              action="{{ route('forum.index') }}">

                            <h5 class="mb-3 fw-bold">
                                Buscar publicaciones
                            </h5>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Buscar contenido..."
                                        value="{{ request('search') }}">
                                </div>

                                <div class="col-md-3">
                                    <select
                                        name="module_id"
                                        id="filterModule"
                                        class="form-select">

                                        <option value="">
                                            Todos los módulos
                                        </option>

                                        @foreach($modules as $module)
                                            <option
                                                value="{{ $module->id }}"
                                                {{ request('module_id') == $module->id ? 'selected' : '' }}>

                                                {{ $module->nombre }}

                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select
                                        name="subtopic_id"
                                        id="filterSubtopic"
                                        class="form-select">

                                        <option value="">
                                            Todos los subtemas
                                        </option>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <input
                                        type="text"
                                        name="author"
                                        class="form-control"
                                        placeholder="Buscar por autor..."
                                        value="{{ request('author') }}">
                                </div>

                                <div class="col-md-6 d-grid">
                                    <button class="btn forum-btn">
                                        Filtrar resultados
                                    </button>
                                </div>

                            </div>

                        </form>

                    </div>

                    {{-- Feed --}}
                    @foreach($posts as $post)

                        <div class="forum-card post-card mb-3">

                            <div class="d-flex align-items-center mb-3">

                                <img src="{{ $post->usuario->foto_perfil }}"
                                     class="avatar">

                                <div class="ms-3">

                                    <h6 class="mb-0 fw-bold">
                                        {{ $post->usuario->nombre }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $post->created_at->diffForHumans() }}
                                    </small>

                                </div>

                                <div class="ms-auto">

                                    <span class="badge bg-primary">
                                        {{ $post->estado }}
                                    </span>

                                </div>

                            </div>

                            <p class="post-content">
                                {{ $post->contenido }}
                            </p>

                            <div class="post d-flex justify-content-between align-items-center">

                                <small class="text-muted">
                                    {{ $post->modulo->nombre ?? '' }}
                                </small>

                                <a href="{{ route('forum.show', $post->id) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    Abrir hilo

                                </a>

                            </div>

                        </div>

                    @endforeach

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection


@push('scripts')
    <script>

        function loadSubtopics(moduleId, targetSelect, selected = null)
        {
            fetch(`/student/forum/subtopics/${moduleId}`)
                .then(res => res.json())
                .then(data => {

                    targetSelect.innerHTML =
                        '<option value="">Selecciona subtema</option>';

                    data.forEach(item => {

                        let isSelected =
                            selected == item.id ? 'selected' : '';

                        targetSelect.innerHTML += `
                    <option value="${item.id}" ${isSelected}>
                        ${item.nombre}
                    </option>
                `;
                    });

                });
        }

        /* Crear publicación */
        document.getElementById('moduleSelect')
            .addEventListener('change', function () {

                loadSubtopics(
                    this.value,
                    document.getElementById('subtopicSelect')
                );

            });


        /* Filtros */
        document.getElementById('filterModule')
            .addEventListener('change', function () {

                loadSubtopics(
                    this.value,
                    document.getElementById('filterSubtopic')
                );

            });


        /* Mantener filtro al recargar */
        @if(request('module_id'))

        loadSubtopics(
            "{{ request('module_id') }}",
            document.getElementById('filterSubtopic'),
            "{{ request('subtopic_id') }}"
        );

        @endif

    </script>
@endpush
