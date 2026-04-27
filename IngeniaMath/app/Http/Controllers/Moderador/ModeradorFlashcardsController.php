<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use App\Services\ModeradorFlashcardsService;
use Illuminate\Http\RedirectResponse;

/**
 * Flujo Moderador para flashcards: aprobar, rechazar y eliminar.
 */
class ModeradorFlashcardsController extends Controller
{
    public function __construct(
        private readonly ModeradorFlashcardsService $moderadorFlashcardsService,
    ) {
    }

    /**
     * Aprueba una flashcard en revisión y la publica.
     */
    public function approve(int $flashcardId): RedirectResponse
    {
        $flashcard = $this->moderadorFlashcardsService->obtenerFlashcardPorId($flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        if (! $this->moderadorFlashcardsService->moderadorPuedeResolverEstado($flashcard->estado)) {
            return back()->withErrors([
                'estado' => 'Solo puedes aprobar flashcards en estado PENDIENTE o REVISION.',
            ]);
        }

        $flashcard->estado = 'PUBLICADO';
        $flashcard->save();

        return back()->with('success', 'Flashcard aprobada y publicada correctamente.');
    }

    /**
     * Rechaza una flashcard en revisión.
     */
    public function reject(int $flashcardId): RedirectResponse
    {
        $flashcard = $this->moderadorFlashcardsService->obtenerFlashcardPorId($flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        if (! $this->moderadorFlashcardsService->moderadorPuedeResolverEstado($flashcard->estado)) {
            return back()->withErrors([
                'estado' => 'Solo puedes rechazar flashcards en estado PENDIENTE o REVISION.',
            ]);
        }

        $flashcard->estado = 'RECHAZADO';
        $flashcard->save();

        return back()->with('success', 'Flashcard rechazada correctamente.');
    }

    /**
     * Elimina una flashcard desde moderación.
     */
    public function destroy(int $flashcardId): RedirectResponse
    {
        $flashcard = $this->moderadorFlashcardsService->obtenerFlashcardPorId($flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        $flashcard->delete();

        return back()->with('success', 'Flashcard eliminada correctamente.');
    }
}
