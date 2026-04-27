<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Flashcards;
use App\Services\TutorFlashcardsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Flujo Tutor para flashcards: CRUD y cambios de estado dentro de su panel.
 */
class TutorFlashcardsController extends Controller
{
    public function __construct(
        private readonly TutorFlashcardsService $tutorFlashcardsService,
    ) {
    }

    /**
     * Crea una flashcard y la deja en BORRADOR o PENDIENTE.
     */
    public function store(Request $request): RedirectResponse
    {
        $tutorId = (int) auth()->id();

        $validated = $request->validate([
            'subtema_id' => 'required|integer|exists:subtemas,id',
            'pregunta' => 'required|string',
            'respuesta' => 'required|string',
            'accion_envio' => 'nullable|in:enviar,borrador',
        ]);

        if (! $this->tutorFlashcardsService->subtemaPerteneceAModulosTutor($tutorId, (int) $validated['subtema_id'])) {
            return back()->withErrors([
                'subtema_id' => 'No puedes crear flashcards en ese subtema.',
            ])->withInput();
        }

        $estado = ($validated['accion_envio'] ?? 'enviar') === 'borrador'
            ? 'BORRADOR'
            : 'PENDIENTE';

        Flashcards::query()->create([
            'subtema_id' => (int) $validated['subtema_id'],
            'pregunta' => $validated['pregunta'],
            'respuesta' => $validated['respuesta'],
            'estado' => $estado,
            'creado_por' => $tutorId,
        ]);

        return back()->with('success', $estado === 'BORRADOR'
            ? 'Flashcard guardada en borrador.'
            : 'Flashcard enviada y marcada como PENDIENTE.');
    }

    /**
     * Edita una flashcard y la reenvía a PENDIENTE.
     */
    public function update(Request $request, int $flashcardId): RedirectResponse
    {
        $tutorId = (int) auth()->id();
        $flashcard = $this->tutorFlashcardsService->obtenerFlashcardTutorPorId($tutorId, $flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        if (! $this->tutorFlashcardsService->flashcardEditable($flashcard->estado)) {
            return back()->withErrors([
                'estado' => 'Esta flashcard no se puede editar hasta que vuelva a PUBLICADO.',
            ]);
        }

        $validated = $request->validate([
            'subtema_id' => 'nullable|integer|exists:subtemas,id',
            'pregunta' => 'nullable|string',
            'respuesta' => 'nullable|string',
        ]);

        if (! empty($validated['subtema_id'])) {
            if (! $this->tutorFlashcardsService->subtemaPerteneceAModulosTutor($tutorId, (int) $validated['subtema_id'])) {
                return back()->withErrors([
                    'subtema_id' => 'No puedes asignar la flashcard a ese subtema.',
                ]);
            }
        }

        $validated['estado'] = 'PENDIENTE';

        $flashcard->fill($validated);
        $flashcard->save();

        return back()->with('success', 'Flashcard actualizada y enviada a estado PENDIENTE.');
    }

    /**
     * Elimina una flashcard del tutor autenticado.
     */
    public function destroy(int $flashcardId): RedirectResponse
    {
        $tutorId = (int) auth()->id();
        $flashcard = $this->tutorFlashcardsService->obtenerFlashcardTutorPorId($tutorId, $flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        $flashcard->delete();

        return back()->with('success', 'Flashcard eliminada correctamente.');
    }

    /**
     * Cambia estado cuando la transición está permitida para rol tutor.
     */
    public function cambiarEstado(Request $request, int $flashcardId): RedirectResponse
    {
        $tutorId = (int) auth()->id();
        $flashcard = $this->tutorFlashcardsService->obtenerFlashcardTutorPorId($tutorId, $flashcardId);

        if (! $flashcard) {
            abort(404);
        }

        $estadoPermitido = implode(',', $this->tutorFlashcardsService->obtenerEstadosContenido());

        $validated = $request->validate([
            'estado' => "required|in:{$estadoPermitido}",
        ]);

        if (! $this->tutorFlashcardsService->tutorPuedeCambiarEstado($flashcard->estado, $validated['estado'])) {
            return back()->withErrors([
                'estado' => 'No puedes cambiar este estado desde tu panel. Las flashcards en PENDIENTE o REVISION se resuelven por revisión.',
            ]);
        }

        $flashcard->estado = $validated['estado'];
        $flashcard->save();

        return back()->with('success', 'Estado de la flashcard actualizado correctamente.');
    }
}
