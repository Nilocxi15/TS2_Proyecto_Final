@extends('layouts.app')

@section('title', 'Diagnóstico inicial')

@section('content')
    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm border-0">
                    <div class="card-body p-5 text-center">

                        {{-- Icono --}}
                        <div class="mb-4">
                            <i class="fas fa-brain fa-3x text-primary"></i>
                        </div>

                        {{-- Título --}}
                        <h2 class="mb-3">
                            Diagnóstico Inicial
                        </h2>

                        {{-- Descripción --}}
                        <p class="text-muted mb-4">
                            Antes de comenzar, necesitamos conocer tu nivel actual en matemáticas.
                            Este diagnóstico nos permitirá crear una <strong>ruta de aprendizaje personalizada</strong>
                            adaptada a tus fortalezas y áreas de mejora.
                        </p>

                        {{-- Info --}}
                        <div class="row text-start mb-4">

                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-3">
                                    <i class="fas fa-list text-primary mt-1"></i>
                                    <div>
                                        <strong>7 módulos evaluados</strong>
                                        <p class="text-muted small mb-0">
                                            Cubrimos todos los temas clave del curso
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-3">
                                    <i class="fas fa-layer-group text-primary mt-1"></i>
                                    <div>
                                        <strong>Dificultad progresiva</strong>
                                        <p class="text-muted small mb-0">
                                            Preguntas desde básicas hasta avanzadas
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-3">
                                    <i class="fas fa-route text-primary mt-1"></i>
                                    <div>
                                        <strong>Ruta personalizada</strong>
                                        <p class="text-muted small mb-0">
                                            Se genera automáticamente según tu rendimiento
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex gap-3">
                                    <i class="fas fa-clock text-primary mt-1"></i>
                                    <div>
                                        <strong>Duración estimada</strong>
                                        <p class="text-muted small mb-0">
                                            10 - 15 minutos aproximadamente
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Importante --}}
                        <div class="alert alert-warning text-start">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Este diagnóstico es <strong>obligatorio</strong> y solo se realiza una vez al inicio.
                        </div>

                        {{-- Botón --}}
                        <form method="POST" action="{{ route('student.diagnostic.start') }}">
                            @csrf
                            <button class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-play me-2"></i>
                                Iniciar diagnóstico
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
