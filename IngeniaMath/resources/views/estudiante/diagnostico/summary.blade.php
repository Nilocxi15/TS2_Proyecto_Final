@extends('layouts.app')

@section('content')

    <div class="container py-5">

        <h1 class="fw-bold mb-4">
            Resultado Diagnóstico
        </h1>

        <div class="row g-3">

            @foreach($resultados as $r)

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">

                            <h5>Módulo {{ $r->modulo_id }}</h5>

                            <h2 class="fw-bold">
                                {{ round($r->puntaje) }}%
                            </h2>

                            <span class="badge bg-primary">
                        {{ ucfirst($r->estado) }}
                    </span>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('student.dashboard') }}"
               class="btn btn-success px-5 rounded-pill">
                Continuar
            </a>
        </div>

    </div>

@endsection
