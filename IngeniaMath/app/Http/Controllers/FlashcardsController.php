<?php

namespace App\Http\Controllers;

use App\Models\Subtemas;
use App\Services\FlashcardsServices;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Flujo Estudiante: navegación de flashcards publicadas por módulo y subtema.
 */
class FlashcardsController extends Controller
{
    public function __construct(
        private readonly FlashcardsServices $flashcardsServices,
    ) {
    }

    /**
     * Galería de módulos con subtemas y flashcards en estado PUBLICADO.
     */
    public function index(Request $request): View
    {
        return view('estudiante.flashcards-galery', [
            'modulos' => $this->flashcardsServices->obtenerGaleria(
                $request->filled('modulo') ? (int) $request->input('modulo') : null
            ),
            'moduloSeleccionado' => $request->input('modulo'),
        ]);
    }

    /**
     * Vista de detalle de un subtema con paginación de flashcards publicadas.
     */
    public function subtema(Subtemas $subtema, Request $request): View
    {
        $perPage = (int) $request->input('per_page', 6);
        $allowedPerPage = [4, 6, 8, 12];

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 6;
        }

        return view('estudiante.flashcards-subtema', array_merge(
            $this->flashcardsServices->obtenerSubtemaDetalle($subtema, $perPage),
            [
                'allowedPerPage' => $allowedPerPage,
            ]
        ));
    }
}