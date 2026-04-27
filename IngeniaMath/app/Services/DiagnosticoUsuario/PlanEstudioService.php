<?php

namespace App\Services\DiagnosticoUsuario;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlanEstudioService
{
    /**
     * Obtener horas configuradas por usuario
     */
    public function obtenerHoras(int $usuarioId): ?int
    {
        $config = DB::table('configuracion_estudio')
            ->where('usuario_id', $usuarioId)
            ->first();

        return $config?->horas_semana;
    }

    /**
     * Guardar / actualizar horas semanales
     */
    public function guardarHoras(int $usuarioId, int $horas): void
    {
        DB::table('configuracion_estudio')->updateOrInsert(
            ['usuario_id' => $usuarioId],
            [
                'horas_semana' => $horas,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Obtener plan activo con detalle
     */
    public function obtenerPlanActivo(int $usuarioId)
    {
        $plan = DB::table('planes_estudio')
            ->where('usuario_id', $usuarioId)
            ->where('activo', true)
            ->orderByDesc('id')
            ->first();

        if (!$plan) {
            return null;
        }

        $plan->detalles = DB::table('plan_detalle as pd')
            ->join('subtemas as s', 'pd.subtema_id', '=', 's.id')
            ->join('modulos as m', 's.modulo_id', '=', 'm.id')
            ->select(
                'pd.*',
                's.nombre as subtema',
                'm.nombre as modulo'
            )
            ->where('pd.plan_id', $plan->id)
            ->orderBy('pd.fecha')
            ->get();

        return $plan;
    }

    /**
     * Generar plan semanal nuevo
     */
    public function generarPlan(int $usuarioId): void
    {
        DB::transaction(function () use ($usuarioId) {

            $horas = $this->obtenerHoras($usuarioId);

            if (!$horas) {
                return;
            }

            // Desactivar planes viejos
            DB::table('planes_estudio')
                ->where('usuario_id', $usuarioId)
                ->update(['activo' => false]);

            $inicio = Carbon::now()->startOfWeek();
            $fin = Carbon::now()->endOfWeek();

            $planId = DB::table('planes_estudio')
                ->insertGetId([
                    'usuario_id' => $usuarioId,
                    'fecha_inicio' => $inicio->toDateString(),
                    'fecha_fin' => $fin->toDateString(),
                    'horas_semana' => $horas,
                    'activo' => true,
                    'created_at' => now()
                ]);

            // Obtener ruta activa pendiente
            $ruta = DB::table('rutas_aprendizaje')
                ->where('usuario_id', $usuarioId)
                ->where('activa', true)
                ->first();

            if (!$ruta) {
                return;
            }

            $temas = DB::table('ruta_detalle')
                ->where('ruta_id', $ruta->id)
                ->where('completado', false)
                ->orderBy('prioridad')
                ->limit(7)
                ->get();

            if ($temas->isEmpty()) {
                return;
            }

            $minutosPorSemana = $horas * 60;
            $dias = max($temas->count(), 1);
            $minutosPorDia = floor($minutosPorSemana / $dias);

            foreach ($temas as $index => $tema) {

                $fecha = $inicio->copy()->addDays($index);

                $cantidadEjercicios = max(
                    floor($minutosPorDia / 8),
                    5
                );

                DB::table('plan_detalle')->insert([
                    'plan_id' => $planId,
                    'fecha' => $fecha->toDateString(),
                    'subtema_id' => $tema->subtema_id,
                    'ejercicios_recomendados' => $cantidadEjercicios,
                    'tiempo_estimado' => $minutosPorDia,
                    'completado' => false
                ]);
            }
        });
    }
}
