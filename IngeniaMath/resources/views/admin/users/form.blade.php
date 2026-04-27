@extends('layouts.app')

@section('title', $isEdit ? 'Editar Usuario' : 'Crear Usuario')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush


@section('content')
    <div class="page">
        <div class="container">

            <div class="register-shell">

                {{-- Lado izquierdo --}}
                <div class="brand-column">
                    <p class="eyebrow">IngeniaMath</p>
                    <h1>
                        {{ $isEdit ? 'Editar usuario' : 'Crear nuevo usuario' }}
                    </h1>
                    <p class="subtitle">
                        Gestiona los usuarios del sistema desde este panel.
                    </p>
                </div>

                {{-- Formulario --}}
                <div class="form-column">

                    <form method="POST"
                          class="register-form"
                          action="{{ $isEdit ? route('admin.users.update', $usuario->id) : route('admin.users.store') }}">

                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <h2>{{ $isEdit ? 'Actualizar' : 'Registro' }}</h2>

                        {{-- Rol --}}
                        <div class="field-group">
                            <label>Rol</label>
                            <select name="rol_id">
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}"
                                        {{ old('rol_id', $usuario->roles->first()->id ?? '') == $rol->id ? 'selected' : '' }}>
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nombre --}}
                        <div class="field-group">
                            <label>Nombre</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $usuario->nombre) }}">
                        </div>

                        {{-- Apellido --}}
                        <div class="field-group">
                            <label>Apellido</label>
                            <input type="text" name="lastname"
                                   value="{{ old('lastname', $usuario->apellido) }}">
                        </div>

                        {{-- Email --}}
                        <div class="field-group">
                            <label>Correo electrónico</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $usuario->email) }}">
                        </div>

                        {{-- Password --}}
                        <div class="field-group password-group">
                            <label>Contraseña</label>

                            <div class="input-wrapper">
                                <input type="password" name="password" id="password">

                                <button type="button" class="toggle-password" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-primary">
                                {{ $isEdit ? 'Actualizar' : 'Crear' }}
                            </button>

                            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                                Cancelar
                            </a>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
@endsection

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
