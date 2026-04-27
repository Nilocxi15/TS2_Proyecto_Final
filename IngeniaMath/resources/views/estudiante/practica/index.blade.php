@extends('layouts.app')

@section('content')
<div class="page">
    <div class="container py-4">
        <h2 class="mb-4 fw-bold" style="color: #4a5568;"><i class="bi bi-pencil-square me-2"></i> Práctica de Ejercicios</h2>
        
        @if(session('error'))
            <div class="alert alert-danger fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">
            <!-- Práctica Libre -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body p-4 text-center">
                        <div class="mb-4" style="font-size: 3rem; color: #667eea;">
                            <i class="bi bi-controller"></i>
                        </div>
                        <h4 class="card-title fw-bold mb-3">Práctica Libre</h4>
                        <p class="text-muted mb-4">Elige el tema y la dificultad específica que deseas repasar a tu propio ritmo.</p>
                        
                        <form action="{{ route('student.practice.start-free') }}" method="POST">
                            @csrf
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold">Módulo Temático</label>
                                <select name="modulo_id" class="form-select input-app" required>
                                    <option value="" disabled selected>Selecciona un módulo...</option>
                                    @foreach($modulos as $mod)
                                        <option value="{{ $mod->id }}">{{ $mod->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4 text-start">
                                <label class="form-label fw-bold">Nivel de Dificultad</label>
                                <select name="dificultad" class="form-select input-app" required>
                                    <option value="" disabled selected>Selecciona la dificultad...</option>
                                    @foreach($dificultades as $dif)
                                        <option value="{{ $dif }}">{{ ucfirst(strtolower($dif)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Iniciar Práctica Libre <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Práctica Guiada -->
            <div class="col-md-6">
                <div class="card h-100" style="background: linear-gradient(135deg, rgba(102,126,234,0.05), rgba(118,75,162,0.05));">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                        <div class="mb-4" style="font-size: 3rem; color: #764ba2;">
                            <i class="bi bi-compass"></i>
                        </div>
                        <h4 class="card-title fw-bold mb-3">Práctica Guiada</h4>
                        <p class="text-muted mb-4">Deja que el motor de recomendación seleccione los ejercicios de acuerdo a tu nivel y áreas de mejora.</p>
                        
                        <form action="{{ route('student.practice.start-guided') }}" method="POST" class="mt-auto">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-3 shadow-lg" style="font-size: 1.1rem; border-radius: 12px;">
                                <i class="bi bi-stars me-2"></i> Iniciar Práctica Recomendada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
