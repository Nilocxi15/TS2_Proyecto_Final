<?php

namespace Database\Factories;

use App\Models\Ejercicios;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ejercicios>
 */
class EjerciciosFactory extends Factory
{
    protected $model = Ejercicios::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'modulo_id' => null,
            'subtema_id' => null,
            'dificultad' => $this->faker->randomElement(['BASICO', 'INTERMEDIO', 'AVANZADO']),
            'tipo' => $this->faker->randomElement(['OPCION_MULTIPLE', 'VF', 'NUMERICO', 'COMPLETAR']),
            'enunciado' => 'Resolver: ' . $this->faker->sentence(8),
            'imagen' => null,
            'respuesta_correcta' => (string) $this->faker->numberBetween(1, 200),
            'solucion' => $this->faker->paragraph(2),
            'explicacion' => $this->faker->sentence(12),
            'tiempo_estimado' => $this->faker->numberBetween(5, 20),
            'estado' => 'PUBLICADO',
            'creado_por' => null,
            'revisado_por' => null,
            'created_at' => now(),
        ];
    }
}