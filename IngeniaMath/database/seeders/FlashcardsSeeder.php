<?php

namespace Database\Seeders;

use DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlashcardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flashcards = [
            ['subtema_id' => 1, 'pregunta' => '¿Qué representa el valor absoluto de un número?', 'respuesta' => 'Representa la distancia de un número al 0 en la recta numérica, siempre es no negativo.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 5, 'pregunta' => '¿Cómo se calcula el MCD (Máximo Común Divisor)?', 'respuesta' => 'Se descomponen los números en factores primos y se multiplican los factores comunes con el menor exponente.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 7, 'pregunta' => '¿Cuál es la fórmula del cuadrado de un binomio?', 'respuesta' => '(a + b)² = a² + 2ab + b²', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 8, 'pregunta' => '¿Qué es la factorización?', 'respuesta' => 'Es el proceso de expresar una expresión algebraica como el producto de factores más simples.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 11, 'pregunta' => '¿Cómo se resuelve una ecuación de primer grado?', 'respuesta' => 'Se despeja la variable aislándola en un lado de la ecuación mediante operaciones inversas.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 12, 'pregunta' => '¿Cuál es la fórmula general de una ecuación cuadrática?', 'respuesta' => 'x = (-b ± √(b² - 4ac)) / (2a)', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 15, 'pregunta' => '¿Qué es el dominio de una función?', 'respuesta' => 'Es el conjunto de todos los valores de entrada (x) para los cuales la función está definida.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 20, 'pregunta' => '¿Cómo se calcula la distancia entre dos puntos del plano?', 'respuesta' => 'Se usa la fórmula d = √((x2 - x1)² + (y2 - y1)²).', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 23, 'pregunta' => '¿Qué establece el teorema de Pitágoras?', 'respuesta' => 'En un triángulo rectángulo, el cuadrado de la hipotenusa es igual a la suma de los cuadrados de los catetos.', 'estado' => 'PUBLICADO', 'creado_por' => 4],
            ['subtema_id' => 28, 'pregunta' => '¿Cuál es la relación fundamental de la trigonometría?', 'respuesta' => 'sen²(x) + cos²(x) = 1', 'estado' => 'PUBLICADO', 'creado_por' => 4],
        ];

        foreach ($flashcards as $flashcard) {
            DB::table('flashcards')->updateOrInsert(
                [
                    'subtema_id' => $flashcard['subtema_id'],
                    'pregunta' => $flashcard['pregunta'],
                    'respuesta' => $flashcard['respuesta'],
                ],
                [
                    'respuesta' => $flashcard['respuesta'],
                    'estado' => $flashcard['estado'],
                    'creado_por' => $flashcard['creado_por'],
                ]
            );
        }
    }
}
