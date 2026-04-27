<?php

namespace App\Services\DiagnosticoUsuario;

use App\Models\RutasAprendizaje;
use Illuminate\Support\Facades\DB;

class RutaAprendizajeService
{
    public function generarRuta(int $usuarioId, int $diagnosticoId): void
    {
        DB::transaction(function () use ($usuarioId, $diagnosticoId) {

            DB::table('rutas_aprendizaje')
                ->where('usuario_id', $usuarioId)
                ->update(['activa' => false]);

            $rutaId = DB::table('rutas_aprendizaje')->insertGetId([
                'usuario_id' => $usuarioId,
                'activa' => true
            ]);

            $modulos = DB::table('resultados_diagnostico')
                ->where('diagnostico_id', $diagnosticoId)
                ->get();

            $prioridad = 1;

            foreach ($modulos as $modulo) {

                $pesoModulo = match ($modulo->estado) {
                    'deficiente' => 1,
                    'desarrollo' => 2,
                    default => 3
                };

                $subtemas = DB::table('subtemas')
                    ->where('modulo_id', $modulo->modulo_id)
                    ->orderBy('nivel_complejidad')
                    ->get();

                foreach ($subtemas as $subtema) {

                    // insertar prerequisitos primero
                    $this->insertarPrerequisitos(
                        $rutaId,
                        $subtema->id,
                        $prioridad
                    );

                    $yaExiste = DB::table('ruta_detalle')
                        ->where('ruta_id', $rutaId)
                        ->where('subtema_id', $subtema->id)
                        ->exists();

                    if ($yaExiste) {
                        continue;
                    }

                    DB::table('ruta_detalle')->insert([
                        'ruta_id' => $rutaId,
                        'subtema_id' => $subtema->id,
                        'prioridad' => ($pesoModulo * 100) + $prioridad++,
                        'completado' => false
                    ]);
                }
            }
        });
    }

    private function insertarPrerequisitos($rutaId, $subtemaId, &$prioridad): void
    {
        $mapa = $this->prerrequisitos();

        if (!isset($mapa[$subtemaId])) {
            return;
        }

        foreach ($mapa[$subtemaId] as $preId) {

            $existe = DB::table('ruta_detalle')
                ->where('ruta_id', $rutaId)
                ->where('subtema_id', $preId)
                ->exists();

            if (!$existe) {
                DB::table('ruta_detalle')->insert([
                    'ruta_id' => $rutaId,
                    'subtema_id' => $preId,
                    'prioridad' => $prioridad++,
                    'completado' => false
                ]);
            }
        }
    }

    public function evaluarRetrocesoPorFallo($usuarioId, $subtemaId): void
    {
        $ruta = DB::table('rutas_aprendizaje')
            ->where('usuario_id', $usuarioId)
            ->where('activa', true)
            ->first();

        if (!$ruta) return;

        $prioridad = DB::table('ruta_detalle')
                ->where('ruta_id', $ruta->id)
                ->max('prioridad') + 1;

        $this->insertarPrerequisitos($ruta->id, $subtemaId, $prioridad);
    }

    private function prerrequisitos(): array
    {
        return [

            // Álgebra avanzada
            8  => [7],          // Factorización ← Productos Notables
            9  => [8],          // Fracciones Algebraicas ← Factorización
            10 => [8],          // Residuo ← Factorización

            // Ecuaciones
            12 => [11],         // Cuadráticas ← Primer grado
            13 => [12],         // Reducibles ← Cuadráticas
            14 => [11],         // Inecuaciones ← Primer grado

            // Funciones
            17 => [16,15],      // Cuadrática ← Lineal + Dominio
            18 => [16,17],      // Composición ← lineal/cuadrática

            // Geometría Analítica
            19 => [16],         // Recta ← función lineal
            20 => [19],         // distancia ← recta
            21 => [20],         // circunferencia ← distancia
            22 => [17,21],      // parábola ← cuadrática + circunferencia

            // Geometría
            24 => [23],         // semejanza ← pitágoras base geométrica
            26 => [25],         // volúmenes ← áreas

            // Trigonometría
            28 => [27],         // funciones trig ← radianes
            29 => [28,24],      // triángulos ← trig + semejanza
        ];
    }
}
