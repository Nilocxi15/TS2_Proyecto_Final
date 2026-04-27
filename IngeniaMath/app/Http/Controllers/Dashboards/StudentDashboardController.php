<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 🔹 Radar (último diagnóstico)
        $radar = DB::select("
            SELECT
                m.nombre,
                ROUND(
                    (
                        SUM(CASE WHEN r.es_correcta = true THEN 1 ELSE 0 END)::decimal
                        / NULLIF(COUNT(r.id),0)
                    ) * 100,
                2) as puntaje

            FROM modulos m

            LEFT JOIN ejercicios e
                ON e.modulo_id = m.id

            LEFT JOIN respuestas_usuario r
                ON r.ejercicio_id = e.id
                AND r.usuario_id = ?

            GROUP BY m.id, m.nombre
            ORDER BY m.id
        ", [$userId]);

        $modulos = array_column($radar, 'nombre');
        $puntajes = array_map(fn($r) => (float)$r->puntaje, $radar);

        // 🔹 Progreso en el tiempo
        $progreso = DB::select("
            SELECT DATE(created_at) as fecha, COUNT(*) as total
            FROM respuestas_usuario
            WHERE usuario_id = ?
            GROUP BY fecha
            ORDER BY fecha
        ", [$userId]);

        // 🔹 Heatmap
        $heatmap = DB::select("
            SELECT
                EXTRACT(DOW FROM created_at) as dia,
                EXTRACT(HOUR FROM created_at) as hora,
                COUNT(*) as total
            FROM respuestas_usuario
            WHERE usuario_id = ?
            GROUP BY dia, hora
        ", [$userId]);

        // 🔹 Métricas
        $ejercicios = DB::scalar("
            SELECT COUNT(*) FROM respuestas_usuario WHERE usuario_id = ?
        ", [$userId]);

        $simulacros = DB::scalar("
            SELECT COUNT(*) FROM simulacros WHERE usuario_id = ?
        ", [$userId]);

        // 🔹 Top 3 módulos con error
        $topErrores = DB::select("
            SELECT m.nombre, COUNT(*) as errores
            FROM respuestas_usuario r
            JOIN ejercicios e ON e.id = r.ejercicio_id
            JOIN modulos m ON m.id = e.modulo_id
            WHERE r.usuario_id = ?
            AND r.es_correcta = false
            GROUP BY m.nombre
            ORDER BY errores DESC
            LIMIT 3
        ", [$userId]);

        // 🔹 Racha (en PHP)
        $fechas = DB::select("
            SELECT DATE(created_at) as fecha
            FROM respuestas_usuario
            WHERE usuario_id = ?
            GROUP BY fecha
            ORDER BY fecha DESC
        ", [$userId]);

        $racha = $this->calcularRacha($fechas);

        return view('dashboards.estudiante', compact(
            'modulos',
            'puntajes',
            'progreso',
            'heatmap',
            'ejercicios',
            'simulacros',
            'topErrores',
            'racha'
        ));
    }

    private function calcularRacha($fechas)
    {
        $racha = 0;
        $hoy = now()->toDateString();

        foreach ($fechas as $f) {
            if ($f->fecha == $hoy) {
                $racha++;
                $hoy = now()->subDays($racha)->toDateString();
            } else {
                break;
            }
        }

        return $racha;
    }
}
