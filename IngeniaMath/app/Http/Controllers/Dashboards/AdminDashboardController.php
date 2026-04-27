<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $estados = DB::select("
            SELECT estado, COUNT(*) total
            FROM ejercicios
            GROUP BY estado
        ");

        $stats = DB::selectOne("
            SELECT
                COUNT(DISTINCT usuario_id) usuarios_activos,
                COUNT(*) total_respuestas,
                ROUND(
                    SUM(CASE WHEN es_correcta THEN 1 ELSE 0 END)
                    *100.0 / NULLIF(COUNT(*),0),2
                ) tasa_acierto
            FROM respuestas_usuario
        ");

        $roles = DB::select("
            SELECT r.nombre as rol, COUNT(*) as total
            FROM usuario_roles ur
            JOIN roles r ON r.id = ur.rol_id
            GROUP BY r.nombre
            ORDER BY total DESC
        ");

        $topErrores = DB::select("
            SELECT e.id, LEFT(e.enunciado,60) ejercicio,
                   COUNT(*) errores
            FROM respuestas_usuario r
            JOIN ejercicios e ON e.id = r.ejercicio_id
            WHERE r.es_correcta = false
            GROUP BY e.id, e.enunciado
            ORDER BY errores DESC
            LIMIT 5
        ");

        $modulosActivos = DB::select("
            SELECT m.nombre, COUNT(*) total
            FROM respuestas_usuario r
            JOIN ejercicios e ON e.id = r.ejercicio_id
            JOIN modulos m ON m.id = e.modulo_id
            GROUP BY m.nombre
            ORDER BY total DESC
        ");

        $sinActividad = DB::scalar("
            SELECT COUNT(*)
            FROM usuarios u
            WHERE NOT EXISTS (
                SELECT 1
                FROM respuestas_usuario r
                WHERE r.usuario_id = u.id
            )
        ");

        return view('dashboards.admin', compact(
            'estados',
            'stats',
            'roles',
            'topErrores',
            'modulosActivos',
            'sinActividad'
        ));
    }
}
