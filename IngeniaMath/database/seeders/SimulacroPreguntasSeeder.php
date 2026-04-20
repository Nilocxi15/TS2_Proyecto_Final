<?php

namespace Database\Seeders;

use App\Models\Ejercicios;
use App\Models\SimulacroPreguntas;
use App\Models\Simulacros;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SimulacroPreguntasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $simulacros = Simulacros::query()
            ->whereIn('duracion', [75, 80, 70])
            ->whereIn('fecha', [
                Carbon::create(2026, 1, 20, 9, 0, 0),
                Carbon::create(2026, 1, 23, 10, 30, 0),
                Carbon::create(2026, 1, 27, 8, 45, 0),
            ])
            ->orderBy('fecha')
            ->get();

        if ($simulacros->count() < 3) {
            return;
        }

        $ejerciciosIds = Ejercicios::query()
            ->where('estado', 'PUBLICADO')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        if ($ejerciciosIds->count() < 30) {
            return;
        }

        foreach ($simulacros as $index => $simulacro) {
            $inicio = $index * 10;
            $preguntasIds = $ejerciciosIds->slice($inicio, 10)->values();

            if ($preguntasIds->count() < 10) {
                continue;
            }

            SimulacroPreguntas::query()->where('simulacro_id', $simulacro->id)->delete();

            $respuestasCorrectas = 0;

            foreach ($preguntasIds as $i => $ejercicioId) {
                $esCorrecta = (($i + $index) % 3) !== 0;

                SimulacroPreguntas::query()->create([
                    'simulacro_id' => $simulacro->id,
                    'ejercicio_id' => $ejercicioId,
                    'es_correcta' => $esCorrecta,
                ]);

                if ($esCorrecta) {
                    $respuestasCorrectas++;
                }
            }

            $puntaje = round(($respuestasCorrectas / 10) * 100, 2);

            Simulacros::query()
                ->whereKey($simulacro->id)
                ->update(['puntaje' => $puntaje]);
        }
    }
}
