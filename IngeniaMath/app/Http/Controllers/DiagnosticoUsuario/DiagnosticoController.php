<?php

namespace App\Http\Controllers\DiagnosticoUsuario;

use App\Http\Controllers\Controller;
use App\Models\SesionesPractica;
use App\Models\Modulos;
use App\Services\EjercicioService;
use App\Services\DiagnosticoUsuario\DiagnosticoService;
use App\Services\DiagnosticoUsuario\RutaAprendizajeService;
use App\Traits\GestionaSesionesEjercicios;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    use GestionaSesionesEjercicios;

    public function __construct(
        private DiagnosticoService $diagnosticoService,
        private RutaAprendizajeService $rutaService,
        private EjercicioService $ejercicioService
    ) {}

    /**
     * Pantalla inicial
     */
    public function index()
    {
        return view('estudiante.diagnostico.index');
    }

    /**
     * Inicia examen diagnóstico
     */
    public function start()
    {
        $preguntas = $this->ejercicioService->getParaDiagnostico();

        if ($preguntas->isEmpty()) {
            return back()->with('error', 'No hay preguntas disponibles.');
        }

        $sesion = $this->iniciarSesion(
            'diagnostico',
            $preguntas->pluck('id')->toArray()
        );

        return redirect()->route(
            'student.diagnostic.session',
            $sesion->id
        );
    }

    /**
     * Mostrar sesión
     */
    public function session($id)
    {
        $sesion = SesionesPractica::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        $ejerciciosCol = $this->obtenerEjerciciosDeSesion($sesion->modo);

        if ($ejerciciosCol->isEmpty()) {
            return redirect()->route(
                'student.diagnostic.summary',
                $sesion->id
            );
        }

        $ejercicios = $ejerciciosCol->map(function ($ej) {
            return [
                'id' => $ej->id,
                'modulo' => $ej->modulo->nombre,
                'subtema' => $ej->subtema->nombre,
                'dificultad' => $ej->dificultad,
                'tipo' => $ej->tipo,
                'enunciado' => $ej->enunciado,
                'imagen' => $ej->imagen,
            ];
        });

        return view(
            'estudiante.diagnostico.session',
            compact('sesion', 'ejercicios')
        );
    }

    /**
     * Guardar respuesta AJAX
     */
    public function answer(Request $request, $id)
    {
        $sesion = SesionesPractica::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        $payload = $this->guardarRespuesta(
            $sesion->id,
            $request->ejercicio_id,
            $request->respuesta,
            $request->tiempo ?? 0
        );

        return response()->json($payload);
    }

    /**
     * Finalizar examen
     */
    public function summary($id)
    {
        $usuarioId = auth()->id();

        $diagnostico = $this->diagnosticoService
            ->generarDiagnostico($usuarioId, $id);

        $this->rutaService
            ->generarRuta($usuarioId, $diagnostico->id);

        $resultados = $diagnostico->resultados;

        return view(
            'estudiante.diagnostico.summary',
            compact('diagnostico', 'resultados')
        );
    }
}
