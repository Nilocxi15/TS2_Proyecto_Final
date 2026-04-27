<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TutorDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Rendimiento por estudiante
        $estudiantes = DB::select("
            SELECT
                u.id,
                u.nombre,
                COUNT(r.id) as intentos,
                SUM(CASE WHEN r.es_correcta THEN 1 ELSE 0 END) as correctas
            FROM usuarios u
            LEFT JOIN respuestas_usuario r
                ON r.usuario_id = u.id
            GROUP BY u.id, u.nombre
            ORDER BY intentos DESC
        ");

        // 🔹 Error por módulo
        $modulosError = DB::select("
            SELECT
                m.nombre,
                ROUND(
                    COUNT(*) FILTER (WHERE r.es_correcta = false)
                    * 100.0 / NULLIF(COUNT(*),0),2
                ) as tasa_error
            FROM respuestas_usuario r
            JOIN ejercicios e ON e.id = r.ejercicio_id
            JOIN modulos m ON m.id = e.modulo_id
            GROUP BY m.nombre
            ORDER BY tasa_error DESC
        ");

        // 🔹 Cards resumen
        $totalEstudiantes = DB::scalar("
            SELECT COUNT(*) FROM usuarios
        ");

        $totalIntentos = DB::scalar("
            SELECT COUNT(*) FROM respuestas_usuario
        ");

        $promedioGeneral = DB::scalar("
            SELECT ROUND(
                SUM(CASE WHEN es_correcta THEN 1 ELSE 0 END)
                * 100.0 / NULLIF(COUNT(*),0),2
            )
            FROM respuestas_usuario
        ");

        $moduloCritico = $modulosError[0]->nombre ?? 'N/A';

        return view('dashboards.tutor', compact(
            'estudiantes',
            'modulosError',
            'totalEstudiantes',
            'totalIntentos',
            'promedioGeneral',
            'moduloCritico'
        ));
    }
}
