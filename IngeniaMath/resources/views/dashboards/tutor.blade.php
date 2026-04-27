@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="container py-4">

            <div class="mb-4">
                <h1 class="fw-bold text-primary mb-1">
                    Dashboard Tutor
                </h1>
                <p class="text-muted mb-0">
                    Estadísticas generales del rendimiento estudiantil.
                </p>
            </div>

            {{-- 🔹 CARDS --}}
            <div class="row g-4 mb-4">

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <small class="text-muted">Estudiantes</small>
                            <h2 class="fw-bold text-primary mb-0">
                                {{ $totalEstudiantes }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <small class="text-muted">Intentos Totales</small>
                            <h2 class="fw-bold text-success mb-0">
                                {{ $totalIntentos }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <small class="text-muted">Promedio General</small>
                            <h2 class="fw-bold text-warning mb-0">
                                {{ $promedioGeneral ?? 0 }}%
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <small class="text-muted">Módulo Crítico</small>
                            <h6 class="fw-bold text-danger mb-0">
                                {{ $moduloCritico }}
                            </h6>
                        </div>
                    </div>
                </div>

            </div>


            {{-- 🔹 CHART + TOP MODULOS --}}
            <div class="row g-4 mb-4">

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">
                                Tasa de Error por Módulo
                            </h5>

                            <div id="barChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Módulos con más error
                            </h5>

                            @foreach($modulosError as $m)
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <span>{{ $m->nombre }}</span>
                                    <span class="fw-bold text-danger">
                                    {{ $m->tasa_error }}%
                                </span>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>


            {{-- 🔹 TABLA ESTUDIANTES --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Rendimiento por Estudiante
                    </h5>

                    <div class="table-responsive">
                        <table class="table align-middle">

                            <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Intentos</th>
                                <th>Correctas</th>
                                <th>Precisión</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($estudiantes as $e)

                                @php
                                    $precision = $e->intentos > 0
                                        ? round(($e->correctas / $e->intentos) * 100)
                                        : 0;
                                @endphp

                                <tr>
                                    <td class="fw-semibold">
                                        {{ $e->nombre }}
                                    </td>

                                    <td>
                                        {{ $e->intentos }}
                                    </td>

                                    <td class="text-success fw-bold">
                                        {{ $e->correctas }}
                                    </td>

                                    <td style="width:220px;">
                                        <div class="d-flex align-items-center gap-2">

                                            <div class="progress flex-grow-1"
                                                 style="height:10px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $precision }}%">
                                                </div>
                                            </div>

                                            <small class="fw-bold">
                                                {{ $precision }}%
                                            </small>

                                        </div>
                                    </td>
                                </tr>

                            @endforeach

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- APEXCHARTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            new ApexCharts(document.querySelector("#barChart"), {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show:false }
                },
                series: [{
                    name: 'Tasa Error',
                    data: @json(array_map(fn($m) => (float)$m->tasa_error, $modulosError))
                }],
                xaxis: {
                    categories: @json(array_map(fn($m) => $m->nombre, $modulosError))
                },
                dataLabels: {
                    enabled: true
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: false
                    }
                }
            }).render();

        });
    </script>
@endsection
