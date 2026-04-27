<?php

namespace App\Traits;

use App\Models\SesionesPractica;
use App\Models\RespuestasUsuario;
use App\Models\Ejercicios;
use Illuminate\Database\Eloquent\Collection;

trait GestionaSesionesEjercicios
{
    /**
     * Inicializa una sesión de práctica o simulacro en la base de datos
     * y extrae los correspondientes identificadores de los ejercicios para usarse en frontend.
     */
    public function iniciarSesion(string $modo, array $ejerciciosIds): SesionesPractica
    {
        $sesion = new SesionesPractica();
        $sesion->usuario_id = auth()->id();
        $sesion->modo = $modo;
        $sesion->save();

        session(["{$modo}_ejercicios" => $ejerciciosIds]);

        return $sesion;
    }

    /**
     * Guarda la evaluación de una respuesta en la sesión actual
     */
    public function guardarRespuesta(int $sesionId, int $ejercicioId, string $respuesta, int $tiempoSegundos = 0): array
    {
        $ejercicio = Ejercicios::findOrFail($ejercicioId);
        
        $validator = app(\App\Services\RespuestaValidator::class);
        $esCorrecta = $validator->validar($ejercicio->tipo, $respuesta, $ejercicio->respuesta_correcta);

        $res = new RespuestasUsuario();
        $res->usuario_id = auth()->id();
        $res->ejercicio_id = $ejercicioId;
        $res->sesion_id = $sesionId;
        $res->respuesta = $respuesta;
        $res->es_correcta = $esCorrecta;
        $res->tiempo_respuesta = $tiempoSegundos;
        $res->save();

        return [
            'success' => true,
            'es_correcta' => $esCorrecta,
            'solucion' => $ejercicio->solucion,
            'explicacion' => $ejercicio->explicacion,
            'respuesta_correcta' => $ejercicio->respuesta_correcta
        ];
    }

    /**
     * Devuelve el consolidado de puntuación para mostrar en la interfaz de resumen final
     */
    public function obtenerResumenSesion(int $sesionId, string $modo = 'libre'): array
    {
        $respuestas = RespuestasUsuario::where('sesion_id', $sesionId)->get();
        
        $totalOriginal = session("{$modo}_ejercicios") ? count(session("{$modo}_ejercicios")) : $respuestas->count();
        $respondidos = $respuestas->count();
        $correctas = $respuestas->where('es_correcta', true)->count();
        $porcentaje = $respondidos > 0 ? round(($correctas / $respondidos) * 100) : 0;
        
        $tiempoTotal = $respuestas->sum('tiempo_respuesta');
        $minutos = floor($tiempoTotal / 60);
        $segundos = $tiempoTotal % 60;
        $tiempoStr = "{$minutos}m {$segundos}s";

        $modulosIds = Ejercicios::whereIn('id', $respuestas->pluck('ejercicio_id'))
            ->select('modulo_id')
            ->distinct()
            ->pluck('modulo_id');
            
        $modulosTrabajados = \App\Models\Modulos::whereIn('id', $modulosIds)->pluck('nombre');
        
        // Limpiar caché
        session()->forget("{$modo}_ejercicios");

        return [
            'total' => $totalOriginal,
            'respondidos' => $respondidos,
            'correctas' => $correctas,
            'porcentaje' => $porcentaje,
            'tiempoStr' => $tiempoStr,
            'modulosTrabajados' => $modulosTrabajados
        ];
    }

    /**
     * Retorna la colección de ejercicios basada en lo preseleccionado de la sesión en el cache
     */
    public function obtenerEjerciciosDeSesion(string $modo = 'libre'): Collection
    {
        $ejerciciosIds = session("{$modo}_ejercicios", []);
        
        if (empty($ejerciciosIds)) {
            return collect();
        }

        return Ejercicios::with(['modulo', 'subtema'])
            ->whereIn('id', $ejerciciosIds)
            ->get()
            // Mantener el orden del array nativo
            ->sortBy(function($model) use ($ejerciciosIds) {
                return array_search($model->getKey(), $ejerciciosIds);
            })
            ->values();
    }
}
