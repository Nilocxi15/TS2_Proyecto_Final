<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Services\TutorFlashcardsService;
use App\Services\TutorRecursosService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Flujo Tutor: panel unificado de gestión de recursos y flashcards propias.
 */
class TutorRecursosEducativosController extends Controller
{
    public function __construct(
        private readonly TutorRecursosService $tutorRecursosService,
        private readonly TutorFlashcardsService $tutorFlashcardsService,
    ) {
    }

    /**
     * Renderiza el panel combinando listados y filtros de recursos/flashcards.
     */
    public function show(Request $request): View
    {
        $tutorId = (int) auth()->id();

        return view('tutor.recursos_educativos.show', array_merge(
            $this->tutorRecursosService->obtenerRecursosTutor($tutorId, [
                'q' => $request->input('rec_q', ''),
                'tipo' => $request->input('rec_tipo'),
                'estado' => $request->input('rec_estado'),
                'modulo' => $request->input('rec_modulo'),
                'subtema' => $request->input('rec_subtema'),
                'orden' => $request->input('rec_orden', 'recientes'),
                'per_page' => $request->input('rec_per_page', 8),
            ]),
            $this->tutorFlashcardsService->obtenerFlashcardsTutor($tutorId, [
                'q' => $request->input('flash_q', ''),
                'estado' => $request->input('flash_estado'),
                'modulo' => $request->input('flash_modulo'),
                'subtema' => $request->input('flash_subtema'),
                'orden' => $request->input('flash_orden', 'recientes'),
                'per_page' => $request->input('flash_per_page', 8),
            ])
        ));
    }
}
