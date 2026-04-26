@extends('layouts.app')

@section('title', 'Flashcards')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estudiante/flashcards-galery.css') }}">
@endpush

@section('content')
    <section>
        <div class="container">
            <div class="resources-header">
                <p class="eyebrow mb-2">Biblioteca Académica</p>
                <h1 class="mb-2">Flashcards</h1>
                <p class="resources-subtitle mb-0">
                    Explora nuestra colección de flashcards diseñadas para ayudarte a repasar y memorizar conceptos clave de
                    matemáticas. Cada flashcard contiene una pregunta o concepto en un lado y la respuesta o explicación en
                    el otro, facilitando el aprendizaje activo y la retención de información. Perfectas para estudiar antes
                    de exámenes o para reforzar tus conocimientos de manera rápida y efectiva.
                </p>
                <p></p>
                <p class="resource-subtitle mb-0">
                    Y recuerda "¡Id y enseñad a todos!"
                </p>
            </div>
        </div>
    </section>
@endsection