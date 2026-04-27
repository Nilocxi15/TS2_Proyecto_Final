<?php

namespace App\Services;

use App\Models\Modulos;
use App\Models\Recursos;
use App\Models\Subtemas;

/**
 * Consultas y reglas de moderación para recursos educativos.
 */
class ModeradorRecursosService
{
	private const TUTOR_RESOURCE_TYPES = ['PDF', 'VIDEO', 'SIMULADOR'];
	private const ESTADOS_CONTENIDO = ['BORRADOR', 'PENDIENTE', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'];

	/**
	 * Devuelve listado paginado de recursos para revisión del moderador.
	 */
	public function obtenerRecursosModeracion(array $filtros = []): array
	{
		$search = trim((string) ($filtros['q'] ?? ''));
		$tipo = $filtros['tipo'] ?? null;
		$estado = $filtros['estado'] ?? null;
		$moduloId = $filtros['modulo'] ?? null;
		$subtemaId = $filtros['subtema'] ?? null;
		$orden = (string) ($filtros['orden'] ?? 'recientes');
		$perPage = (int) ($filtros['per_page'] ?? 8);
		$allowedPerPage = [6, 8, 12, 16];

		if (! in_array($perPage, $allowedPerPage, true)) {
			$perPage = 8;
		}

		$query = Recursos::query()
			->with([
				'modulo:id,nombre',
				'subtema:id,nombre,modulo_id',
				'creador',
			])
			->whereIn('tipo', self::TUTOR_RESOURCE_TYPES);

		if ($search !== '') {
			$query->where(function ($innerQuery) use ($search): void {
				$innerQuery
					->where('titulo', 'like', "%{$search}%")
					->orWhere('descripcion', 'like', "%{$search}%");
			});
		}

		if (! empty($tipo)) {
			$query->where('tipo', $tipo);
		}

		if (! empty($estado)) {
			$query->where('estado', $estado);
		}

		if (! empty($moduloId)) {
			$query->where('modulo_id', $moduloId);
		}

		if (! empty($subtemaId)) {
			$query->where('subtema_id', $subtemaId);
		}

		switch ($orden) {
			case 'antiguos':
				$query->orderBy('id', 'asc');
				break;
			case 'titulo_asc':
				$query->orderBy('titulo', 'asc');
				break;
			case 'titulo_desc':
				$query->orderBy('titulo', 'desc');
				break;
			case 'tipo_asc':
				$query->orderBy('tipo', 'asc')->orderBy('titulo', 'asc');
				break;
			default:
				$orden = 'recientes';
				$query->orderBy('id', 'desc');
				break;
		}

		$recursos = $query->paginate($perPage, ['*'], 'recursos_page')->withQueryString();

		$modulos = Modulos::query()
			->whereIn('id', Recursos::query()->whereIn('tipo', self::TUTOR_RESOURCE_TYPES)->distinct()->pluck('modulo_id')->filter()->all())
			->orderBy('nombre')
			->get(['id', 'nombre']);

		$subtemas = Subtemas::query()
			->whereIn('id', Recursos::query()->whereIn('tipo', self::TUTOR_RESOURCE_TYPES)->distinct()->pluck('subtema_id')->filter()->all())
			->orderBy('nombre')
			->get(['id', 'nombre', 'modulo_id']);

		return [
			'moderadorRecursos' => $recursos,
			'moderadorRecursosModulos' => $modulos,
			'moderadorRecursosSubtemas' => $subtemas,
			'moderadorRecursosTipos' => self::TUTOR_RESOURCE_TYPES,
			'moderadorRecursosEstados' => self::ESTADOS_CONTENIDO,
			'moderadorRecursosFiltros' => [
				'q' => $search,
				'tipo' => $tipo,
				'estado' => $estado,
				'modulo' => $moduloId,
				'subtema' => $subtemaId,
				'orden' => $orden,
				'per_page' => $perPage,
			],
			'moderadorRecursosAllowedPerPage' => $allowedPerPage,
		];
	}

	/**
	 * Busca un recurso moderable por id.
	 */
	public function obtenerRecursoPorId(int $recursoId): ?Recursos
	{
		return Recursos::query()
			->where('id', $recursoId)
			->whereIn('tipo', self::TUTOR_RESOURCE_TYPES)
			->first();
	}

	/**
	 * Define si el estado actual permite resolución por moderador.
	 */
	public function moderadorPuedeResolverEstado(?string $estadoActual): bool
	{
		$actual = strtoupper((string) $estadoActual);

		return in_array($actual, ['PENDIENTE', 'REVISION'], true);
	}
}
