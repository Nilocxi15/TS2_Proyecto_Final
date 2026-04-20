<?php

namespace App\Http\Controllers\BancoEjercicios;

use App\Http\Controllers\Controller;
use App\Models\Ejercicios;
use App\Models\Modulos;
use App\Models\Subtemas;
use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        
        return view('BancoEjercicios.Tutor.index', compact('ejercicios', 'modulos', 'estados', 'dificultades'));
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
        
        return view('BancoEjercicios.Tutor.create', compact('modulos', 'dificultades', 'tipos', 'usuarios'));
    }
    
    // Guardar nuevo ejercicio
    public function store(Request $request)
    {
        $validated = $this->validateEjercicio($request);
        
        // Detectar duplicados antes de guardar
        $duplicados = $this->detectarDuplicados($request->enunciado, $request->modulo_id);
        
        if ($duplicados->count() > 0) {
            return back()
                ->withInput()
                ->with('warning', 'Se detectaron ejercicios similares. ¿Estás seguro de que no es un duplicado?')
                ->with('duplicados', $duplicados);
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
                'creado_por' => $request->creado_por ?? 1, // Temporal: primer usuario por defecto
                'created_at' => now(),
            ]);
            
            // Guardar ejercicios relacionados
            if ($request->has('relacionados') && is_array($request->relacionados)) {
                $ejercicio->relacionados()->attach($request->relacionados);
            }
            
            DB::commit();
            
            return redirect()->route('ejercicios.index')
                ->with('success', 'Ejercicio creado exitosamente.');
                
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
        
        return view('BancoEjercicios.Tutor.show', compact('ejercicio', 'dificultades', 'tipos', 'estados'));
    }
    
    // Mostrar formulario de edición
    public function edit($id)
    {
        $ejercicio = Ejercicios::with('relacionados')->findOrFail($id);
        
        // Solo se puede editar si está en BORRADOR o DESHABILITADO
        if (!in_array($ejercicio->estado, ['BORRADOR', 'DESHABILITADO'])) {
            return redirect()->route('ejercicios.show', $ejercicio)
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
        
        return view('BancoEjercicios.Tutor.edit', compact('ejercicio', 'modulos', 'dificultades', 'tipos', 'usuarios', 'ejerciciosExistentes'));
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
            
            return redirect()->route('ejercicios.show', $ejercicio)
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
        
        $request->validate([
            'estado' => 'required|in:BORRADOR,REVISION,APROBADO,PUBLICADO,DESHABILITADO',
            'revisor_id' => 'nullable|exists:usuarios,id',
        ]);
        
        $nuevoEstado = $request->estado;
        $estadoAnterior = $ejercicio->estado;
        
        // Validar transiciones permitidas
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
        
        DB::beginTransaction();
        try {
            $updateData = ['estado' => $nuevoEstado];
            
            // Si se aprueba o rechaza, registrar quién lo revisó
            if (in_array($nuevoEstado, ['APROBADO', 'PUBLICADO', 'BORRADOR']) && $estadoAnterior === 'REVISION') {
                $updateData['revisado_por'] = $request->revisor_id ?? 1; // Temporal
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
            
            return redirect()->route('ejercicios.show', $ejercicio)
                ->with('success', $mensajes[$nuevoEstado] ?? "Estado cambiado a {$nuevoEstado}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }
    
    // Eliminar (deshabilitar)
    public function destroy($id)
    {
        $ejercicio = Ejercicios::findOrFail($id);
        
        // Cambiar a DESHABILITADO en lugar de eliminar
        $ejercicio->update(['estado' => 'DESHABILITADO']);
        
        return redirect()->route('ejercicios.index')
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
    
    // Detección de duplicados (servicio simple)
    private function detectarDuplicados($enunciado, $moduloId, $excluirId = null)
    {
        $query = Ejercicios::where('modulo_id', $moduloId)
            ->whereIn('estado', ['PUBLICADO', 'APROBADO', 'REVISION'])
            ->whereRaw('LOWER(enunciado) LIKE LOWER(?)', ['%' . substr($enunciado, 0, 50) . '%']);
        
        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }
        
        return $query->limit(5)->get();
    }


    // Agrega este método al final del controlador
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
}