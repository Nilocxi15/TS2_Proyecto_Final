<?php

namespace App\Http\Controllers\DiagnosticoUsuario;

use App\Http\Controllers\Controller;
use App\Models\RutasAprendizaje;

class RutaController extends Controller
{
    public function index()
    {
        $ruta = RutasAprendizaje::where('usuario_id', auth()->id())
            ->where('activa', true)
            ->latest()
            ->first();

        if (!$ruta) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'No tienes ruta generada.');
        }

        $detalles = \DB::table('ruta_detalle as rd')
            ->join('subtemas as s', 'rd.subtema_id', '=', 's.id')
            ->join('modulos as m', 's.modulo_id', '=', 'm.id')
            ->select(
                'rd.*',
                's.nombre as subtema',
                's.nivel_complejidad',
                'm.nombre as modulo'
            )
            ->where('rd.ruta_id', $ruta->id)
            ->orderBy('rd.prioridad')
            ->get();

        return view(
            'estudiante.ruta-aprendizaje',
            compact('ruta', 'detalles')
        );
    }
}
