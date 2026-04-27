<?php

namespace App\Services;

use App\Models\Modulos;
use App\Models\Recursos;
use App\Models\Subtemas;

/**
 * Reglas y consultas del flujo de recursos para el rol tutor.
 */
class TutorRecursosService
{
    private const TUTOR_RESOURCE_TYPES = ['PDF', 'VIDEO', 'SIMULADOR'];
    private const ESTADOS_CONTENIDO = ['BORRADOR', 'PENDIENTE', 'REVISION', 'APROBADO', 'PUBLICADO', 'DESHABILITADO', 'RECHAZADO'];
    private const ESTADOS_EDITABLES = ['BORRADOR', 'PUBLICADO', 'RECHAZADO'];
    private const ESTADOS_BLOQUEADOS_TUTOR = ['PENDIENTE', 'REVISION'];

    /**
     * Lista recursos del tutor con filtros del panel y catálogos auxiliares.
     */
    public function obtenerRecursosTutor(int $tutorId, array $filtros = []): array
    {
        $tutorModuloIds = $this->obtenerTutorModuloIdsInterno($tutorId);
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

        $recursosQuery = Recursos::query()
            ->with([
                'modulo:id,nombre',
                'subtema:id,nombre,modulo_id',
            ])
            ->where('creado_por', $tutorId)
            ->whereIn('tipo', self::TUTOR_RESOURCE_TYPES);

        if ($search !== '') {
            $recursosQuery->where(function ($innerQuery) use ($search): void {
                $innerQuery
                    ->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if (! empty($tipo)) {
            $recursosQuery->where('tipo', $tipo);
        }

        if (! empty($estado)) {
            $recursosQuery->where('estado', $estado);
        }

        if (! empty($moduloId)) {
            $recursosQuery->where('modulo_id', $moduloId);
        }

        if (! empty($subtemaId)) {
            $recursosQuery->where('subtema_id', $subtemaId);
        }

        switch ($orden) {
            case 'antiguos':
                $recursosQuery->orderBy('id', 'asc');
                break;
            case 'titulo_asc':
                $recursosQuery->orderBy('titulo', 'asc');
                break;
            case 'titulo_desc':
                $recursosQuery->orderBy('titulo', 'desc');
                break;
            case 'tipo_asc':
                $recursosQuery->orderBy('tipo', 'asc')->orderBy('titulo', 'asc');
                break;
            default:
                $orden = 'recientes';
                $recursosQuery->orderBy('id', 'desc');
                break;
        }

        $recursos = $recursosQuery->paginate($perPage, ['*'], 'recursos_page')->withQueryString();

        $modulos = Modulos::query()
            ->whereIn('id', $tutorModuloIds)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $subtemas = Subtemas::query()
            ->whereIn('modulo_id', $tutorModuloIds)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modulo_id']);

        return [
            'recursosTutor' => $recursos,
            'recursosModulos' => $modulos,
            'recursosSubtemas' => $subtemas,
            'recursosTipos' => self::TUTOR_RESOURCE_TYPES,
            'recursosEstados' => self::ESTADOS_CONTENIDO,
            'recursosFiltros' => [
                'q' => $search,
                'tipo' => $tipo,
                'estado' => $estado,
                'modulo' => $moduloId,
                'subtema' => $subtemaId,
                'orden' => $orden,
                'per_page' => $perPage,
            ],
            'recursosAllowedPerPage' => $allowedPerPage,
        ];
    }

    /**
     * Busca un recurso propiedad del tutor por id.
     */
    public function obtenerRecursoTutorPorId(int $tutorId, int $recursoId): ?Recursos
    {
        return Recursos::query()
            ->where('id', $recursoId)
            ->where('creado_por', $tutorId)
            ->whereIn('tipo', self::TUTOR_RESOURCE_TYPES)
            ->first();
    }

    /**
     * Tipos de recurso disponibles en el módulo educativo.
     */
    public function obtenerTiposRecursoTutor(): array
    {
        return self::TUTOR_RESOURCE_TYPES;
    }

    /**
     * Estados válidos del flujo de contenido.
     */
    public function obtenerEstadosContenido(): array
    {
        return self::ESTADOS_CONTENIDO;
    }

    /**
     * Indica si el recurso se puede editar en panel tutor.
     */
    public function recursoEditable(?string $estado): bool
    {
        return in_array((string) $estado, self::ESTADOS_EDITABLES, true);
    }

    /**
     * Evalúa transiciones de estado permitidas para tutor.
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
     * Devuelve módulos en los que el tutor ya tiene recursos creados.
     */
    public function obtenerTutorModuloIds(int $tutorId): array
    {
        return $this->obtenerTutorModuloIdsInterno($tutorId);
    }

    /**
     * Verifica que un subtema pertenezca al universo de módulos del tutor.
     */
    public function subtemaPerteneceAModulosTutor(int $tutorId, int $subtemaId): bool
    {
        $tutorModuloIds = $this->obtenerTutorModuloIdsInterno($tutorId);

        if ($tutorModuloIds === []) {
            return false;
        }

        return Subtemas::query()
            ->where('id', $subtemaId)
            ->whereIn('modulo_id', $tutorModuloIds)
            ->exists();
    }

    /**
     * Consulta interna para reutilizar módulo-ids del tutor.
     */
    private function obtenerTutorModuloIdsInterno(int $tutorId): array
    {
        return Recursos::query()
            ->where('creado_por', $tutorId)
            ->whereIn('tipo', self::TUTOR_RESOURCE_TYPES)
            ->whereNotNull('modulo_id')
            ->distinct()
            ->pluck('modulo_id')
            ->all();
    }
}
