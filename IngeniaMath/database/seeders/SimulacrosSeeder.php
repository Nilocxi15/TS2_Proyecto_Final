<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\Simulacros;
use App\Models\Usuarios;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SimulacrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estudianteRoleId = Roles::query()
            ->whereRaw('LOWER(nombre) = ?', ['estudiante'])
            ->value('id');

        $studentIds = collect();

        if ($estudianteRoleId) {
            $studentIds = Usuarios::query()
                ->whereHas('roles', function ($query) use ($estudianteRoleId) {
                    $query->where('roles.id', $estudianteRoleId);
                })
                ->orderBy('id')
                ->pluck('id');
        }

        if ($studentIds->count() < 2) {
            $studentIds = Usuarios::query()->orderBy('id')->limit(2)->pluck('id');
        }

        if ($studentIds->count() < 2) {
            $studentIds = Usuarios::factory()->count(2)->create()->pluck('id');
        }

        $studentIds = $studentIds->values();

        $simulacrosBase = [
            [
                'usuario_id' => $studentIds[0],
                'fecha' => Carbon::create(2026, 1, 20, 9, 0, 0),
                'duracion' => 75,
                'puntaje' => 0,
            ],
            [
                'usuario_id' => $studentIds[1],
                'fecha' => Carbon::create(2026, 1, 23, 10, 30, 0),
                'duracion' => 80,
                'puntaje' => 0,
            ],
            [
                'usuario_id' => $studentIds[0],
                'fecha' => Carbon::create(2026, 1, 27, 8, 45, 0),
                'duracion' => 70,
                'puntaje' => 0,
            ],
        ];

        foreach ($simulacrosBase as $simulacro) {
            Simulacros::query()->updateOrCreate(
                [
                    'usuario_id' => $simulacro['usuario_id'],
                    'fecha' => $simulacro['fecha'],
                    'duracion' => $simulacro['duracion'],
                ],
                [
                    'puntaje' => $simulacro['puntaje'],
                ]
            );
        }
    }
}
