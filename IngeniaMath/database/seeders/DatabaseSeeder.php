<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            ModulosSeeder::class,

            UsuarioSeeder::class,
            UsuarioRolesSeeder::class,
            SubtemasSeeder::class,
            EjerciciosSeeder::class,
            SimulacrosSeeder::class,
            SimulacroPreguntasSeeder::class,
            RutasAprendizajeSeeder::class,
            RutaDetalleSeeder::class,
            FlashcardsSeeder::class,
        ]);
    }
}
