<?php

namespace App\Services;

use App\Models\Flashcards;
use App\Models\Modulos;
use App\Models\Recursos;
use App\Models\Subtemas;

/**
 * Reglas y consultas de flashcards para el panel del tutor.
 */
class TutorFlashcardsService
{
    private const ESTADOS_CONTENIDO = ['BORRADOR', 'PENDIENTE', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'];
    private const ESTADOS_EDITABLES = ['BORRADOR', 'PUBLICADO', 'RECHAZADO'];
    private const ESTADOS_BLOQUEADOS_TUTOR = ['PENDIENTE', 'REVISION'];

    /**
     * Lista flashcards del tutor con filtros y catálogos para la vista.
     */
    public function obtenerFlashcardsTutor(int $tutorId, array $filtros = []): array
    {
        $tutorSubtemaIds = Flashcards::query()
            ->where('creado_por', $tutorId)
            ->whereNotNull('subtema_id')
            ->distinct()
            ->pluck('subtema_id')
            ->all();

        $tutorModuloIds = Subtemas::query()
            ->whereIn('id', $tutorSubtemaIds)
            ->whereNotNull('modulo_id')
            ->distinct()
            ->pluck('modulo_id')
            ->all();

        $search = trim((string) ($filtros['q'] ?? ''));
        $estado = $filtros['estado'] ?? null;
        $moduloId = $filtros['modulo'] ?? null;
        $subtemaId = $filtros['subtema'] ?? null;
        $orden = (string) ($filtros['orden'] ?? 'recientes');
        $perPage = (int) ($filtros['per_page'] ?? 8);
        $allowedPerPage = [6, 8, 12, 16];

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 8;
        }

        $flashcardsQuery = Flashcards::query()
            ->with([
                'subtema.modulo:id,nombre',
            ])
            ->where('creado_por', $tutorId);

        if ($search !== '') {
            $flashcardsQuery->where(function ($innerQuery) use ($search): void {
                $innerQuery
                    ->where('pregunta', 'like', "%{$search}%")
                    ->orWhere('respuesta', 'like', "%{$search}%");
            });
        }

        if (! empty($estado)) {
            $flashcardsQuery->where('estado', $estado);
        }

        if (! empty($moduloId)) {
            $flashcardsQuery->whereHas('subtema', function ($query) use ($moduloId): void {
                $query->where('modulo_id', $moduloId);
            });
        }

        if (! empty($subtemaId)) {
            $flashcardsQuery->where('subtema_id', $subtemaId);
        }

        switch ($orden) {
            case 'antiguos':
                $flashcardsQuery->orderBy('id', 'asc');
                break;
            case 'pregunta_asc':
                $flashcardsQuery->orderBy('pregunta', 'asc');
                break;
            case 'pregunta_desc':
                $flashcardsQuery->orderBy('pregunta', 'desc');
                break;
            case 'estado_asc':
                $flashcardsQuery->orderBy('estado', 'asc')->orderBy('pregunta', 'asc');
                break;
            default:
                $orden = 'recientes';
                $flashcardsQuery->orderBy('id', 'desc');
                break;
        }

        $flashcards = $flashcardsQuery->paginate($perPage, ['*'], 'flashcards_page')->withQueryString();

        $modulos = Modulos::query()
            ->whereIn('id', $tutorModuloIds)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $subtemas = Subtemas::query()
            ->whereIn('id', $tutorSubtemaIds)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modulo_id']);

        return [
            'flashcardsTutor' => $flashcards,
            'flashcardsModulos' => $modulos,
            'flashcardsSubtemas' => $subtemas,
            'flashcardsEstados' => self::ESTADOS_CONTENIDO,
            'flashcardsFiltros' => [
                'q' => $search,
                'estado' => $estado,
                'modulo' => $moduloId,
                'subtema' => $subtemaId,
                'orden' => $orden,
                'per_page' => $perPage,
            ],
            'flashcardsAllowedPerPage' => $allowedPerPage,
        ];
    }

    /**
     * Obtiene una flashcard por id validando propiedad del tutor.
     */
    public function obtenerFlashcardTutorPorId(int $tutorId, int $flashcardId): ?Flashcards
    {
        return Flashcards::query()
            ->where('id', $flashcardId)
            ->where('creado_por', $tutorId)
            ->first();
    }

    /**
     * Estados válidos en el flujo de contenido.
     */
    public function obtenerEstadosContenido(): array
    {
        return self::ESTADOS_CONTENIDO;
    }

    /**
     * Indica si la flashcard puede editarse desde el panel tutor.
     */
    public function flashcardEditable(?string $estado): bool
    {
        return in_array((string) $estado, self::ESTADOS_EDITABLES, true);
    }

    /**
     * Evalúa si la transición de estado solicitada es válida para tutor.
     */
    public function tutorPuedeCambiarEstado(?string $estadoActual, ?string $estadoObjetivo): bool
    {
        $actual = strtoupper((string) $estadoActual);
        $objetivo = strtoupper((string) $estadoObjetivo);

        if ($actual === '' || $objetivo === '') {
            return false;
        }

        if (in_array($actual, self::ESTADOS_BLOQUEADOS_TUTOR, true)) {
            return false;
        }

        if ($objetivo === 'REVISION') {
            return in_array($actual, ['BORRADOR', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'], true);
        }

        return ($actual === 'PUBLICADO' && $objetivo === 'DESHABILITADO')
            || ($actual === 'DESHABILITADO' && $objetivo === 'PUBLICADO');
    }

    /**
     * Verifica que el subtema esté en el alcance de módulos del tutor.
     */
    public function subtemaPerteneceAModulosTutor(int $tutorId, int $subtemaId): bool
    {
        $tutorModuloIds = Recursos::query()
            ->where('creado_por', $tutorId)
            ->whereIn('tipo', ['PDF', 'VIDEO', 'SIMULADOR'])
            ->whereNotNull('modulo_id')
            ->distinct()
            ->pluck('modulo_id')
            ->all();

        if ($tutorModuloIds === []) {
            return false;
        }

        return Subtemas::query()
            ->where('id', $subtemaId)
            ->whereIn('modulo_id', $tutorModuloIds)
            ->exists();
    }
}
