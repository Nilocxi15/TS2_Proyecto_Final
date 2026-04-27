<?php

namespace App\Services\DiagnosticoUsuario;

use App\Models\Diagnosticos;
use App\Models\ResultadosDiagnostico;
use Illuminate\Support\Facades\DB;

class DiagnosticoService
{
    public function generarDiagnostico(int $usuarioId): Diagnosticos
    {
        return DB::transaction(function () use ($usuarioId) {

            $diagnostico = Diagnosticos::create([
                'usuario_id' => $usuarioId
            ]);

            // Obtener resultados por módulo
            $resultados = DB::table('respuestas_usuario as r')
                ->join('ejercicios as e', 'r.ejercicio_id', '=', 'e.id')
                ->select(
                    'e.modulo_id',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN r.es_correcta THEN 1 ELSE 0 END) as correctas')
                )
                ->where('r.usuario_id', $usuarioId)
                ->groupBy('e.modulo_id')
                ->get();

            foreach ($resultados as $r) {

                $puntaje = $r->total > 0
                    ? ($r->correctas / $r->total) * 100
                    : 0;

                $estado = $this->clasificar($puntaje);

                DB::table('resultados_diagnostico')->insert([
                    'diagnostico_id' => $diagnostico->id,
                    'modulo_id' => $r->modulo_id,
                    'puntaje' => $puntaje,
                    'estado' => $estado
                ]);
            }

            return $diagnostico;
        });
    }

    private function clasificar(float $puntaje): string
    {
        if ($puntaje >= 80) return 'dominado';
        if ($puntaje >= 50) return 'desarrollo';
        return 'deficiente';
    }
}
