<?php

namespace App\Http\Controllers\BancoEjercicios;

use App\Http\Controllers\Controller;
use App\Models\Ejercicios;
use App\Models\Modulos;
use App\Models\Subtemas;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\RolEnum;

class EjercicioController extends Controller
{
    // Listar ejercicios con filtros
    public function index(Request $request)
    {
        $query = Ejercicios::with(['modulo', 'subtema', 'creador']);

        // Filtros
        if ($request->filled('modulo_id')) {
            $query->where('modulo_id', $request->modulo_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('dificultad')) {
            $query->where('dificultad', $request->dificultad);
        }

        if ($request->filled('search')) {
            $query->where('enunciado', 'ILIKE', '%' . $request->search . '%');
        }

        $ejercicios = $query->orderBy('created_at', 'desc')->paginate(15);
        $modulos = Modulos::all();

        // Para los filtros en la vista
        $estados = [
            'BORRADOR' => 'Borrador',
            'REVISION' => 'En Revisión',
            'APROBADO' => 'Aprobado',
            'PUBLICADO' => 'Publicado',
            'DESHABILITADO' => 'Deshabilitado',
        ];

        $dificultades = [
            'BASICO' => 'Básico',
            'INTERMEDIO' => 'Intermedio',
            'AVANZADO' => 'Avanzado',
            'EXAMEN' => 'Nivel Examen Real',
        ];

        return view('tutor.banco_ejercicios.index', compact('ejercicios', 'modulos', 'estados', 'dificultades'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $modulos = Modulos::with('subtemas')->get();
        $dificultades = [
            'BASICO' => 'Básico',
            'INTERMEDIO' => 'Intermedio',
            'AVANZADO' => 'Avanzado',
            'EXAMEN' => 'Nivel Examen Real',
        ];
        $tipos = [
            'OPCION_MULTIPLE' => 'Opción Múltiple',
            'VF' => 'Verdadero / Falso',
            'NUMERICO' => 'Respuesta Numérica',
            'COMPLETAR' => 'Completar Espacios',
        ];

        // Temporal: obtener usuarios (después se usará auth)
        $usuarios = Usuarios::all();

        return view('tutor.banco_ejercicios.create', compact('modulos', 'dificultades', 'tipos', 'usuarios'));
    }

    // Guardar nuevo ejercicio
    public function store(Request $request)
    {
        // Verificar si viene del botón "forzar"
        $force = $request->has('force') && $request->force == 'true';

        if (!$force) {
            $validated = $this->validateEjercicio($request);

            // Detectar duplicados
            $duplicados = $this->detectarDuplicados($request->enunciado, $request->modulo_id);

            if ($duplicados->count() > 0) {
                return back()
                    ->withInput()
                    ->with('warning', 'Se detectaron ejercicios similares. ¿Estás seguro de que quieres continuar?')
                    ->with('duplicados', $duplicados)
                    ->with('show_force_button', true);
            }
        } else {
            $validated = $this->validateEjercicio($request);
        }

        DB::beginTransaction();
        try {
            $ejercicio = Ejercicios::create([
                'modulo_id' => $request->modulo_id,
                'subtema_id' => $request->subtema_id,
                'dificultad' => $request->dificultad,
                'tipo' => $request->tipo,
                'enunciado' => $request->enunciado,
                'imagen' => $request->imagen,
                'respuesta_correcta' => $request->respuesta_correcta,
                'solucion' => $request->solucion,
                'explicacion' => $request->explicacion,
                'tiempo_estimado' => $request->tiempo_estimado,
                'estado' => 'BORRADOR',
                'creado_por' => $request->creado_por ?? auth()->id() ?? 1,
                'created_at' => now(),
            ]);

            // Guardar ejercicios relacionados
            if ($request->has('relacionados') && is_array($request->relacionados)) {
                $ejercicio->relacionados()->attach($request->relacionados);
            }

            DB::commit();

            $message = $force ? 'Ejercicio creado exitosamente (forzado).' : 'Ejercicio creado exitosamente.';

            return redirect()->route('tutor.exercises.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // Mostrar un ejercicio específico
    public function show($id)
    {
        $ejercicio = Ejercicios::with(['modulo', 'subtema', 'creador', 'revisor', 'relacionados'])->findOrFail($id);

        $dificultades = [
            'BASICO' => 'Básico',
            'INTERMEDIO' => 'Intermedio',
            'AVANZADO' => 'Avanzado',
            'EXAMEN' => 'Nivel Examen Real',
        ];

        $tipos = [
            'OPCION_MULTIPLE' => 'Opción Múltiple',
            'VF' => 'Verdadero / Falso',
            'NUMERICO' => 'Respuesta Numérica',
            'COMPLETAR' => 'Completar Espacios',
        ];

        $estados = [
            'BORRADOR' => 'Borrador',
            'REVISION' => 'En Revisión',
            'APROBADO' => 'Aprobado',
            'PUBLICADO' => 'Publicado',
            'DESHABILITADO' => 'Deshabilitado',
        ];

        return view('tutor.banco_ejercicios.show', compact('ejercicio', 'dificultades', 'tipos', 'estados'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $ejercicio = Ejercicios::with('relacionados')->findOrFail($id);

        // Solo se puede editar si está en BORRADOR o DESHABILITADO
        if (!in_array($ejercicio->estado, ['BORRADOR', 'DESHABILITADO'])) {
            return redirect()->route('tutor.exercises.show', $ejercicio)
                ->with('error', 'Solo se pueden editar ejercicios en estado Borrador o Deshabilitado.');
        }

        $modulos = Modulos::with('subtemas')->get();
        $dificultades = [
            'BASICO' => 'Básico',
            'INTERMEDIO' => 'Intermedio',
            'AVANZADO' => 'Avanzado',
            'EXAMEN' => 'Nivel Examen Real',
        ];
        $tipos = [
            'OPCION_MULTIPLE' => 'Opción Múltiple',
            'VF' => 'Verdadero / Falso',
            'NUMERICO' => 'Respuesta Numérica',
            'COMPLETAR' => 'Completar Espacios',
        ];

        $usuarios = Usuarios::all();

        // Obtener ejercicios existentes para relacionados (solo publicados y diferentes al actual)
        $ejerciciosExistentes = Ejercicios::whereIn('estado', ['PUBLICADO', 'APROBADO'])
            ->where('id', '!=', $id)
            ->limit(50)
            ->get();

        return view('tutor.banco_ejercicios.edit', compact('ejercicio', 'modulos', 'dificultades', 'tipos', 'usuarios', 'ejerciciosExistentes'));
    }

    // Actualizar ejercicio
    public function update(Request $request, $id)
    {
        $ejercicio = Ejercicios::findOrFail($id);

        // Solo se puede editar si está en BORRADOR o DESHABILITADO
        if (!in_array($ejercicio->estado, ['BORRADOR', 'DESHABILITADO'])) {
            return back()->with('error', 'Solo se pueden editar ejercicios en estado Borrador o Deshabilitado.');
        }

        $validated = $this->validateEjercicio($request, $id);

        DB::beginTransaction();
        try {
            $ejercicio->update([
                'modulo_id' => $request->modulo_id,
                'subtema_id' => $request->subtema_id,
                'dificultad' => $request->dificultad,
                'tipo' => $request->tipo,
                'enunciado' => $request->enunciado,
                'imagen' => $request->imagen,
                'respuesta_correcta' => $request->respuesta_correcta,
                'solucion' => $request->solucion,
                'explicacion' => $request->explicacion,
                'tiempo_estimado' => $request->tiempo_estimado,
            ]);

            // Actualizar relacionados
            $ejercicio->relacionados()->sync($request->relacionados ?? []);

            DB::commit();

            return redirect()->route('tutor.exercises.show', $ejercicio)
                ->with('success', 'Ejercicio actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // Cambiar estado del ejercicio (para el flujo de aprobación)
    public function cambiarEstado(Request $request, $id)
    {
        $ejercicio = Ejercicios::findOrFail($id);
        $user = auth()->user();

        $request->validate([
            'estado' => 'required|in:BORRADOR,REVISION,APROBADO,PUBLICADO,DESHABILITADO',
            'revisor_id' => 'nullable|exists:usuarios,id',
        ]);

        $nuevoEstado = $request->estado;
        $estadoAnterior = $ejercicio->estado;

        // VERIFICACIÓN DE PERMISOS SEGÚN ROL
        // Tutor: solo puede hacer transiciones específicas
        if ($user->tieneRol(RolEnum::TUTOR->value)) {
            $transicionesTutor = [
                'BORRADOR' => ['REVISION', 'DESHABILITADO'],
                'APROBADO' => ['PUBLICADO'],
                'PUBLICADO' => ['DESHABILITADO'],
                'DESHABILITADO' => ['BORRADOR'],
            ];

            if (!in_array($nuevoEstado, $transicionesTutor[$estadoAnterior] ?? [])) {
                return back()->with('error', 'No tienes permiso para realizar esta acción como Tutor.');
            }
        }

        // Revisor: solo puede aprobar o rechazar ejercicios en REVISION
        if ($user->tieneRol(RolEnum::REVISOR->value)) {
            if ($estadoAnterior !== 'REVISION') {
                return back()->with('error', 'Solo puedes revisar ejercicios que están en estado "En Revisión".');
            }

            if (!in_array($nuevoEstado, ['APROBADO', 'BORRADOR'])) {
                return back()->with('error', 'Como Revisor solo puedes Aprobar o Rechazar ejercicios.');
            }
        }

        // VALIDACIÓN DE TRANSICIONES PERMITIDAS (reglas generales)

        $transicionesPermitidas = [
            'BORRADOR' => ['REVISION', 'DESHABILITADO'],
            'REVISION' => ['APROBADO', 'BORRADOR', 'DESHABILITADO'],
            'APROBADO' => ['PUBLICADO', 'DESHABILITADO'],
            'PUBLICADO' => ['DESHABILITADO'],
            'DESHABILITADO' => ['BORRADOR'],
        ];

        if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoAnterior] ?? [])) {
            return back()->with('error', "No se puede cambiar de {$estadoAnterior} a {$nuevoEstado}");
        }

        // EJECUTAR EL CAMBIO DE ESTADO

        DB::beginTransaction();
        try {
            $updateData = ['estado' => $nuevoEstado];

            // Si se aprueba o rechaza, registrar quién lo revisó
            if (in_array($nuevoEstado, ['APROBADO', 'PUBLICADO', 'BORRADOR']) && $estadoAnterior === 'REVISION') {
                $updateData['revisado_por'] = $request->revisor_id ?? auth()->id();
            }

            $ejercicio->update($updateData);

            DB::commit();

            $mensajes = [
                'REVISION' => 'Ejercicio enviado a revisión',
                'APROBADO' => 'Ejercicio aprobado',
                'PUBLICADO' => 'Ejercicio publicado',
                'BORRADOR' => 'Ejercicio devuelto a borrador',
                'DESHABILITADO' => 'Ejercicio deshabilitado',
            ];

            // Redirigir según el rol del usuario
            if ($user->tieneRol(RolEnum::TUTOR->value)) {
                return redirect()->route('tutor.exercises.show', $ejercicio)
                    ->with('success', $mensajes[$nuevoEstado] ?? "Estado cambiado a {$nuevoEstado}");
            } elseif ($user->tieneRol(RolEnum::REVISOR->value)) {
                return redirect()->route('moderador.exercises.revisions')
                    ->with('success', $mensajes[$nuevoEstado] ?? "Estado cambiado a {$nuevoEstado}");
            } else {
                return redirect()->route('tutor.exercises.show', $ejercicio)
                    ->with('success', $mensajes[$nuevoEstado] ?? "Estado cambiado a {$nuevoEstado}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    // Eliminar (deshabilitar :3)
    public function destroy($id)
    {
        $ejercicio = Ejercicios::findOrFail($id);

        // Cambiar a DESHABILITADO en lugar de eliminar :3
        $ejercicio->update(['estado' => 'DESHABILITADO']);

        return redirect()->route('tutor.exercises.index')
            ->with('success', 'Ejercicio deshabilitado exitosamente.');
    }

    // Validación
    private function validateEjercicio(Request $request, $id = null)
    {
        return $request->validate([
            'modulo_id' => 'required|exists:modulos,id',
            'subtema_id' => 'required|exists:subtemas,id',
            'dificultad' => 'required|in:BASICO,INTERMEDIO,AVANZADO,EXAMEN',
            'tipo' => 'required|in:OPCION_MULTIPLE,VF,NUMERICO,COMPLETAR',
            'enunciado' => 'required|string|min:10',
            'imagen' => 'nullable|url',
            'respuesta_correcta' => 'required|string',
            'solucion' => 'required|string|min:20',
            'explicacion' => 'required|string|min:20',
            'tiempo_estimado' => 'required|integer|min:1|max:30',
            'relacionados' => 'nullable|array',
            'relacionados.*' => 'exists:ejercicios,id',
        ]);
    }






    // Detección de duplicados aplicado  sobre el enunciado
    private function detectarDuplicados($enunciado, $moduloId, $excluirId = null)
    {
        \Log::info('=== detectarDuplicados START ===');
        \Log::info('Parámetros:', ['enunciado' => $enunciado, 'moduloId' => $moduloId]);

        // Primero, verifica cuántos ejercicios hay en total
        $total = Ejercicios::count();
        \Log::info('Total ejercicios en BD:', ['total' => $total]);

        // Busca por ID del módulo
        $query = Ejercicios::where('modulo_id', $moduloId);
        \Log::info('Ejercicios en mismo módulo:', ['count' => $query->count()]);

        // Luego por enunciado EXACTO
        $query->where('enunciado', $enunciado);

        $resultados = $query->get();
        \Log::info('Coincidencias exactas encontradas:', ['count' => $resultados->count()]);

        foreach ($resultados as $r) {
            \Log::info('Coincidencia:', ['id' => $r->id, 'estado' => $r->estado, 'enunciado' => $r->enunciado]);
        }

        return $resultados;
    }







    public function getSubtemas($moduloId)
    {
        $subtemas = Subtemas::where('modulo_id', $moduloId)
            ->orderBy('nivel_complejidad')
            ->get(['id', 'nombre']);

        return response()->json($subtemas);
    }

    public function getEjerciciosPublicados()
    {
        $ejercicios = Ejercicios::with('modulo')
            ->where('estado', 'PUBLICADO')
            ->limit(50)
            ->get(['id', 'enunciado', 'modulo_id']);

        return response()->json($ejercicios);
    }


    // Para el Revisor: lista de ejercicios en REVISION
    public function revisionsIndex(Request $request)
    {
        $ejercicios = Ejercicios::with(['modulo', 'subtema', 'creador'])
            ->where('estado', 'REVISION')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $modulos = Modulos::all();
        $estados = [
            'BORRADOR' => 'Borrador',
            'REVISION' => 'En Revisión',
            'APROBADO' => 'Aprobado',
            'PUBLICADO' => 'Publicado',
            'DESHABILITADO' => 'Deshabilitado',
        ];
        $dificultades = [
            'BASICO' => 'Básico',
            'INTERMEDIO' => 'Intermedio',
            'AVANZADO' => 'Avanzado',
            'EXAMEN' => 'Nivel Examen Real',
        ];

        return view('moderador.exercises.revisions', compact('ejercicios', 'modulos', 'estados', 'dificultades'));
    }






    public function approve($id)
    {
        $ejercicio = Ejercicios::findOrFail($id);

        // Solo puede aprobar si está en REVISION
        if ($ejercicio->estado !== 'REVISION') {
            return back()->with('error', 'Este ejercicio no está pendiente de revisión.');
        }

        $request = new \Illuminate\Http\Request([
            'estado' => 'APROBADO',
            'revisor_id' => auth()->id()
        ]);

        return $this->cambiarEstado($request, $id);
    }


    // Ver detalle para revisar (similar a show pero con botones :D)
    public function review($id)
    {
        $ejercicio = Ejercicios::with(['modulo', 'subtema', 'creador', 'relacionados'])->findOrFail($id);

        return view('moderador.exercises.review', compact('ejercicio'));
    }


    // Rechazar ejercicio (REVISION - BORRADOR)
    public function reject(Request $request, $id)
    {
        $request->merge(['estado' => 'BORRADOR', 'revisor_id' => auth()->id()]);
        return $this->cambiarEstado($request, $id);
    }

    public function findDuplicates($id)
    {
        $ejercicio = Ejercicios::findOrFail($id);
        $duplicados = $this->detectarDuplicados($ejercicio->enunciado, $ejercicio->modulo_id, $id);

        return response()->json($duplicados);
    }
}