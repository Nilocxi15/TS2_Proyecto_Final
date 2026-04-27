@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="container py-4">

            <div class="mb-4">
                <h1 class="fw-bold text-primary mb-1">
                    Dashboard Administrador
                </h1>
                <p class="text-muted">
                    Control global de usuarios, actividad y banco de ejercicios.
                </p>
            </div>

            @php
                $totalEjercicios = collect($estados)->sum('total');
            @endphp

            {{-- CARDS --}}
            <div class="row g-4 mb-4">

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <small class="text-muted">Usuarios Activos</small>
                            <h2 class="fw-bold text-primary">{{ $stats->usuarios_activos }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <small class="text-muted">Respuestas Totales</small>
                            <h2 class="fw-bold text-success">{{ $stats->total_respuestas }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <small class="text-muted">% Acierto Global</small>
                            <h2 class="fw-bold text-warning">{{ $stats->tasa_acierto ?? 0 }}%</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <small class="text-muted">Sin Actividad</small>
                            <h2 class="fw-bold text-danger">{{ $sinActividad }}</h2>
                        </div>
                    </div>
                </div>

            </div>


            {{-- CHARTS 1 --}}
            <div class="row g-4 mb-4">

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold">Estado Ejercicios</h5>
                            <div id="donutChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold">Usuarios por Rol</h5>
                            <div id="rolesChart"></div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- CHARTS 2 --}}
            <div class="row g-4 mb-4">

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold">Módulos Más Usados</h5>
                            <div id="modulosChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="fw-bold">Top Ejercicios Fallados</h5>

                            @foreach($topErrores as $e)
                                <div class="border-bottom py-2">
                                    <small>{{ $e->ejercicio }}</small>
                                    <div class="fw-bold text-danger">{{ $e->errores }} errores</div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>


            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center">
                    <h5 class="fw-bold">Banco Total</h5>
                    <div class="display-6 fw-bold text-primary">
                        {{ $totalEjercicios }}
                    </div>
                </div>
            </div>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("DOMContentLoaded",()=>{

            new ApexCharts(document.querySelector("#donutChart"),{
                chart:{type:'donut',height:320},
                series:@json(array_map(fn($x)=>(int)$x->total,$estados)),
                labels:@json(array_map(fn($x)=>$x->estado,$estados))
            }).render();

            new ApexCharts(document.querySelector("#rolesChart"),{
                chart:{type:'bar',height:320},
                series:[{
                    name:'Usuarios',
                    data:@json(array_map(fn($x)=>(int)$x->total,$roles))
                }],
                xaxis:{
                    categories:@json(array_map(fn($x)=>$x->rol,$roles))
                }
            }).render();

            new ApexCharts(document.querySelector("#modulosChart"),{
                chart:{type:'bar',height:320},
                series:[{
                    name:'Intentos',
                    data:@json(array_map(fn($x)=>(int)$x->total,$modulosActivos))
                }],
                xaxis:{
                    categories:@json(array_map(fn($x)=>$x->nombre,$modulosActivos))
                }
            }).render();

        });
    </script>
@endsection
