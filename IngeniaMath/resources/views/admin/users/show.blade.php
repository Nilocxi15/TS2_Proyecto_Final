@extends('layouts.app')

@section('title', 'Detalle Usuario')

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
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user me-2"></i>Detalle de Usuario
                    </h2>
                    <p class="text-muted mb-0">
                        Información completa del usuario
                    </p>
                </div>

                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>

            {{-- Card principal --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- Avatar --}}
                        <div class="col-md-3 text-center">
                            <img src="{{ $usuario->foto_perfil }}"
                                 alt="Foto"
                                 class="rounded-circle img-fluid mb-3"
                                 style="width:120px; height:120px; object-fit:cover;">

                            <h5 class="mb-0">
                                {{ $usuario->nombre }} {{ $usuario->apellido }}
                            </h5>

                            <small class="text-muted">
                                ID: {{ $usuario->id }}
                            </small>
                        </div>

                        {{-- Información --}}
                        <div class="col-md-9">

                            <div class="row g-3">

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Correo</label>
                                    <div class="fw-semibold">
                                        {{ $usuario->email }}
                                    </div>
                                </div>

                                {{-- Estado --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Estado</label>
                                    <div>
                                <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }}">
                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                                    </div>
                                </div>

                                {{-- Roles --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Rol</label>
                                    <div>
                                        @foreach($usuario->roles as $rol)
                                            <span class="badge bg-info text-dark">
                                        {{ $rol->nombre }}
                                    </span>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Fecha --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Fecha de registro</label>
                                    <div class="fw-semibold">
                                        {{ optional($usuario->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Acciones --}}
                <div class="card-footer bg-white d-flex justify-content-end gap-2 flex-wrap">

                    <a href="{{ route('admin.users.edit', $usuario->id) }}"
                       class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>

                    <form method="POST"
                          action="{{ route('admin.users.destroy', $usuario->id) }}"
                          onsubmit="return confirm('¿Desactivar usuario?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outline-danger">
                            <i class="fas fa-trash me-1"></i>Desactivar
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>

@endsection
