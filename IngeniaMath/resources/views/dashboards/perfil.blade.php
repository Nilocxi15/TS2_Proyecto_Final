@extends('layouts.app')

@section('title', 'Mi Perfil')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
@endpush

@section('content')
    <div class="admin-page">
        <div class="admin-container">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user-circle me-2"></i>Mi Perfil
                    </h2>
                    <p class="text-muted mb-0">
                        Gestiona tu información personal
                    </p>
                </div>
            </div>

            {{-- Card --}}
            <div class="card shadow-sm border-0">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">

                        <div class="row g-4">

                            {{-- Avatar --}}
                            <div class="col-md-3 text-center">

                                <img src="{{ auth()->user()->foto_perfil }}"
                                     class="rounded-circle img-fluid mb-3"
                                     style="width:120px; height:120px; object-fit:cover;">

                                {{-- (Futuro) cambiar foto --}}
                                <div class="small text-muted">
                                    Foto de perfil
                                </div>

                                <h5 class="mt-2">
                                    {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                                </h5>
                            </div>

                            {{-- Formulario --}}
                            <div class="col-md-9">

                                <div class="row g-3">

                                    {{-- Nombre --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="nombre"
                                               class="form-control"
                                               value="{{ old('nombre', auth()->user()->nombre) }}">
                                    </div>

                                    {{-- Apellido --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido</label>
                                        <input type="text" name="apellido"
                                               class="form-control"
                                               value="{{ old('apellido', auth()->user()->apellido) }}">
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Correo</label>
                                        <input type="email" name="email"
                                               class="form-control"
                                               value="{{ old('email', auth()->user()->email) }}">
                                    </div>

                                    {{-- Password --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva contraseña</label>

                                        <div class="input-group">
                                            <input type="password"
                                                   name="password"
                                                   id="password"
                                                   class="form-control"
                                                   placeholder="Opcional">

                                            <button type="button"
                                                    class="btn btn-outline-secondary"
                                                    onclick="togglePassword()">
                                                <i class="fas fa-eye" id="eyeIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Rol (solo lectura) --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Rol</label>
                                        <input type="text"
                                               class="form-control"
                                               value="{{ auth()->user()->roles->first()->nombre ?? '' }}"
                                               disabled>
                                    </div>

                                    {{-- Estado (solo lectura) --}}
                                    <div class="col-md-6">
                                        <label class="form-label">Estado</label>
                                        <input type="text"
                                               class="form-control"
                                               value="{{ auth()->user()->activo ? 'Activo' : 'Inactivo' }}"
                                               disabled>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">

                        <button class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar cambios
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>



    {{-- Script --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

@endsection
