@extends('layouts.app')

@section('content')
<div class="page">
    <div class="container max-w-lg mx-auto py-5 text-center">
        
        <div class="mb-4">
            @if($porcentaje >= 80)
                <img src="https://cdn3d.iconscout.com/3d/premium/thumb/trophy-4993132-4161748.png" style="width: 150px;" class="mb-3 animate__animated animate__bounceIn">
                <h1 class="font-bold fw-bolder text-warning text-uppercase is-size-2">¡Excelente Trabajo!</h1>
                <p class="text-muted fs-5">Has dominado esta sesión magistralmente.</p>
            @elseif($porcentaje >= 50)
                <img src="https://cdn3d.iconscout.com/3d/premium/thumb/target-4993137-4161753.png" style="width: 150px;" class="mb-3 animate__animated animate__bounceIn">
                <h1 class="font-bold fw-bolder text-primary text-uppercase is-size-2">Buen Esfuerzo</h1>
                <p class="text-muted fs-5">Vas por buen camino, ¡sigue practicando!</p>
            @else
                <img src="https://cdn3d.iconscout.com/3d/premium/thumb/idea-4993139-4161755.png" style="width: 150px;" class="mb-3 animate__animated animate__bounceIn">
                <h1 class="font-bold fw-bolder text-secondary text-uppercase is-size-2">Sigue intentando</h1>
                <p class="text-muted fs-5">Repasa los recursos para mejorar tu puntuación.</p>
            @endif
        </div>

        <div class="card shadow-lg border-0 mb-4 mx-auto" style="max-width: 600px; border-radius: 20px;">
            <div class="card-body p-5">
                <h4 class="mb-4 text-dark fw-bold border-bottom pb-2">Resumen de la Sesión</h4>
                
                <div class="row text-center mb-4 g-4">
                    <div class="col-6 col-md-4">
                        <div class="p-3 bg-light rounded shadow-sm">
                            <div class="fs-1 fw-bold text-dark">{{ $porcentaje }}%</div>
                            <div class="text-muted small fw-bold text-uppercase">Precisión</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-3 bg-light rounded shadow-sm">
                            <div class="fs-1 fw-bold text-success">{{ $correctas }}/{{ $total }}</div>
                            <div class="text-muted small fw-bold text-uppercase">Aciertos</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 bg-light rounded shadow-sm">
                            <div class="fs-1 fw-bold text-info"><i class="bi bi-stopwatch"></i></div>
                            <div class="fs-5 fw-bold text-dark">{{ $tiempoStr }}</div>
                            <div class="text-muted small fw-bold text-uppercase">Tiempo</div>
                        </div>
                    </div>
                </div>

                <div class="text-start">
                    <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-tags me-2"></i>Módulos practicados:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($modulosTrabajados as $mt)
                            <span class="badge bg-primary px-3 py-2" style="border-radius: 10px;">{{ $mt }}</span>
                        @empty
                            <span class="text-muted fst-italic">Ninguno registrado</span>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('student.practice') }}" class="btn btn-primary btn-lg px-5 fw-bold shadow" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                <i class="bi bi-arrow-repeat me-2"></i> OTRA PRÁCTICA
            </a>
            <a href="{{ route('student.dashboard') }}" class="btn btn-light btn-lg px-5 fw-bold shadow-sm text-secondary border" style="border-radius: 12px;">
                <i class="bi bi-house me-2"></i> IR A INICIO
            </a>
        </div>
        
    </div>
</div>
@endsection
