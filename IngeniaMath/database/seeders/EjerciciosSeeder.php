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
            ->get()
            ->groupBy('modulo_id');

        DB::table('ejercicios')
            ->where('enunciado', 'like', '[Seeder] %')
            ->delete();

        $ejerciciosPorModulo = [

            1 => [
                ['enunciado' => 'Calcula | -5 + 3 |', 'respuesta' => '2', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Convierte 0.75 a fracción', 'respuesta' => '3/4', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Calcula 20% de 150', 'respuesta' => '30', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Calcula el MCD de 24 y 36', 'respuesta' => '12', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Calcula el MCM de 8 y 12', 'respuesta' => '24', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Simplifica √50', 'respuesta' => '5√2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Racionaliza 1/√3', 'respuesta' => '√3/3', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Resuelve: |2x-5|=7', 'respuesta' => 'x=6 o x=-1', 'dificultad' => 'AVANZADO'],
                ['enunciado' => 'Calcula 15% de 240', 'respuesta' => '36', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Simplifica 18/24', 'respuesta' => '3/4', 'dificultad' => 'BASICO'],
            ],

            2 => [
                ['enunciado' => 'Expande (x+3)^2', 'respuesta' => 'x^2+6x+9', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Factoriza x^2-9', 'respuesta' => '(x-3)(x+3)', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Simplifica (2x)(3x)', 'respuesta' => '6x^2', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Factoriza 2x^2+4x', 'respuesta' => '2x(x+2)', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Simplifica (x^2/x)', 'respuesta' => 'x', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Expande (x-2)(x+5)', 'respuesta' => 'x^2+3x-10', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Factoriza x^2+5x+6', 'respuesta' => '(x+2)(x+3)', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Simplifica (x^2-1)/(x-1)', 'respuesta' => 'x+1', 'dificultad' => 'AVANZADO'],
                ['enunciado' => 'Expande (2x+1)^2', 'respuesta' => '4x^2+4x+1', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Factoriza x^2-4x', 'respuesta' => 'x(x-4)', 'dificultad' => 'BASICO'],
            ],

            3 => [
                ['enunciado' => 'Resuelve x+5=12', 'respuesta' => '7', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Resuelve 2x=10', 'respuesta' => '5', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Resuelve x-3=0', 'respuesta' => '3', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Resuelve x^2=16', 'respuesta' => 'x=4 o x=-4', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Resuelve x^2-5x=0', 'respuesta' => 'x=0 o x=5', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Resuelve 3x+2=11', 'respuesta' => '3', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Resuelve x^2-4=0', 'respuesta' => 'x=2 o x=-2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Resuelve x^2+2x-3=0', 'respuesta' => 'x=1 o x=-3', 'dificultad' => 'AVANZADO'],
                ['enunciado' => 'Resuelve 4x-8=0', 'respuesta' => '2', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Resuelve 5x=25', 'respuesta' => '5', 'dificultad' => 'BASICO'],
            ],

            4 => [
                ['enunciado' => 'Evalúa f(x)=x+2 en x=3', 'respuesta' => '5', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Evalúa f(x)=2x en x=4', 'respuesta' => '8', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Dominio de f(x)=1/x', 'respuesta' => 'x≠0', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Evalúa f(x)=x^2 en x=3', 'respuesta' => '9', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Composición g(f(x)) con f(x)=x+1 y g(x)=2x', 'respuesta' => '2x+2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Dominio de √x', 'respuesta' => 'x≥0', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Evalúa f(x)=x^2+1 en x=2', 'respuesta' => '5', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'f(g(x)) con f(x)=x^2 y g(x)=x+1', 'respuesta' => '(x+1)^2', 'dificultad' => 'AVANZADO'],
                ['enunciado' => 'Evalúa f(x)=3x en x=2', 'respuesta' => '6', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Dominio de 1/(x-2)', 'respuesta' => 'x≠2', 'dificultad' => 'INTERMEDIO'],
            ],

            5 => [
                ['enunciado' => 'Pendiente entre (0,0) y (2,2)', 'respuesta' => '1', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Punto medio entre (0,0) y (2,2)', 'respuesta' => '(1,1)', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Distancia entre (0,0) y (3,4)', 'respuesta' => '5', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Pendiente entre (1,2) y (3,6)', 'respuesta' => '2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Punto medio (2,4) y (6,8)', 'respuesta' => '(4,6)', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Distancia (1,1) y (4,5)', 'respuesta' => '5', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Ecuación de recta pendiente 2 que pasa por (0,1)', 'respuesta' => 'y=2x+1', 'dificultad' => 'AVANZADO'],
                ['enunciado' => 'Pendiente entre (2,2) y (4,2)', 'respuesta' => '0', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Distancia (0,0) y (1,1)', 'respuesta' => '√2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Punto medio (1,3) y (5,7)', 'respuesta' => '(3,5)', 'dificultad' => 'BASICO'],
            ],

            6 => [
                ['enunciado' => 'Área de cuadrado lado 4', 'respuesta' => '16', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Perímetro de rectángulo 2x3', 'respuesta' => '10', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Hipotenusa con catetos 3 y 4', 'respuesta' => '5', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Área triángulo base 4 altura 3', 'respuesta' => '6', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Volumen cubo lado 3', 'respuesta' => '27', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Área círculo radio 2', 'respuesta' => '4π', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Área rectángulo 5x6', 'respuesta' => '30', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Hipotenusa 5 y 12', 'respuesta' => '13', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Área triángulo base 10 altura 5', 'respuesta' => '25', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Volumen prisma base 6 altura 5', 'respuesta' => '30', 'dificultad' => 'AVANZADO'],
            ],

            7 => [
                ['enunciado' => 'sin(90°)', 'respuesta' => '1', 'dificultad' => 'BASICO'],
                ['enunciado' => 'cos(0°)', 'respuesta' => '1', 'dificultad' => 'BASICO'],
                ['enunciado' => 'tan(45°)', 'respuesta' => '1', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Convierte 180° a radianes', 'respuesta' => 'π', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'Convierte π/2 a grados', 'respuesta' => '90', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'sin(30°)', 'respuesta' => '1/2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'cos(60°)', 'respuesta' => '1/2', 'dificultad' => 'INTERMEDIO'],
                ['enunciado' => 'tan(0°)', 'respuesta' => '0', 'dificultad' => 'BASICO'],
                ['enunciado' => 'sin(0°)', 'respuesta' => '0', 'dificultad' => 'BASICO'],
                ['enunciado' => 'Identidad sin²(x)+cos²(x)', 'respuesta' => '1', 'dificultad' => 'AVANZADO'],
            ],
        ];

        foreach ($modulos as $moduloId) {

            $subtemas = $subtemasPorModulo->get($moduloId, collect())->pluck('id')->values();

            foreach ($ejerciciosPorModulo[$moduloId] as $index => $ejercicio) {

                $subtemaId = $subtemas->isNotEmpty()
                    ? $subtemas[$index % $subtemas->count()]
                    : null;

                $creadoPor = $usuariosIds[array_rand($usuariosIds)];
                $revisadoPor = $usuariosIds[array_rand($usuariosIds)];

                Ejercicios::updateOrCreate(
                    [
                        'enunciado' => $ejercicio['enunciado'],
                        'modulo_id' => $moduloId
                    ],
                    [
                        'subtema_id' => $subtemaId,
                        'dificultad' => $ejercicio['dificultad'],
                        'tipo' => 'NUMERICO',
                        'respuesta_correcta' => $ejercicio['respuesta'],
                        'solucion' => 'Solución paso a paso.',
                        'explicacion' => 'Ejercicio del módulo.',
                        'tiempo_estimado' => 10,
                        'estado' => 'PUBLICADO',
                        'creado_por' => $creadoPor,
                        'revisado_por' => $revisadoPor,
                    ]
                );
            }
        }
    }
}