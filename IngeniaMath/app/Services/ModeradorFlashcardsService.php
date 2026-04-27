<?php

namespace App\Services;

use App\Models\Flashcards;
use App\Models\Modulos;
use App\Models\Subtemas;

/**
 * Consultas y reglas de moderación para flashcards.
 */
class ModeradorFlashcardsService
{
	private const ESTADOS_CONTENIDO = ['BORRADOR', 'PENDIENTE', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'];

	/**
	 * Devuelve flashcards para revisión con filtros del moderador.
	 */
	public function obtenerFlashcardsModeracion(array $filtros = []): array
	{
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

		$query = Flashcards::query()
			->with([
				'subtema.modulo:id,nombre',
				'creador',
			]);

		if ($search !== '') {
			$query->where(function ($innerQuery) use ($search): void {
				$innerQuery
					->where('pregunta', 'like', "%{$search}%")
					->orWhere('respuesta', 'like', "%{$search}%");
			});
		}

		if (! empty($estado)) {
			$query->where('estado', $estado);
		}

		if (! empty($moduloId)) {
			$query->whereHas('subtema', function ($subtemaQuery) use ($moduloId): void {
				$subtemaQuery->where('modulo_id', $moduloId);
			});
		}

		if (! empty($subtemaId)) {
			$query->where('subtema_id', $subtemaId);
		}

		switch ($orden) {
			case 'antiguos':
				$query->orderBy('id', 'asc');
				break;
			case 'pregunta_asc':
				$query->orderBy('pregunta', 'asc');
				break;
			case 'pregunta_desc':
				$query->orderBy('pregunta', 'desc');
				break;
			case 'estado_asc':
				$query->orderBy('estado', 'asc')->orderBy('pregunta', 'asc');
				break;
			default:
				$orden = 'recientes';
				$query->orderBy('id', 'desc');
				break;
		}

		$flashcards = $query->paginate($perPage, ['*'], 'flashcards_page')->withQueryString();

		$subtemaIds = Flashcards::query()
			->whereNotNull('subtema_id')
			->distinct()
			->pluck('subtema_id')
			->all();

		$moduloIds = Subtemas::query()
			->whereIn('id', $subtemaIds)
			->whereNotNull('modulo_id')
			->distinct()
			->pluck('modulo_id')
			->all();

		$modulos = Modulos::query()
			->whereIn('id', $moduloIds)
			->orderBy('nombre')
			->get(['id', 'nombre']);

		$subtemas = Subtemas::query()
			->whereIn('id', $subtemaIds)
			->orderBy('nombre')
			->get(['id', 'nombre', 'modulo_id']);

		return [
			'moderadorFlashcards' => $flashcards,
			'moderadorFlashcardsModulos' => $modulos,
			'moderadorFlashcardsSubtemas' => $subtemas,
			'moderadorFlashcardsEstados' => self::ESTADOS_CONTENIDO,
			'moderadorFlashcardsFiltros' => [
				'q' => $search,
				'estado' => $estado,
				'modulo' => $moduloId,
				'subtema' => $subtemaId,
				'orden' => $orden,
				'per_page' => $perPage,
			],
			'moderadorFlashcardsAllowedPerPage' => $allowedPerPage,
		];
	}

	/**
	 * Busca una flashcard por id para acciones de moderación.
	 */
	public function obtenerFlashcardPorId(int $flashcardId): ?Flashcards
	{
		return Flashcards::query()
			->where('id', $flashcardId)
			->first();
	}

	/**
	 * Define si una flashcard puede resolverse en moderación.
	 */
	public function moderadorPuedeResolverEstado(?string $estadoActual): bool
	{
		$actual = strtoupper((string) $estadoActual);

		return in_array($actual, ['PENDIENTE', 'REVISION'], true);
	}
}
