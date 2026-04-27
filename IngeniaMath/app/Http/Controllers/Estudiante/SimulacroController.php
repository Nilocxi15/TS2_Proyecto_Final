<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Configuraciones;
use App\Models\Ejercicios;
use App\Models\Modulos;
use App\Models\SimulacroPreguntas;
use App\Models\Simulacros;
use App\Services\RespuestaValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulacroController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $simulacros = Simulacros::where('usuario_id', $userId)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('estudiante.simulacro.index', compact('simulacros'));
    }

    public function start()
    {
        $userId = auth()->id();
        $totalPreguntas = (int)Configuraciones::getValor('simulacro_total_preguntas', 35);
        $duracion = (int)Configuraciones::getValor('simulacro_duracion_minutos', 90);

        // Obtener el número de módulos para distribuir proporcionalmente
        $modulos = Modulos::all();
        $numModulos = $modulos->count();
        $preguntasPorModulo = $numModulos > 0 ? max(1, floor($totalPreguntas / $numModulos)) : 0;
        $residuo = $numModulos > 0 ? $totalPreguntas % $numModulos : 0;

        $ejerciciosSeleccionados = collect();

        foreach ($modulos as $index => $modulo) {
            $limite = $preguntasPorModulo + ($index < $residuo ? 1 : 0);

            // Obtener ejercicios aleatorios de este módulo
            $ejercicios = Ejercicios::where('modulo_id', $modulo->id)
                ->where('estado', 'PUBLICADO')
                ->inRandomOrder()
                ->limit($limite)
                ->get();

            $ejerciciosSeleccionados = $ejerciciosSeleccionados->concat($ejercicios);
        }

        // Si faltan ejercicios por alguna razón (e.g. un módulo no tiene suficientes)
        if ($ejerciciosSeleccionados->count() < $totalPreguntas) {
            $faltantes = $totalPreguntas - $ejerciciosSeleccionados->count();
            $extras = Ejercicios::where('estado', 'PUBLICADO')
                ->whereNotIn('id', $ejerciciosSeleccionados->pluck('id'))
                ->inRandomOrder()
                ->limit($faltantes)
                ->get();
            $ejerciciosSeleccionados = $ejerciciosSeleccionados->concat($extras);
        }

        // Mezclar las preguntas seleccionadas para que no estén agrupadas por módulo
        $ejerciciosSeleccionados = $ejerciciosSeleccionados->shuffle();

        DB::beginTransaction();
        try {
            $simulacro = new Simulacros();
            $simulacro->usuario_id = $userId;
            $simulacro->duracion = $duracion;
            $simulacro->save();

            foreach ($ejerciciosSeleccionados as $ej) {
                SimulacroPreguntas::create([
                    'simulacro_id' => $simulacro->id,
                    'ejercicio_id' => $ej->id,
                    'es_correcta' => null,
                    'respuesta' => null,
                ]);
            }

            DB::commit();

            // Guardar IDs en la sesión para el frontend (opcional)
            session(['simulacro_ejercicios_' . $simulacro->id => $ejerciciosSeleccionados->pluck('id')->toArray()]);

            return redirect()->route('student.mock-exams.session', ['id' => $simulacro->id]);

        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No se pudo generar el simulacro: ' . $e->getMessage());
        }
    }

    public function session($id)
    {
        $simulacro = Simulacros::where('usuario_id', auth()->id())->findOrFail($id);

        if ($simulacro->puntaje !== null) {
            return redirect()->route('student.mock-exams.results', ['id' => $simulacro->id]);
        }

        // Obtener IDs de ejercicios desde simulacro_preguntas
        $preguntas = SimulacroPreguntas::where('simulacro_id', $simulacro->id)->get();

        if ($preguntas->isEmpty()) {
            return redirect()->route('student.mock-exams')->with('error', 'El simulacro no tiene preguntas asignadas.');
        }

        $ejerciciosIds = $preguntas->pluck('ejercicio_id')->toArray();

        $ejercicios = Ejercicios::whereIn('id', $ejerciciosIds)
            ->where('estado', 'PUBLICADO')
            ->with(['modulo', 'subtema'])
            ->get();

        $ejerciciosData = $ejercicios->map(function ($ej) {
            return [
            'id' => $ej->id,
            'modulo' => $ej->modulo ? $ej->modulo->nombre : 'General',
            'subtema' => $ej->subtema ? $ej->subtema->nombre : 'General',
            'dificultad' => $ej->dificultad,
            'tipo' => $ej->tipo,
            'enunciado' => $ej->enunciado,
            'imagen' => $ej->imagen,
            ];
        });

        $segundosTranscurridos = now()->diffInSeconds($simulacro->fecha);
        $segundosRestantes = max(0, ($simulacro->duracion * 60) - $segundosTranscurridos);

        return view('estudiante.simulacro.session', compact('simulacro', 'ejerciciosData', 'segundosRestantes'));
    }

    public function saveAnswer(Request $request, $id)
    {
        $simulacro = Simulacros::where('usuario_id', auth()->id())->findOrFail($id);

        if ($simulacro->puntaje !== null) {
            return response()->json(['error' => 'Simulacro ya finalizado'], 400);
        }

        $ejercicioId = $request->input('ejercicio_id');
        $respuestaUsuario = $request->input('respuesta');

        $ejercicio = Ejercicios::findOrFail($ejercicioId);
        $validator = app(RespuestaValidator::class);
        $esCorrecta = $validator->validar($ejercicio->tipo, $respuestaUsuario, $ejercicio->respuesta_correcta);

        // Actualizar el registro en simulacro_preguntas
        SimulacroPreguntas::where('simulacro_id', $simulacro->id)
            ->where('ejercicio_id', $ejercicioId)
            ->update([
            'es_correcta' => $esCorrecta,
            'respuesta' => $respuestaUsuario
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Respuesta guardada'
        ]);
    }

    public function finish($id)
    {
        $simulacro = Simulacros::where('usuario_id', auth()->id())->findOrFail($id);
        return $this->forceFinish($simulacro);
    }

    private function forceFinish(Simulacros $simulacro)
    {
        if ($simulacro->puntaje !== null) {
            return redirect()->route('student.mock-exams.results', ['id' => $simulacro->id]);
        }

        $totalPreguntas = SimulacroPreguntas::where('simulacro_id', $simulacro->id)->count();
        $correctas = SimulacroPreguntas::where('simulacro_id', $simulacro->id)
            ->where('es_correcta', true)
            ->count();

        $puntaje = $totalPreguntas > 0 ? round(($correctas / $totalPreguntas) * 100, 2) : 0;

        $simulacro->puntaje = $puntaje;
        $simulacro->save();

        session()->forget('simulacro_ejercicios_' . $simulacro->id);

        return redirect()->route('student.mock-exams.results', ['id' => $simulacro->id]);
    }

    public function results($id)
    {
        $simulacro = Simulacros::where('usuario_id', auth()->id())->findOrFail($id);

        if ($simulacro->puntaje === null) {
            // Si el usuario intentó ir a resultados sin finalizar
            return redirect()->route('student.mock-exams.session', ['id' => $simulacro->id]);
        }

        $preguntas = SimulacroPreguntas::with('ejercicio.modulo')->where('simulacro_id', $simulacro->id)->get();
        $total = $preguntas->count();
        $correctas = $preguntas->where('es_correcta', true)->count();
        $incorrectas = $preguntas->where('es_correcta', false)->values();
        // las nulas contaran como incorrectas (no respondidas)
        $noRespondidas = $preguntas->whereNull('es_correcta')->values();

        $incorrectas = $incorrectas->concat($noRespondidas);

        $puntajeAprobacion = 61; // Puntaje de referencia USAC

        // Desglose por modulo
        $desgloseModulos = [];
        $modulos = Modulos::all();
        foreach ($modulos as $mod) {
            $pregMod = $preguntas->filter(fn($p) => $p->ejercicio->modulo_id === $mod->id);
            if ($pregMod->count() > 0) {
                $cMod = $pregMod->where('es_correcta', true)->count();
                $desgloseModulos[] = [
                    'nombre' => $mod->nombre,
                    'total' => $pregMod->count(),
                    'correctas' => $cMod,
                    'porcentaje' => round(($cMod / $pregMod->count()) * 100, 1)
                ];
            }
        }

        return view('estudiante.simulacro.results', compact('simulacro', 'total', 'correctas', 'incorrectas', 'desgloseModulos', 'puntajeAprobacion'));
    }
}