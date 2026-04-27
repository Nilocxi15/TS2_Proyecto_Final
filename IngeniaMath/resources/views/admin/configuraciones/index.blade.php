@extends('layouts.app')
@section('title', 'Configuraciones Globales')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: var(--primary);">Configuraciones del Sistema</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="feedback success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm" style="border-radius: 16px; border: none;">
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Parámetro</th>
                                <th>Descripción</th>
                                <th style="width: 250px;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($configuraciones as $config)
                            <tr>
                                <td><code class="text-primary">{{ $config->clave }}</code></td>
                                <td class="text-muted">{{ $config->descripcion }}</td>
                                <td>
                                    <input type="text" name="{{ $config->clave }}" value="{{ old($config->clave, $config->valor) }}" class="form-control" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; padding: 10px 24px; font-weight: 600;">
                        <i class="bi bi-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
