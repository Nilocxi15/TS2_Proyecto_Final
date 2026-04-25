<?php

namespace App\Http\Controllers;

use App\Models\Modulos;
use App\Models\Recursos;
use App\Models\Subtemas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecursosController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $tipo = $request->input('tipo');
        $moduloId = $request->input('modulo');
        $subtemaId = $request->input('subtema');
        $orden = (string) $request->input('orden', 'recientes');

        $perPage = (int) $request->input('per_page', 9);
        $allowedPerPage = [6, 9, 12, 18];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 9;
        }

        $query = Recursos::query()
            ->with([
                'modulo:id,nombre',
                'subtema:id,nombre,modulo_id',
            ])
            ->where('estado', 'PUBLICADO');

        if ($search !== '') {
            $query->where(function ($innerQuery) use ($search): void {
                $innerQuery
                    ->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if (!empty($tipo)) {
            $query->where('tipo', $tipo);
        }

        if (!empty($moduloId)) {
            $query->where('modulo_id', $moduloId);
        }

        if (!empty($subtemaId)) {
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

        $recursos = $query->paginate($perPage)->withQueryString();

        $tipos = Recursos::query()
            ->where('estado', 'PUBLICADO')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $modulos = Modulos::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $subtemas = Subtemas::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modulo_id']);

        return view('estudiante.recursos', [
            'recursos' => $recursos,
            'tipos' => $tipos,
            'modulos' => $modulos,
            'subtemas' => $subtemas,
            'filtros' => [
                'q' => $search,
                'tipo' => $tipo,
                'modulo' => $moduloId,
                'subtema' => $subtemaId,
                'orden' => $orden,
                'per_page' => $perPage,
            ],
            'allowedPerPage' => $allowedPerPage,
        ]);
    }
}
