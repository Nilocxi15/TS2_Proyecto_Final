<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use App\Services\ModeradorFlashcardsService;
use App\Services\ModeradorRecursosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Flujo Moderador para recursos: revisión, aprobación/rechazo y eliminación.
 */
class ModeradorRecursosController extends Controller
{
    public function __construct(
        private readonly ModeradorRecursosService $moderadorRecursosService,
        private readonly ModeradorFlashcardsService $moderadorFlashcardsService,
    ) {
    }

    /**
     * Muestra el panel de revisión (recursos + flashcards) con filtros.
     */
    public function show(Request $request): View
    {
        return view('moderador.recursos_educativos.show', array_merge(
            $this->moderadorRecursosService->obtenerRecursosModeracion([
                'q' => $request->input('rec_q', ''),
                'tipo' => $request->input('rec_tipo'),
                'estado' => $request->input('rec_estado'),
                'modulo' => $request->input('rec_modulo'),
                'subtema' => $request->input('rec_subtema'),
                'orden' => $request->input('rec_orden', 'recientes'),
                'per_page' => $request->input('rec_per_page', 8),
            ]),
            $this->moderadorFlashcardsService->obtenerFlashcardsModeracion([
                'q' => $request->input('flash_q', ''),
                'estado' => $request->input('flash_estado'),
                'modulo' => $request->input('flash_modulo'),
                'subtema' => $request->input('flash_subtema'),
                'orden' => $request->input('flash_orden', 'recientes'),
                'per_page' => $request->input('flash_per_page', 8),
            ])
        ));
    }

    /**
     * Aprueba un recurso pendiente/revisión y lo publica.
     */
    public function approve(int $recursoId): RedirectResponse
    {
        $recurso = $this->moderadorRecursosService->obtenerRecursoPorId($recursoId);

        if (! $recurso) {
            abort(404);
        }

        if (! $this->moderadorRecursosService->moderadorPuedeResolverEstado($recurso->estado)) {
            return back()->withErrors([
                'estado' => 'Solo puedes aprobar recursos en estado PENDIENTE o REVISION.',
            ]);
        }

        $recurso->estado = 'PUBLICADO';
        $recurso->save();

        return back()->with('success', 'Recurso aprobado y publicado correctamente.');
    }

    /**
     * Rechaza un recurso pendiente/revisión.
     */
    public function reject(int $recursoId): RedirectResponse
    {
        $recurso = $this->moderadorRecursosService->obtenerRecursoPorId($recursoId);

        if (! $recurso) {
            abort(404);
        }

        if (! $this->moderadorRecursosService->moderadorPuedeResolverEstado($recurso->estado)) {
            return back()->withErrors([
                'estado' => 'Solo puedes rechazar recursos en estado PENDIENTE o REVISION.',
            ]);
        }

        $recurso->estado = 'RECHAZADO';
        $recurso->save();

        return back()->with('success', 'Recurso rechazado correctamente.');
    }

    /**
     * Elimina recurso desde el panel de moderación.
     */
    public function destroy(int $recursoId): RedirectResponse
    {
        $recurso = $this->moderadorRecursosService->obtenerRecursoPorId($recursoId);

        if (! $recurso) {
            abort(404);
        }

        $recurso->delete();

        return back()->with('success', 'Recurso eliminado correctamente.');
    }
}
