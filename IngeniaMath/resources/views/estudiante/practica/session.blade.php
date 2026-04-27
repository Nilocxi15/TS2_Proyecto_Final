@extends('layouts.app')

@section('content')
<div class="page" id="practiceApp">
    <div class="container max-w-lg mx-auto py-2">
        <!-- Cabecera de Progreso -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="m-0 text-muted fw-bold">
                <i class="bi bi-x-lg me-2 text-secondary" style="cursor: pointer;" onclick="abandonarSesion()"></i>
            </h5>
            <div class="progress flex-grow-1 mx-4" style="height: 12px; border-radius: 10px; background-color: #e2e8f0;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: linear-gradient(135deg, #667eea, #764ba2);"></div>
            </div>
            <div class="fw-bold fs-5 text-dark" id="timerDisplay">
                00:00
            </div>
        </div>

        <!-- Renderizado del componente universal -->
        <x-ejercicio-card modo="PRACTICA" />

    </div>
</div>

<!-- Forms -->
<form id="finishForm" action="{{ route('student.practice.summary', $sesion->id) }}" method="GET" class="d-none"></form>
<form id="abandonForm" action="{{ route('student.practice') }}" method="GET" class="d-none"></form>

@endsection

@push('scripts')
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="{{ asset('js/practice.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.sessionManagerInstance = new SessionManager({
            ejercicios: @json($ejercicios),
            sesionId: {{ $sesion->id }},
            routeAnswer: "{{ route('student.practice.answer', $sesion->id) }}",
            csrfToken: '{{ csrf_token() }}',
            modo: 'PRACTICA'
        });
    });

    window.abandonarSesion = function() {
        if(confirm("¿Seguro que deseas abandonar la sesión? Tu progreso no se mostrará pero el avance quedó guardado.")){
            document.getElementById('abandonForm').submit();
        }
    }
</script>
@endpush