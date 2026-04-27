@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="container py-4">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold mb-1" style="color:#4a5568;">
                        <i class="bi bi-calendar-check me-2"></i>
                        Plan de Estudio Semanal
                    </h2>
                    <p class="text-muted mb-0">
                        Organiza tu semana según tu ruta de aprendizaje.
                    </p>
                </div>
            </div>

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="alert alert-success shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- CONFIGURAR HORAS --}}
            @if(!$horas)

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-3">
                            <i class="bi bi-clock-history me-2"></i>
                            Configura tu disponibilidad
                        </h4>

                        <p class="text-muted">
                            Indica cuántas horas puedes estudiar por semana para generar tu plan personalizado.
                        </p>

                        <form method="POST"
                              action="{{ route('student.study-plan.save-hours') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Horas disponibles por semana
                                </label>

                                <input type="number"
                                       name="horas_semana"
                                       min="1"
                                       max="80"
                                       class="form-control form-control-lg"
                                       placeholder="Ej: 7"
                                       required>
                            </div>

                            <button class="btn btn-primary px-4">
                                <i class="bi bi-stars me-2"></i>
                                Generar Plan
                            </button>
                        </form>

                    </div>
                </div>

            @else

                {{-- RESUMEN --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <small class="text-muted">Horas semanales</small>
                                <h3 class="fw-bold mb-0">{{ $horas }} h</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <small class="text-muted">Periodo</small>
                                <h6 class="fw-bold mb-0">
                                    {{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d/m') }}
                                    -
                                    {{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d/m') }}
                                </h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <small class="text-muted">Actividades</small>
                                <h3 class="fw-bold mb-0">
                                    {{ count($plan->detalles) }}
                                </h3>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- BOTON REGENERAR --}}
                <div class="mb-4">
                    <form method="POST"
                          action="{{ route('student.study-plan.save-hours') }}"
                          class="d-flex gap-2 flex-wrap">
                        @csrf

                        <input type="number"
                               name="horas_semana"
                               min="1"
                               max="80"
                               value="{{ $horas }}"
                               class="form-control"
                               style="max-width:140px">

                        <button class="btn btn-outline-primary">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Regenerar plan
                        </button>
                    </form>
                </div>

                {{-- LISTA DIARIA --}}
                <div class="row g-4">

                    @foreach($plan->detalles as $dia)

                        <div class="col-md-6 col-xl-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">

                                <div class="card-body p-4">

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-primary">
                                        {{ \Carbon\Carbon::parse($dia->fecha)->locale('es')->translatedFormat('l') }}
                                    </span>

                                        @if($dia->completado)
                                            <span class="badge bg-success">
                                            Completado
                                        </span>
                                        @endif
                                    </div>

                                    <h5 class="fw-bold mb-1">
                                        {{ $dia->subtema }}
                                    </h5>

                                    <p class="text-muted small mb-3">
                                        {{ $dia->modulo }}
                                    </p>

                                    <div class="small mb-2">
                                        <i class="bi bi-pencil-square me-2"></i>
                                        {{ $dia->ejercicios_recomendados }} ejercicios
                                    </div>

                                    <div class="small mb-4">
                                        <i class="bi bi-clock me-2"></i>
                                        {{ $dia->tiempo_estimado }} min
                                    </div>

                                    <div class="d-grid gap-2">

                                        <a href="{{ route('student.resources', ['subtema' => $dia->subtema_id]) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-book me-1"></i>
                                            Ver recursos
                                        </a>

                                        <a href="{{ route('student.flashcards.subtema', $dia->subtema_id) }}"
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-layers me-1"></i>
                                            Flashcards
                                        </a>

                                        <a href="{{ route('student.practice') }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-play-fill me-1"></i>
                                            Practicar
                                        </a>

                                    </div>

                                </div>

                            </div>
                        </div>

                    @endforeach

                </div>

            @endif

        </div>
    </div>
@endsection
