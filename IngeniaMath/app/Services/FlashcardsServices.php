<?php

namespace App\Services;

use App\Models\Flashcards;
use App\Models\Modulos;
use App\Models\Subtemas;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio de lectura para flashcards visibles al estudiante.
 */
class FlashcardsServices
{
	/**
	 * Retorna módulos con subtemas que tengan flashcards PUBLICADAS.
	 */
	public function obtenerGaleria(?int $moduloId = null): Collection
	{
		return Modulos::query()
			->when($moduloId, function ($query, int $moduloId): void {
				$query->whereKey($moduloId);
			})
			->with([
				'subtemas' => function ($query): void {
					$query->whereHas('flashcards', function ($flashcardsQuery): void {
						$flashcardsQuery->where('estado', 'PUBLICADO');
					})
						->orderBy('nombre')
						->with([
							'flashcards' => function ($flashcardsQuery): void {
								$flashcardsQuery->where('estado', 'PUBLICADO')
									->orderBy('id');
							},
						]);
				},
			])
			->whereHas('subtemas.flashcards', function ($flashcardsQuery): void {
				$flashcardsQuery->where('estado', 'PUBLICADO');
			})
			->orderBy('nombre')
			->get();
	}

	/**
	 * Construye el detalle de un subtema con paginación para la vista estudiante.
	 */
	public function obtenerSubtemaDetalle(Subtemas $subtema, int $perPage = 8): array
	{
		$subtema->loadMissing('modulo');

		$flashcards = Flashcards::query()
			->where('subtema_id', $subtema->id)
			->where('estado', 'PUBLICADO')
			->orderBy('id')
			->paginate($perPage)
			->withQueryString();

		$subtemasDelModulo = Subtemas::query()
			->where('modulo_id', $subtema->modulo_id)
			->whereHas('flashcards', function ($flashcardsQuery): void {
				$flashcardsQuery->where('estado', 'PUBLICADO');
			})
			->orderBy('nombre')
			->get(['id', 'nombre', 'modulo_id']);

		return [
			'subtema' => $subtema,
			'modulo' => $subtema->modulo,
			'flashcards' => $flashcards,
			'subtemasDelModulo' => $subtemasDelModulo,
			'perPage' => $perPage,
		];
	}
}
