@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="container py-4">

            <div class="mx-auto" style="max-width: 1400px;">

                {{-- HEADER --}}
                <div class="mb-4">
                    <h1 class="fw-bold mb-1" style="font-size:2rem; color:#2d3748;">
                        Dashboard Estudiante
                    </h1>

                    <p class="text-muted mb-0">
                        Visualiza tu rendimiento, progreso y áreas de mejora.
                    </p>
                </div>

                {{-- MÉTRICAS --}}
                <div class="row g-4 mb-4">

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <small class="text-muted">Ejercicios Resueltos</small>
                                <h2 class="fw-bold mt-2 text-primary">{{ $ejercicios }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <small class="text-muted">Simulacros</small>
                                <h2 class="fw-bold mt-2 text-success">{{ $simulacros }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <small class="text-muted">Racha Actual</small>
                                <h2 class="fw-bold mt-2 text-warning">{{ $racha }} días</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <small class="text-muted">Top Error</small>
                                <h5 class="fw-bold mt-2 text-danger">
                                    {{ $topErrores[0]->nombre ?? 'Sin datos' }}
                                </h5>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- CHARTS --}}
                <div class="row g-4">

                    {{-- RADAR --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Dominio por módulo</h5>
                                <div id="radarChart"></div>
                            </div>
                        </div>
                    </div>

                    {{-- LINE --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Progreso en el tiempo</h5>
                                <div id="lineChart"></div>
                            </div>
                        </div>
                    </div>

                    {{-- HEATMAP --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Heatmap de Actividad</h5>
                                <div id="heatmapChart"></div>
                            </div>
                        </div>
                    </div>

                    {{-- TOP ERRORES --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Top 3 módulos con errores</h5>

                                @forelse($topErrores as $m)
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>{{ $m->nombre }}</span>
                                        <span class="fw-bold text-danger">
                                        {{ $m->errores }}
                                    </span>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Sin errores registrados.</p>
                                @endforelse

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- APEXCHARTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ========================
            // RADAR
            // ========================
            const radarData = @json($puntajes);
            const radarLabels = @json($modulos);

            if (radarData.length > 0) {
                new ApexCharts(document.querySelector("#radarChart"), {
                    chart: {
                        type: 'radar',
                        height: 350
                    },
                    series: [{
                        name: 'Puntaje',
                        data: radarData
                    }],
                    labels: radarLabels,
                    yaxis: {
                        max: 100
                    }
                }).render();
            } else {
                document.querySelector("#radarChart").innerHTML =
                    '<p class="text-muted">Aún no hay diagnóstico disponible.</p>';
            }

            // ========================
            // LINE
            // ========================
            const progreso = @json($progreso);

            if (progreso.length > 0) {
                new ApexCharts(document.querySelector("#lineChart"), {
                    chart: {
                        type: 'line',
                        height: 350
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    series: [{
                        name: 'Ejercicios',
                        data: progreso.map(p => parseInt(p.total))
                    }],
                    xaxis: {
                        categories: progreso.map(p => p.fecha)
                    }
                }).render();
            } else {
                document.querySelector("#lineChart").innerHTML =
                    '<p class="text-muted">Aún no hay progreso registrado.</p>';
            }

            // ========================
            // HEATMAP
            // ========================
            const heatmap = @json($heatmap);

            if (heatmap.length > 0) {

                const dias = {
                    0: 'Dom',
                    1: 'Lun',
                    2: 'Mar',
                    3: 'Mié',
                    4: 'Jue',
                    5: 'Vie',
                    6: 'Sáb'
                };

                const grouped = {};

                heatmap.forEach(item => {

                    const dia = dias[parseInt(item.dia)];

                    if (!grouped[dia]) {
                        grouped[dia] = [];
                    }

                    grouped[dia].push({
                        x: parseInt(item.hora) + ":00",
                        y: parseInt(item.total)
                    });
                });

                const series = Object.keys(grouped).map(d => ({
                    name: d,
                    data: grouped[d]
                }));

                new ApexCharts(document.querySelector("#heatmapChart"), {
                    chart: {
                        type: 'heatmap',
                        height: 400
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: series
                }).render();

            } else {
                document.querySelector("#heatmapChart").innerHTML =
                    '<p class="text-muted">Aún no hay actividad registrada.</p>';
            }

        });
    </script>
@endsection
