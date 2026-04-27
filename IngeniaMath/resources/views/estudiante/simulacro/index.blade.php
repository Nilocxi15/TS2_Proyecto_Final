@extends('layouts.app')
@section('title', 'Simulacros de Examen')

@section('content')
<div class="page-two-columns" style="display: block; padding: 2rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color: var(--primary);">Simulacros de Examen</h2>
            <p class="text-muted">Mide tu nivel con condiciones reales de examen.</p>
        </div>
        <form action="{{ route('student.mock-exams.start') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600;">
                <i class="bi bi-play-circle me-2"></i> Iniciar Nuevo Simulacro
            </button>
        </form>
    </div>

    @if(session('error'))
        <div class="feedback error mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius: 16px; border: none;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Historial de Simulacros</h5>
                    @if($simulacros->isEmpty())
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-clipboard-x" style="font-size: 3rem;"></i>
                            <p class="mt-3">Aún no has realizado ningún simulacro.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Duración Asignada</th>
                                        <th>Puntaje</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($simulacros as $sim)
                                    <tr>
                                        <td>{{ $sim->fecha->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sim->duracion }} min</td>
                                        <td>
                                            @if($sim->puntaje !== null)
                                                <span class="badge {{ $sim->puntaje >= 61 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $sim->puntaje }}%
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">En progreso</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sim->puntaje !== null)
                                                <a href="{{ route('student.mock-exams.results', $sim->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Ver Resultados
                                                </a>
                                            @else
                                                <a href="{{ route('student.mock-exams.session', $sim->id) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                                                    Continuar
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4" style="border-radius: 16px; border: none;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Evolución de Puntaje</h5>
                    @if($simulacros->whereNotNull('puntaje')->count() >= 2)
                        <canvas id="evolucionChart" height="200"></canvas>
                    @else
                        <div class="text-center p-3 text-muted">
                            <small>Necesitas al menos 2 simulacros finalizados para ver tu gráfica de evolución.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($simulacros->whereNotNull('puntaje')->count() >= 2)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('evolucionChart').getContext('2d');
    const labels = {!! json_encode($simulacros->whereNotNull('puntaje')->sortBy('fecha')->pluck('fecha')->map(fn($d) => $d->format('d/m'))->values()) !!};
    const data = {!! json_encode($simulacros->whereNotNull('puntaje')->sortBy('fecha')->pluck('puntaje')->values()) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Puntaje (%)',
                data: data,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endif
@endsection
