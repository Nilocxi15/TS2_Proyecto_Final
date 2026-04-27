<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Services\DiagnosticoUsuario\RutaAprendizajeService;
use Illuminate\Http\Request;
use App\Models\Modulos;
use App\Models\SesionesPractica;
use App\Services\EjercicioService;
use App\Traits\GestionaSesionesEjercicios;

class PracticeController extends Controller
{
    use GestionaSesionesEjercicios;

    protected $ejercicioService;
    protected $rutaAprendizajeService;

    /**
     * Constructor del controlador
     * Inyecta el servicio de ejercicios para manejar la lógica de selección
     */
    public function __construct(EjercicioService $ejercicioService, RutaAprendizajeService $rutaAprendizajeService)
    {
        $this->ejercicioService = $ejercicioService;
        $this->rutaAprendizajeService = $rutaAprendizajeService;
    }

    /**
     * Muestra la página principal de selección de práctica
     * Carga todos los módulos y las dificultades disponibles para los filtros
     */
    public function index(Request $request)
    {
        if ($request->filled('subtema_id')) return $this->startFromRoute($request->subtema_id);


        $modulos = Modulos::all();
        $dificultades = ['BASICO', 'INTERMEDIO', 'AVANZADO', 'EXAMEN'];
        return view('estudiante.practica.index', compact('modulos', 'dificultades'));
    }

    /**
     * Inicia una sesión de práctica libre
     * El estudiante elige manualmente el módulo y la dificultad
     */
    public function startFree(Request $request)
    {
        // Validar que los campos sean correctos
        $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'dificultad' => 'required|in:BASICO,INTERMEDIO,AVANZADO,EXAMEN',
        ]);

        // Obtener ejercicios según módulo y dificultad seleccionados
        $ejercicios = $this->ejercicioService->getByModuloYDificultad($request->modulo_id, $request->dificultad);

        // Verificar que existan ejercicios disponibles
        if ($ejercicios->isEmpty()) {
            return back()->with('error', 'No hay ejercicios disponibles para esa configuración.');
        }

        // Crear una nueva sesión de práctica
        $sesion = $this->iniciarSesion('libre', $ejercicios->pluck('id')->toArray());

        // Redirigir a la página de la sesión
        return redirect()->route('student.practice.session', ['id' => $sesion->id]);
    }

    /**
     * Inicia una sesión de práctica guiada
     * El sistema selecciona automáticamente los ejercicios basados en el rendimiento del estudiante
     * Usa el motor de recomendación para identificar módulos débiles y ajustar dificultad
     */
    public function startGuided(Request $request)
    {
        $userId = auth()->id();

        // Obtener ejercicios recomendados según el historial del estudiante
        $ejercicios = $this->ejercicioService->getRecomendados($userId);

        // Verificar que existan ejercicios disponibles
        if ($ejercicios->isEmpty()) {
            return back()->with('error', 'No hay ejercicios disponibles para la práctica guiada actualmente.');
        }

        // Crear una nueva sesión de práctica guiada
        $sesion = $this->iniciarSesion('guiada', $ejercicios->pluck('id')->toArray());

        // Redirigir a la página de la sesión
        return redirect()->route('student.practice.session', ['id' => $sesion->id]);
    }

    /**
     * Muestra la página interactiva de una sesión de práctica
     * Carga los ejercicios de la sesión y los pasa a la vista para que JavaScript los maneje
     */
    public function session($id)
    {
        // Buscar la sesión y verificar que pertenezca al usuario autenticado
        $sesion = SesionesPractica::where('id', $id)->where('usuario_id', auth()->id())->firstOrFail();

        // Obtener los ejercicios asociados a esta sesión
        $ejerciciosCol = $this->obtenerEjerciciosDeSesion($sesion->modo);

        // Si no hay ejercicios, redirigir directamente al resumen
        if ($ejerciciosCol->isEmpty()) {
            return redirect()->route('student.practice.summary', ['id' => $id]);
        }

        // Transformar la colección de ejercicios a un formato seguro para el frontend
        // Se excluyen campos sensibles como la respuesta correcta, solución y explicación
        // Estos se enviarán solo cuando el estudiante compruebe su respuesta
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

        return view('estudiante.practica.session', compact('sesion', 'ejercicios'));
    }

    /**
     * Guarda la respuesta de un ejercicio en la base de datos
     * Este método es llamado vía AJAX desde el frontend
     */
    public function saveAnswer(Request $request, $id)
    {
        // Verificar que la sesión exista y pertenezca al usuario
        $sesion = SesionesPractica::where('id', $id)->where('usuario_id', auth()->id())->firstOrFail();

        // Guardar la respuesta usando el método del trait
        $payload = $this->guardarRespuesta(
            $sesion->id,
            $request->ejercicio_id,
            $request->respuesta,
            $request->tiempo ?? 0
        );

        // Devolver la respuesta en formato JSON para que JavaScript la procese
        return response()->json($payload);
    }

    /**
     * Muestra el resumen final de una sesión de práctica
     * Calcula estadísticas como total de aciertos, porcentaje y tiempo empleado
     */
    public function summary($id)
    {
        // Verificar que la sesión exista y pertenezca al usuario
        $sesion = SesionesPractica::where('id', $id)->where('usuario_id', auth()->id())->firstOrFail();

        // Obtener el resumen de la sesión (aciertos, tiempo, módulos trabajados)
        $resumen = $this->obtenerResumenSesion($sesion->id, $sesion->modo);

        if($resumen['porcentaje'] < 50){

            $primerEjercicio = $this->obtenerEjerciciosDeSesion($sesion->modo)->first();

            if($primerEjercicio){
                $this->rutaAprendizajeService->evaluarRetrocesoPorFallo(
                    auth()->id(),
                    $primerEjercicio->subtema_id
                );

            }
        }

        if (session()->has('ruta_subtema_id')) {

            $subtemaId = session('ruta_subtema_id');

            $this->marcarRutaSiAprobo(
                auth()->id(),
                $id,
                $subtemaId
            );

            session()->forget('ruta_subtema_id');
        }

        // Combinar los datos de la sesión con el resumen y pasar a la vista
        return view('estudiante.practica.summary', array_merge(['sesion' => $sesion], $resumen));
    }

    public function startFromRoute($subtemaId)
    {
        $ejercicios = $this->ejercicioService
            ->getPorSubtema($subtemaId, 10);

        if ($ejercicios->isEmpty()) {
            return back()->with(
                'error',
                'No hay ejercicios para este tema.'
            );
        }

        $sesion = $this->iniciarSesion(
            'ruta',
            $ejercicios->pluck('id')->toArray()
        );

        session([
            'ruta_subtema_id' => $subtemaId
        ]);

        return redirect()->route(
            'student.practice.session',
            $sesion->id
        );
    }

    private function marcarRutaSiAprobo($usuarioId, $sesionId, $subtemaId)
    {
        $total = \DB::table('respuestas_usuario')
            ->where('sesion_id', $sesionId)
            ->count();

        $correctas = \DB::table('respuestas_usuario')
            ->where('sesion_id', $sesionId)
            ->where('es_correcta', true)
            ->count();

        if ($total == 0) {
            return;
        }

        $porcentaje = ($correctas / $total) * 100;

        if ($porcentaje >= 70) {

            $ruta = \DB::table('rutas_aprendizaje')
                ->where('usuario_id', $usuarioId)
                ->where('activa', true)
                ->first();

            if (!$ruta) return;

            \DB::table('ruta_detalle')
                ->where('ruta_id', $ruta->id)
                ->where('subtema_id', $subtemaId)
                ->update([
                    'completado' => true
                ]);
        }
    }
}
