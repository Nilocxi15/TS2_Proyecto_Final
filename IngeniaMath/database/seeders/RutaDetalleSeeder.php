<?php

namespace Database\Seeders;

use App\Models\RutaDetalle;
use App\Models\RutasAprendizaje;
use App\Models\Subtemas;
use Illuminate\Database\Seeder;

class RutaDetalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rutas = RutasAprendizaje::query()
            ->orderBy('id')
            ->limit(2)
            ->get();

        if ($rutas->isEmpty()) {
            return;
        }

        $subtemasIds = Subtemas::query()
            ->orderBy('nivel_complejidad')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        if ($subtemasIds->isEmpty()) {
            return;
        }

        foreach ($rutas as $rutaIndex => $ruta) {
            $seleccion = $subtemasIds->slice($rutaIndex * 6, 6)->values();

            if ($seleccion->count() < 6) {
                $faltantes = 6 - $seleccion->count();
                $seleccion = $seleccion->concat($subtemasIds->take($faltantes))->values();
            }

            RutaDetalle::query()->where('ruta_id', $ruta->id)->delete();

            foreach ($seleccion as $i => $subtemaId) {
                RutaDetalle::query()->create([
                    'ruta_id' => $ruta->id,
                    'subtema_id' => $subtemaId,
                    'prioridad' => $i + 1,
                    'completado' => $i < 2,
                ]);
            }
        }
    }
}
