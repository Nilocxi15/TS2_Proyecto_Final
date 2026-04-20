@extends('layouts.app')

@section('title', 'Banco de Ejercicios')

@section('content')
<div class="page">
    <div class="container-fluid px-0">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-grey mb-2">
                    <i class="fas fa-database me-3"></i>Banco de Ejercicios
                </h1>
                <p class="text-grey-50">Gestiona todos los ejercicios de la plataforma</p>
            </div>
            <a href="{{ route('ejercicios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Ejercicio
            </a>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('ejercicios.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Módulo</label>
                        <select name="modulo_id" class="form-select">
                            <option value="">Todos los módulos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo->id }}" {{ request('modulo_id') == $modulo->id ? 'selected' : '' }}>
                                    {{ $modulo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            @foreach($estados as $key => $nombre)
                                <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Dificultad</label>
                        <select name="dificultad" class="form-select">
                            <option value="">Todas</option>
                            @foreach($dificultades as $key => $nombre)
                                <option value="{{ $key }}" {{ request('dificultad') == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por enunciado..." 
                               value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabla de ejercicios -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Módulo</th>
                                <th>Subtema</th>
                                <th>Enunciado</th>
                                <th>Dificultad</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ejercicios as $ejercicio)
                                <tr>
                                    <td>{{ $ejercicio->id }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $ejercicio->modulo->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $ejercicio->subtema->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <div class="math-preview">
                                            {!! Str::limit($ejercicio->enunciado, 60) !!}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $difColors = [
                                                'BASICO' => 'success',
                                                'INTERMEDIO' => 'warning',
                                                'AVANZADO' => 'danger',
                                                'EXAMEN' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $difColors[$ejercicio->dificultad] ?? 'secondary' }}">
                                            {{ $dificultades[$ejercicio->dificultad] ?? $ejercicio->dificultad }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $estadoColors = [
                                                'BORRADOR' => 'secondary',
                                                'REVISION' => 'info',
                                                'APROBADO' => 'primary',
                                                'PUBLICADO' => 'success',
                                                'DESHABILITADO' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $estadoColors[$ejercicio->estado] ?? 'secondary' }}">
                                            {{ $estados[$ejercicio->estado] ?? $ejercicio->estado }}
                                        </span>
                                    </td>
                                    <td>{{ $ejercicio->created_at ? $ejercicio->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('ejercicios.show', $ejercicio->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if(in_array($ejercicio->estado, ['BORRADOR', 'DESHABILITADO']))
                                                <a href="{{ route('ejercicios.edit', $ejercicio->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            
                                            @if($ejercicio->estado != 'DESHABILITADO')
                                                <form action="{{ route('ejercicios.destroy', $ejercicio->id) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Deshabilitar este ejercicio?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Deshabilitar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No hay ejercicios registrados</p>
                                        <a href="{{ route('ejercicios.create') }}" class="btn btn-primary">
                                            Crear primer ejercicio
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($ejercicios->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $ejercicios->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .math-preview {
        background: transparent;
        padding: 0;
        border-left: none;
        font-size: 0.9rem;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-group {
        gap: 5px;
    }
</style>
@endpush