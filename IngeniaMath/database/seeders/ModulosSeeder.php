<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = [
            'Números Reales',
            'Fundamentos de Álgebra',
            'Ecuaciones e Inecuaciones',
            'Funciones y Gráficas',
            'Geometría Analítica',
            'Geometría',
            'Trigonometría',
        ];

        foreach ($modulos as $modulo) {
            DB::table('modulos')->updateOrInsert([
                'nombre' => $modulo
            ]);
        }
    }
}