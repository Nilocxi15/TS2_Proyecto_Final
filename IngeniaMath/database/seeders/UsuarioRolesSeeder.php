<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\UsuarioRoles;
use App\Models\Usuarios;
use Illuminate\Database\Seeder;

class UsuarioRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Roles::orderBy('id')->get();
        $usuarios = Usuarios::orderBy('id')->get();

        if ($roles->isEmpty() || $usuarios->count() < $roles->count() * 3) {
            return;
        }

        foreach ($roles as $index => $role) {
            $usuariosForRole = $usuarios->slice($index * 3, 3);

            foreach ($usuariosForRole as $usuario) {
                UsuarioRoles::firstOrCreate([
                    'usuario_id' => $usuario->id,
                    'rol_id' => $role->id,
                ]);
            }
        }
    }
}
