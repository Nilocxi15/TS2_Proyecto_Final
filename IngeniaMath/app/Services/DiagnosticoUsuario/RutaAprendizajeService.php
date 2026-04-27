<?php

namespace App\Services\DiagnosticoUsuario;

use App\Models\RutasAprendizaje;
use Illuminate\Support\Facades\DB;

class RutaAprendizajeService
{
    public function generarRuta(int $usuarioId, int $diagnosticoId): void
    {
        DB::transaction(function () use ($usuarioId, $diagnosticoId) {

            // Desactivar rutas anteriores
            DB::table('rutas_aprendizaje')
                ->where('usuario_id', $usuarioId)
                ->update(['activa' => false]);

            $rutaId = DB::table('rutas_aprendizaje')->insertGetId([
                'usuario_id' => $usuarioId,
                'activa' => true
            ]);

            $modulos = DB::table('resultados_diagnostico')
                ->where('diagnostico_id', $diagnosticoId)
                ->orderByRaw("
                    CASE
                        WHEN estado = 'deficiente' THEN 1
                        WHEN estado = 'desarrollo' THEN 2
                        ELSE 3
                    END
                ")
                ->get();

            $prioridad = 1;

            foreach ($modulos as $modulo) {

                $subtemas = DB::table('subtemas')
                    ->where('modulo_id', $modulo->modulo_id)
                    ->orderBy('nivel_complejidad')
                    ->get();

                foreach ($subtemas as $subtema) {

                    DB::table('ruta_detalle')->insert([
                        'ruta_id' => $rutaId,
                        'subtema_id' => $subtema->id,
                        'prioridad' => $prioridad++,
                        'completado' => false
                    ]);
                }
            }
        });
    }
}
