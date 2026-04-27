@extends('layouts.app')

@section('content')
    <div class="container py-5">

        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="fw-bold display-6 text-primary">
                Ruta de Aprendizaje Personalizada
            </h1>

            <p class="text-muted fs-5">
                Sigue este orden recomendado para mejorar tu rendimiento.
            </p>
        </div>

        @php
            $total = count($detalles);
            $completados = collect($detalles)->where('completado', true)->count();
            $avance = $total > 0 ? round(($completados / $total) * 100) : 0;
        @endphp

            <!-- Barra progreso -->
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Progreso General</span>
                    <span class="text-primary fw-bold">{{ $avance }}%</span>
                </div>

                <div class="progress" style="height:14px;">
                    <div class="progress-bar bg-primary"
                         style="width: {{ $avance }}%">
                    </div>
                </div>

            </div>
        </div>

        <!-- Lista -->
        <div class="row g-4">

            @foreach($detalles as $item)

                <div class="col-md-6">

                    <div class="card border-0 shadow-sm rounded-4 h-100">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div>
                                    @php
                                        if($item->prioridad < 200){
                                            $textoPrioridad = 'Alta prioridad';
                                            $colorPrioridad = 'danger';
                                        } elseif($item->prioridad < 300){
                                            $textoPrioridad = 'Media prioridad';
                                            $colorPrioridad = 'warning';
                                        } else {
                                            $textoPrioridad = 'Refuerzo';
                                            $colorPrioridad = 'success';
                                        }
                                    @endphp

                                    <span class="badge bg-{{ $colorPrioridad }} mb-2">
                                        {{ $textoPrioridad }}
                                    </span>

                                    <h5 class="fw-bold mb-1">
                                        {{ $item->subtema }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ $item->modulo }}
                                    </small>
                                </div>

                                @if($item->completado)
                                    <span class="badge bg-success">
                                Completado
                            </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                Pendiente
                            </span>
                                @endif

                            </div>

                            <div class="mb-3">
                                <small class="text-muted">
                                    Nivel de complejidad:
                                </small>

                                <div class="fw-semibold">
                                    {{ $item->nivel_complejidad }}
                                </div>
                            </div>

                            <a href="{{ route('student.practice', ['subtema_id' => $item->subtema_id]) }}"
                               class="btn btn-outline-primary w-100 rounded-pill">
                                Practicar Tema
                            </a>

                        </div>
                    </div>

                </div>

            @endforeach

        </div>

        <!-- Botón final -->
        <div class="text-center mt-5">
            <a href="{{ route('student.dashboard') }}"
               class="btn btn-primary px-5 py-2 rounded-pill">
                Volver al Dashboard
            </a>
        </div>

    </div>
@endsection
