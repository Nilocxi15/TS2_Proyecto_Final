<?php

namespace Database\Factories;

use App\Models\Usuarios;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuarios>
 */
class UsuariosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => bcrypt('password'),
            'foto_perfil' => 'https://res.cloudinary.com/dbsqzub25/image/upload/v1772951140/user-blue-gradient_78370-4692_othf7b.jpg',
            'activo' => true,
            'created_at' => now(),
        ];
    }
}