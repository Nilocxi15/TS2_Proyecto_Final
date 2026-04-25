<?php

namespace App\Services;

use App\Models\Roles;
use App\Models\UsuarioRoles;
use App\Models\Usuarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsuarioServices
{
	public function registrarEstudiante(array $data): Usuarios
	{
		return DB::transaction(function () use ($data): Usuarios {
			$usuario = Usuarios::create([
				'nombre' => $data['name'],
				'apellido' => $data['lastname'],
				'email' => $data['email'],
				'password_hash' => Hash::make($data['password']),
				'foto_perfil' => 'https://res.cloudinary.com/dbsqzub25/image/upload/v1772951140/user-blue-gradient_78370-4692_othf7b.jpg',
				'activo' => true,
				'created_at' => now(),
			]);

			$rolEstudiante = Roles::find(1);

			if (! $rolEstudiante) {
				throw new RuntimeException('No existe el rol Estudiante en la base de datos.');
			}

			UsuarioRoles::create([
				'usuario_id' => $usuario->id,
				'rol_id' => $rolEstudiante->id,
			]);

			return $usuario;
		});
	}
}
