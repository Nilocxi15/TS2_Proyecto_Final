<?php

namespace App\Services;

use App\Models\Ejercicios;
use App\Models\Modulos;
use App\Models\RespuestasUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class EjercicioService
{
    /**
     * Obtiene ejercicios agrupados por módulo y dificultad
     */
    public function getByModuloYDificultad($moduloId, $dificultad, $limit = 10, $excludeIds = []): Collection
    {
        return Ejercicios::where('modulo_id', $moduloId)
            ->where('dificultad', $dificultad)
            ->whereNotIn('id', $excludeIds)
            ->where('estado', 'PUBLICADO')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Motor de recomendación: Obtiene los ejercicios basados en debilidades
     */
    public function getRecomendados($userId, $limit = 10, $excludeIds = []): Collection
    {
        // Encontrar modulo mas debil
        $peorModulo = DB::table('respuestas_usuario')
            ->join('ejercicios', 'respuestas_usuario.ejercicio_id', '=', 'ejercicios.id')
            ->select('ejercicios.modulo_id', DB::raw('SUM(CASE WHEN es_correcta THEN 1 ELSE 0 END)::float / NULLIF(COUNT(*), 0) as win_rate'))
            ->where('respuestas_usuario.usuario_id', $userId)
            ->groupBy('ejercicios.modulo_id')
            ->orderBy('win_rate', 'asc')
            ->first();

        $moduloId = $peorModulo ? $peorModulo->modulo_id : Modulos::inRandomOrder()->first()->id;

        // Evaluar ultimas 5 respuestas para dinamismo
        $lastResponses = RespuestasUsuario::join('ejercicios', 'respuestas_usuario.ejercicio_id', '=', 'ejercicios.id')
            ->where('respuestas_usuario.usuario_id', $userId)
            ->where('ejercicios.modulo_id', $moduloId)
            ->orderBy('respuestas_usuario.created_at', 'desc')
            ->limit(5)
            ->get();
            
        $dificultad = 'BASICO'; 
        if ($lastResponses->count() >= 3) {
            $correctos = $lastResponses->where('es_correcta', true)->count();
            if ($correctos >= 3) {
                $dificultad = 'INTERMEDIO';
            }
            if ($correctos >= 4) {
                $dificultad = 'AVANZADO';
            }
            if ($correctos <= 1) { // 60% de fallas
                $dificultad = 'BASICO';
            }
        }

        // Ignorar aciertos en ultimos 7 dias
        $recentCorrect = RespuestasUsuario::where('usuario_id', $userId)
            ->where('es_correcta', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->pluck('ejercicio_id')->toArray();

        $toExclude = array_merge($excludeIds, $recentCorrect);

        $ejercicios = $this->getByModuloYDificultad($moduloId, $dificultad, $limit, $toExclude);

        // Respaldo de aleatorios si no halla nada
        if ($ejercicios->isEmpty()) {
            $ejercicios = $this->getAleatorios($limit, $toExclude);
        }

        return $ejercicios;
    }

    /**
     * Obtiene por subtema especifico
     */
    public function getPorSubtema($subtemaId, $limit = 10): Collection
    {
        return Ejercicios::where('subtema_id', $subtemaId)
            ->where('estado', 'PUBLICADO')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Obtiene aleatorios en general
     */
    public function getAleatorios($limit = 10, $excludeIds = []): Collection
    {
        return Ejercicios::whereNotIn('id', $excludeIds)
            ->where('estado', 'PUBLICADO')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Prepara lote para simulacro priorizando distribucion proporcional (13 por mudulo base de 7 * 13 = 91 aproximado a total)
     */
    public function getParaSimulacro($distribucion = null, $totalPreguntas = 90): Collection
    {
        $coleccion = collect();
        $modulos = Modulos::all();
        
        if ($modulos->isEmpty()) {
            return $coleccion;
        }

        $preguntasPorModulo = (int) floor($totalPreguntas / $modulos->count());

        foreach ($modulos as $mod) {
            // Buscamos sacar mix de dificultad de examen y los demas para rellenar
            $ejs = Ejercicios::where('modulo_id', $mod->id)
                ->where('estado', 'PUBLICADO')
                ->inRandomOrder()
                ->limit($preguntasPorModulo)
                ->get();
            $coleccion = $coleccion->merge($ejs);
        }

        // Rellenar lo que falte (si la division no fue exacta p.ej 7 * 12 = 84 faltan 6)
        $faltantes = $totalPreguntas - $coleccion->count();
        if ($faltantes > 0) {
            $extra = Ejercicios::whereNotIn('id', $coleccion->pluck('id'))
                ->where('estado', 'PUBLICADO')
                ->inRandomOrder()
                ->limit($faltantes)
                ->get();
            $coleccion = $coleccion->merge($extra);
        }

        return $coleccion->shuffle();
    }
}
