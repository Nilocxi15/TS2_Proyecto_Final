@extends('layouts.app')
@section('title', 'Simulacro en Progreso')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold m-0">Simulacro</h3>
                <div class="bg-dark text-white px-3 py-2 rounded-pill">
                    <i class="bi bi-clock me-2"></i><span id="countdownTimer">00:00</span>
                </div>
            </div>

            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar bg-primary" id="progressBar" style="width: 0%;"></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div id="ejercicio-container"></div>
                    <div id="input-area"></div>
                    <button id="btn-comprobar" class="btn btn-success mt-3">Comprobar</button>
                    <div id="feedback" class="mt-3"></div>
                </div>
            </div>

            <form id="finishForm" action="{{ route('student.mock-exams.finish', $simulacro->id) }}" method="POST"
                class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    console.log('Script cargado correctamente');

    const ejercicios = @json($ejerciciosData);
    console.log('Ejercicios:', ejercicios);

    const simulacroId = {{ $simulacro-> id }};
    const routeAnswer = "{{ route('student.mock-exams.answer', $simulacro->id) }}";
    const csrfToken = "{{ csrf_token() }}";

    let currentIndex = 0;
    let timeRemaining = {{ $segundosRestantes }};

    const timerEl = document.getElementById('countdownTimer');
    if (timerEl) {
        setInterval(() => {
            if (timeRemaining <= 0) {
                document.getElementById('finishForm').submit();
            } else {
                timeRemaining--;
                const m = Math.floor(timeRemaining / 60);
                const s = timeRemaining % 60;
                timerEl.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }

    if (ejercicios && ejercicios.length > 0) {
        mostrarEjercicio(0);
    } else {
        document.getElementById('ejercicio-container').innerHTML = '<div class="alert alert-danger">No hay ejercicios</div>';
    }

    function mostrarEjercicio(index) {
        const ej = ejercicios[index];
        document.getElementById('ejercicio-container').innerHTML = `
            <span class="badge bg-secondary">${ej.modulo} - ${ej.subtema}</span>
            <h5 class="mt-2">Pregunta ${index + 1} de ${ejercicios.length}</h5>
            <p class="fw-bold">${ej.enunciado}</p>
        `;

        const inputArea = document.getElementById('input-area');
        if (ej.tipo === 'NUMERICO') {
            inputArea.innerHTML = '<input type="number" id="respuesta" class="form-control" placeholder="Respuesta numérica">';
        } else if (ej.tipo === 'VF') {
            inputArea.innerHTML = '<select id="respuesta" class="form-select"><option value="">Selecciona</option><option value="VERDADERO">VERDADERO</option><option value="FALSO">FALSO</option></select>';
        } else {
            inputArea.innerHTML = '<input type="text" id="respuesta" class="form-control" placeholder="Tu respuesta">';
        }

        document.getElementById('feedback').innerHTML = '';
        document.getElementById('progressBar').style.width = `${(index / ejercicios.length) * 100}%`;
    }

    document.getElementById('btn-comprobar').onclick = async function () {
        const respuesta = document.getElementById('respuesta').value;
        const ej = ejercicios[currentIndex];

        if (!respuesta) {
            document.getElementById('feedback').innerHTML = '<div class="alert alert-warning">Ingresa una respuesta</div>';
            return;
        }

        try {
            await fetch(routeAnswer, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ ejercicio_id: ej.id, respuesta: respuesta, tiempo: 0 })
            });

            currentIndex++;
            if (currentIndex < ejercicios.length) {
                mostrarEjercicio(currentIndex);
                document.getElementById('feedback').innerHTML = '<div class="alert alert-success">Respuesta guardada</div>';
            } else {
                document.getElementById('feedback').innerHTML = '<div class="alert alert-info">Finalizando...</div>';
                setTimeout(() => document.getElementById('finishForm').submit(), 500);
            }
        } catch (error) {
            document.getElementById('feedback').innerHTML = '<div class="alert alert-danger">Error</div>';
        }
    };
</script>
@endpush