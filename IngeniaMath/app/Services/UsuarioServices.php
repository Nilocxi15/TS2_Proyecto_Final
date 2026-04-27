<?php

namespace App\Services;

use App\Enums\RolEnum;
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
        return $this->crearUsuario($data, RolEnum::ESTUDIANTE->value);
	}

    public function crearUsuario(array $data, int $rolId): Usuarios
    {
        return DB::transaction(function () use ($data, $rolId): Usuarios {

            $usuario = Usuarios::create([
                'nombre' => $data['name'] ?? $data['nombre'],
                'apellido' => $data['lastname'] ?? $data['apellido'],
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'foto_perfil' => 'https://res.cloudinary.com/dbsqzub25/image/upload/v1772951140/user-blue-gradient_78370-4692_othf7b.jpg',
                'activo' => true,
                'created_at' => now(),
            ]);

            $rol = Roles::find($rolId);

            if (! $rol) {
                throw new RuntimeException('El rol no existe.');
            }

            UsuarioRoles::create([
                'usuario_id' => $usuario->id,
                'rol_id' => $rol->id,
            ]);

            return $usuario;
        });
    }

    public function actualizarUsuario(Usuarios $usuario, array $data, int $rolId): Usuarios
    {
        return DB::transaction(function () use ($usuario, $data, $rolId) {

            $usuario->update([
                'nombre' => $data['nombre'] ?? $data['name'],
                'apellido' => $data['apellido'] ?? $data['lastname'],
                'email' => $data['email'],
                'activo' => $data['activo'] ?? true,
            ]);

            // Password opcional
            if (!empty($data['password'])) {
                $usuario->update([
                    'password_hash' => Hash::make($data['password'])
                ]);
            }

            // Un solo rol
            $usuario->roles()->sync([$rolId]);

            return $usuario;
        });
    }
}
