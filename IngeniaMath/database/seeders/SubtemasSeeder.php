<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubtemasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subtemas = [
            ['modulo_id' => 1, 'nombre' => 'Valor Absoluto', 'nivel_complejidad' => 2],
            ['modulo_id' => 1, 'nombre' => 'Racionalización', 'nivel_complejidad' => 4],
            ['modulo_id' => 1, 'nombre' => 'Proporcionalidad', 'nivel_complejidad' => 3],
            ['modulo_id' => 1, 'nombre' => 'Porcentajes', 'nivel_complejidad' => 3],
            ['modulo_id' => 1, 'nombre' => 'Máximo Común Divisor', 'nivel_complejidad' => 2],
            ['modulo_id' => 1, 'nombre' => 'Mínimo Común Múltiplo', 'nivel_complejidad' => 2],

            ['modulo_id' => 2, 'nombre' => 'Productos Notables', 'nivel_complejidad' => 5],
            ['modulo_id' => 2, 'nombre' => 'Factorización', 'nivel_complejidad' => 6],
            ['modulo_id' => 2, 'nombre' => 'Fracciones Algebraicas', 'nivel_complejidad' => 6],
            ['modulo_id' => 2, 'nombre' => 'Teorema del Residuo', 'nivel_complejidad' => 7],

            ['modulo_id' => 3, 'nombre' => 'Ecuaciones de Primer Grado', 'nivel_complejidad' => 4],
            ['modulo_id' => 3, 'nombre' => 'Ecuaciones Cuadráticas', 'nivel_complejidad' => 6],
            ['modulo_id' => 3, 'nombre' => 'Ecuaciones Reducibles a Cuadráticas', 'nivel_complejidad' => 7],
            ['modulo_id' => 3, 'nombre' => 'Inecuaciones', 'nivel_complejidad' => 5],

            ['modulo_id' => 4, 'nombre' => 'Dominio y Rango', 'nivel_complejidad' => 5],
            ['modulo_id' => 4, 'nombre' => 'Función Lineal', 'nivel_complejidad' => 4],
            ['modulo_id' => 4, 'nombre' => 'Función Cuadrática', 'nivel_complejidad' => 6],
            ['modulo_id' => 4, 'nombre' => 'Composición de Funciones', 'nivel_complejidad' => 7],

            ['modulo_id' => 5, 'nombre' => 'Ecuación de la Recta', 'nivel_complejidad' => 5],
            ['modulo_id' => 5, 'nombre' => 'Distancia y Punto Medio', 'nivel_complejidad' => 4],
            ['modulo_id' => 5, 'nombre' => 'Circunferencia', 'nivel_complejidad' => 6],
            ['modulo_id' => 5, 'nombre' => 'Parábola', 'nivel_complejidad' => 7],

            ['modulo_id' => 6, 'nombre' => 'Teorema de Pitágoras', 'nivel_complejidad' => 3],
            ['modulo_id' => 6, 'nombre' => 'Semejanza de Triángulos', 'nivel_complejidad' => 5],
            ['modulo_id' => 6, 'nombre' => 'Áreas y Perímetros', 'nivel_complejidad' => 4],
            ['modulo_id' => 6, 'nombre' => 'Volúmenes', 'nivel_complejidad' => 5],

            ['modulo_id' => 7, 'nombre' => 'Conversión Grados-Radianes', 'nivel_complejidad' => 3],
            ['modulo_id' => 7, 'nombre' => 'Funciones Trigonométricas', 'nivel_complejidad' => 6],
            ['modulo_id' => 7, 'nombre' => 'Resolución de Triángulos', 'nivel_complejidad' => 7],
        ];

        foreach ($subtemas as $subtema) {
            DB::table('subtemas')->updateOrInsert(
                [
                    'modulo_id' => $subtema['modulo_id'],
                    'nombre' => $subtema['nombre'],
                ],
                [
                    'nivel_complejidad' => $subtema['nivel_complejidad'],
                ]
            );
        }
    }
}
