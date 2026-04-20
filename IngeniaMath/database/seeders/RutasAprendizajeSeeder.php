<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\RutasAprendizaje;
use App\Models\Usuarios;
use Illuminate\Database\Seeder;

class RutasAprendizajeSeeder extends Seeder
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

        $studentIds = $studentIds->take(2);

        foreach ($studentIds as $studentId) {
            RutasAprendizaje::query()->updateOrCreate(
                [
                    'usuario_id' => $studentId,
                ],
                [
                    'activa' => true,
                    'created_at' => now(),
                ]
            );
        }
    }
}
