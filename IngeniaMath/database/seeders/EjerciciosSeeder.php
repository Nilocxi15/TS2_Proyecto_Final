<?php

namespace Database\Seeders;

use App\Models\Ejercicios;
use App\Models\Modulos;
use App\Models\Subtemas;
use App\Models\Usuarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EjerciciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = Modulos::query()->orderBy('id')->pluck('id')->values();

        if ($modulos->isEmpty()) {
            return;
        }

        $usuariosIds = Usuarios::query()->pluck('id')->all();
        if (empty($usuariosIds)) {
            $usuariosIds = Usuarios::factory()->count(2)->create()->pluck('id')->all();
        }

        $subtemasPorModulo = Subtemas::query()
            ->select('id', 'modulo_id')
            ->orderBy('id')
            ->get()
            ->groupBy('modulo_id');

        DB::table('ejercicios')
            ->where('enunciado', 'like', '[Seeder] %')
            ->delete();

        $dificultades = [
            'BASICO',
            'INTERMEDIO',
            'BASICO',
            'INTERMEDIO',
            'AVANZADO',
            'BASICO',
            'INTERMEDIO',
            'AVANZADO',
            'BASICO',
            'INTERMEDIO',
        ];

        $tipos = ['OPCION_MULTIPLE', 'VF', 'NUMERICO', 'COMPLETAR'];

        foreach ($modulos as $moduloId) {
            $subtemas = $subtemasPorModulo->get($moduloId, collect())->pluck('id')->values();

            for ($i = 0; $i < 10; $i++) {
                $subtemaId = $subtemas->isNotEmpty()
                    ? $subtemas[$i % $subtemas->count()]
                    : null;

                $creadoPor = $usuariosIds[array_rand($usuariosIds)];
                $revisadoPor = $usuariosIds[array_rand($usuariosIds)];

                Ejercicios::factory()->create([
                    'modulo_id' => $moduloId,
                    'subtema_id' => $subtemaId,
                    'dificultad' => $dificultades[$i],
                    'tipo' => $tipos[$i % count($tipos)],
                    'enunciado' => sprintf('[Seeder] Ejercicio M%s-%02d', $moduloId, $i + 1),
                    'respuesta_correcta' => (string) (($moduloId * 10) + $i + 1),
                    'solucion' => 'Desarrollo paso a paso del ejercicio semilla.',
                    'explicacion' => 'Ejercicio publicado para pruebas y práctica por módulo.',
                    'tiempo_estimado' => 8 + ($i % 7),
                    'estado' => 'PUBLICADO',
                    'creado_por' => $creadoPor,
                    'revisado_por' => $revisadoPor,
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
            }
        }
    }
}
