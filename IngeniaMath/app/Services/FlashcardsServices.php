<?php

namespace App\Services;

use App\Models\Flashcards;
use App\Models\Modulos;
use App\Models\Subtemas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FlashcardsServices
{
	public function obtenerGaleria(?int $moduloId = null): Collection
	{
		return Modulos::query()
			->when($moduloId, function ($query, int $moduloId): void {
				$query->whereKey($moduloId);
			})
			->with([
				'subtemas' => function ($query): void {
					$query->whereHas('flashcards')
						->orderBy('nombre')
						->with([
							'flashcards' => function ($flashcardsQuery): void {
								$flashcardsQuery->orderBy('id');
							},
						]);
				},
			])
			->whereHas('subtemas.flashcards')
			->orderBy('nombre')
			->get();
	}

	public function obtenerSubtemaDetalle(Subtemas $subtema, int $perPage = 8): array
	{
		$subtema->loadMissing('modulo');

		$flashcards = Flashcards::query()
			->where('subtema_id', $subtema->id)
			->orderBy('id')
			->paginate($perPage)
			->withQueryString();

		$subtemasDelModulo = Subtemas::query()
			->where('modulo_id', $subtema->modulo_id)
			->whereHas('flashcards')
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
