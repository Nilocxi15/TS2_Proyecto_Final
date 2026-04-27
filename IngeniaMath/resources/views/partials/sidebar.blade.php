@php use App\Enums\RolEnum; @endphp

<div class="sidebar">

    <ul class="nav flex-column gap-2">

        {{-- ===================== --}}
        {{-- ESTUDIANTE --}}
        {{-- ===================== --}}
        @if(auth()->user()->tieneRol(RolEnum::ESTUDIANTE->value))


            <li>
                <a href="{{ route('student.dashboard') }}" class="nav-link">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </li>

            <li>
                <a href="{{ route('student.learning-route') }}" class="nav-link">
                    <i class="bi bi-compass"></i> Mi Ruta de Aprendizaje
                </a>
            </li>

            <li>
                <a href="{{ route('student.resources') }}" class="nav-link">
                    <i class="bi bi-book"></i> Recursos Educativos
                </a>
            </li>

            <li>
                <a href="{{ route('forum.index') }}" class="nav-link">
                    <i class="bi bi-chat"></i> Foro
                </a>
            </li>

            <li>
                <a href="{{ route('student.practice') }}" class="nav-link">
                    <i class="bi bi-pencil"></i> Practicar Ejercicios
                </a>
            </li>

            <li>
                <a href="{{ route('student.study-plan.index') }}" class="nav-link">
                    <i class="bi bi-calendar"></i> Plan de estudio
                </a>
            </li>

            <li>
                <a href="{{ route('student.mock-exams') }}" class="nav-link">
                    <i class="bi bi-clipboard"></i> Simulacros de Exámenes
                </a>
            </li>
        @endif


        {{-- ===================== --}}
        {{-- TUTOR --}}
        {{-- ===================== --}}
        @if(auth()->user()->tieneRol(RolEnum::TUTOR->value))

            <li class="nav-item">
                <a class="nav-link" href="{{ route('tutor.dashboard') }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('tutor.exercises.index') }}">
                    <i class="fas fa-database me-1"></i> Ejercicios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tutor.exercises.create') }}">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo Ejercicio
                </a>
            </li>

            <li>
                <a href="{{ route('forum.index') }}" class="nav-link">
                    <i class="bi bi-chat"></i> Foro
                </a>
            </li>

            <li>
                <a href="{{ route('tutor.resources.flashcards.show') }}" class="nav-link">
                    <i class="bi bi-book"></i> Recursos Educativos
                </a>
            </li>

        @endif


        {{-- ===================== --}}
        {{-- ADMIN --}}
        {{-- ===================== --}}
        @if(auth()->user()->tieneRol(RolEnum::ADMINISTRADOR->value))

            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-link">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>

            <li>
                <a href="{{ route('forum.index') }}" class="nav-link">
                    <i class="bi bi-chat"></i> Foro
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings.index') }}" class="nav-link">
                    <i class="fas fa-cog"></i> Configuraciones
                </a>
            </li>

        @endif


        {{-- ===================== --}}
        {{-- REVISOR --}}
        {{-- ===================== --}}
        @if(auth()->user()->tieneRol(RolEnum::REVISOR->value))

            <li>
                <a href="{{ route('moderador.dashboard') }}" class="nav-link">
                    <i class="fas fa-shield-alt"></i> Panel
                </a>
            </li>

            <li>
                <a href="{{ route('moderador.exercises.revisions') }}" class="nav-link">
                    <i class="fas fa-clipboard-list"></i> Revisar Ejercicios
                </a>
            </li>

            <li>
                <a href="{{ route('moderador.resources.revisions') }}" class="nav-link">
                    <i class="bi bi-book"></i> Revisar Recursos
                </a>
            </li>

            <li>
                <a href="{{ route('forum.index') }}" class="nav-link">
                    <i class="bi bi-chat"></i> Foro
                </a>
            </li>

        @endif

    </ul>
</div>