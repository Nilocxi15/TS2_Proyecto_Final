@extends('layouts.app')

@section('title', 'Usuarios')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
@endpush

@section('content')
    <div class="admin-page">
        <div class="admin-container">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 admin-header flex-wrap">
                <div>
                    <h1 class="text-grey mb-2">
                        <i class="fas fa-users me-3"></i>Usuarios
                    </h1>
                    <p class="text-grey-50">Gestiona los usuarios del sistema</p>
                </div>

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </a>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">

                        <!-- Buscar -->
                        <div class="col-md-5">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nombre, apellido o email..."
                                   value="{{ request('search') }}">
                        </div>

                        <!-- Rol -->
                        <div class="col-md-3">
                            <label class="form-label">Rol</label>
                            <select name="rol_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}"
                                        {{ request('rol_id') == $rol->id ? 'selected' : '' }}>
                                        {{ $rol->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select name="activo" class="form-select">
                                <option value="">Todos</option>
                                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <!-- Botón -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Tabla -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover mb-0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse($usuarios as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>

                                    <td>
                                        {{ $user->nombre }} {{ $user->apellido }}
                                    </td>

                                    <td>{{ $user->email }}</td>

                                    <td>
                                        @foreach($user->roles as $rol)
                                            <span class="badge bg-info">
                                                {{ $rol->nombre }}
                                            </span>
                                        @endforeach
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $user->activo ? 'success' : 'secondary' }}">
                                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ optional($user->created_at)->format('d/m/Y') ?? 'N/A' }}
                                    </td>

                                    <td>
                                        <div class="btn-group gap-2">

                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user->id) }}"
                                                  onsubmit="return confirm('¿Desactivar usuario?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                        <p>No hay usuarios registrados</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                @if($usuarios->hasPages())
                    <div class="card-footer">
                        {{ $usuarios->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
