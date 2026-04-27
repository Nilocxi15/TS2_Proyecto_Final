@extends('layouts.app')

@section('content')
    <div class="page" id="practiceApp">
        <div class="container max-w-lg mx-auto py-2">

            <!-- Cabecera -->
            <div class="d-flex align-items-center justify-content-between mb-4">

                <h5 class="m-0 text-muted fw-bold">
                    Diagnóstico Inicial
                </h5>

                <div class="progress flex-grow-1 mx-4"
                     style="height:12px;border-radius:10px;background:#e2e8f0;">

                    <div id="progressBar"
                         class="progress-bar progress-bar-striped progress-bar-animated"
                         style="width:0%">
                    </div>
                </div>

                <div class="fw-bold fs-5 text-dark" id="timerDisplay">
                    00:00
                </div>

            </div>

            <x-ejercicio-card modo="DIAGNOSTICO" />

        </div>
    </div>

    <form id="finishForm"
          action="{{ route('student.diagnostic.summary', $sesion->id) }}"
          method="GET"
          class="d-none">
    </form>

@endsection

@push('scripts')

    <script src="{{ asset('js/practice.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            window.sessionManagerInstance = new SessionManager({
                ejercicios: @json($ejercicios),
                sesionId: {{ $sesion->id }},
                routeAnswer: "{{ route('student.diagnostic.answer', $sesion->id) }}",
                csrfToken: '{{ csrf_token() }}',
                modo: 'DIAGNOSTICO'
            });

        });
    </script>

@endpush
