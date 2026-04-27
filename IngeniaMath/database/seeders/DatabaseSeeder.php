<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedersByTable = [
            'roles' => RolesSeeder::class,
            'modulos' => ModulosSeeder::class,
            'usuarios' => UsuarioSeeder::class,
            'usuario_roles' => UsuarioRolesSeeder::class,
            'subtemas' => SubtemasSeeder::class,
            'ejercicios' => EjerciciosSeeder::class,
            'ejercicios_relacionados' => EjerciciosRelacionadosSeeder::class,
            'simulacros' => SimulacrosSeeder::class,
            'simulacro_preguntas' => SimulacroPreguntasSeeder::class,
            'rutas_aprendizaje' => RutasAprendizajeSeeder::class,
            'ruta_detalle' => RutaDetalleSeeder::class,
            'flashcards' => FlashcardsSeeder::class,
            'recursos' => RecursosSeeder::class,
        ];

        foreach ($seedersByTable as $table => $seederClass) {
            if (Schema::hasTable($table)) {
                $this->call($seederClass);
            }
        }
    }
}
