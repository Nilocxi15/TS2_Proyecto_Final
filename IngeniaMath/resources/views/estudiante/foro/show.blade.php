@php use App\Enums\RolEnum; @endphp

@extends('layouts.app')

@section('title', 'Detalle del Foro')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/forum/forum.css') }}">
@endpush

@section('content')

    <div class="forum-page">
        <div class="forum-container">

            <div class="row justify-content-center">

                <div class="col-lg-10">

                    {{-- Back --}}
                    <div class="mb-3">
                        <a href="{{ route('forum.index') }}"
                           class="btn btn-outline-secondary btn-sm">
                            ← Volver al foro
                        </a>
                    </div>

                    {{-- MAIN POST --}}
                    <div class="forum-card mb-4">

                        <div class="d-flex align-items-center mb-3">

                            <img
                                src="{{ $post->usuario->foto_perfil }}"
                                class="avatar">

                            <div class="ms-3">

                                <h5 class="mb-0 fw-bold">
                                    {{ $post->usuario->nombre }}
                                </h5>

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

                        <div class="mb-3">
                    <span class="badge bg-light text-dark border">
                        {{ $post->modulo->nombre ?? 'Sin módulo' }}
                    </span>

                            @if($post->subtema)
                                <span class="badge bg-light text-dark border">
                            {{ $post->subtema->nombre }}
                        </span>
                            @endif
                        </div>

                        <p class="thread-content">
                            {{ $post->contenido }}
                        </p>

                    </div>

                    @if(auth()->user()->tieneRol(RolEnum::REVISOR->value))

                        <div class="forum-card mb-4 border-warning">

                            <h5 class="fw-bold mb-3 text-warning">
                                Panel de Moderación
                            </h5>

                            <div class="d-flex gap-2 flex-wrap">

                                @if($post->estado !== 'RESUELTO')

                                    <form method="POST"
                                          action="{{ route('forum.close', $post->id) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button class="btn btn-success">
                                            Marcar como resuelto
                                        </button>
                                    </form>

                                @endif

                                <form method="POST"
                                      action="{{ route('forum.delete-post', $post->id) }}"
                                      onsubmit="return confirm('¿Eliminar esta publicación?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger">
                                        Eliminar publicación
                                    </button>

                                </form>

                            </div>

                        </div>

                    @endif


                    {{-- ANSWERS --}}
                    <div class="forum-card mb-4">

                        <h5 class="fw-bold mb-4">
                            Respuestas
                            ({{ $post->respuestas->count() }})
                        </h5>

                        @forelse($post->respuestas as $answer)

                            <div class="answer-item mb-4">

                                <div class="d-flex align-items-center mb-2">

                                    <img
                                        src="{{ $answer->usuario->foto_perfil }}"
                                        class="avatar small-avatar">

                                    <div class="ms-3">

                                        <h6 class="mb-0 fw-bold">
                                            {{ $answer->usuario->nombre }}
                                        </h6>

                                        <small class="text-muted">
                                            {{ $answer->created_at->diffForHumans() }}
                                        </small>

                                    </div>

                                    <div class="ms-auto text-end">

                                        @if($answer->es_solucion)

                                            <span class="badge bg-success">
                                                Solución aceptada
                                            </span>

                                        @elseif(
                                            auth()->user()->tieneRol(RolEnum::ESTUDIANTE->value)
                                            && auth()->id() == $post->usuario_id
                                            && !$post->respuestas->where('es_solucion', true)->count()
                                        )

                                            <form method="POST"
                                                  action="{{ route('forum.approve', [$post->id, $answer->id]) }}">
                                                @csrf

                                                <button class="btn btn-sm btn-outline-success">
                                                    Aceptar solución
                                                </button>
                                            </form>

                                        @endif

                                        @if(auth()->user()->tieneRol(RolEnum::REVISOR->value))

                                            <form method="POST"
                                                  action="{{ route('forum.delete-answer', $answer->id) }}"
                                                  onsubmit="return confirm('¿Eliminar respuesta?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-outline-danger mt-2">
                                                    Eliminar respuesta
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </div>

                                <p class="mb-2 text-secondary">
                                    {{ $answer->contenido }}
                                </p>

                            </div>

                            @if(!$loop->last)
                                <hr>
                            @endif

                        @empty

                            <div class="text-center py-4">

                                <h6 class="fw-bold mb-2">
                                    Aún no hay respuestas
                                </h6>

                                <p class="text-muted mb-0">
                                    Cuando un tutor responda, aparecerá aquí.
                                </p>

                            </div>

                        @endforelse

                    </div>


                    @if(auth()->user()->tieneRol(RolEnum::TUTOR->value))
                        @if($post->estado !== 'RESUELTO')

                            <div class="forum-card">

                                <h5 class="fw-bold mb-3">
                                    Responder duda
                                </h5>

                                <form method="POST"
                                      action="{{ route('forum.reply', $post->id) }}">
                                    @csrf

                                    <div class="mb-3">
                                        <textarea
                                            name="contenido"
                                            rows="4"
                                            class="form-control"
                                            placeholder="Escribe una respuesta clara y útil..."
                                            required></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button class="btn forum-btn">
                                            Publicar respuesta
                                        </button>
                                    </div>

                                </form>

                            </div>

                        @else
                            <div class="forum-card border-success">

                                <h5 class="fw-bold text-success mb-2">
                                    Hilo resuelto
                                </h5>

                                <p class="text-muted mb-0">
                                    Este tema ya fue marcado como resuelto y no acepta nuevas respuestas.
                                </p>

                            </div>
                        @endif
                    @endif
                </div>

            </div>

        </div>
    </div>

@endsection
