<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert([
            'nombre'=>'Estudiante',
        ]);
        DB::table('roles')->updateOrInsert([
            'nombre'=>'Tutor',
        ]);
        DB::table('roles')->updateOrInsert([
            'nombre'=>'Administrador',
        ]);
        DB::table('roles')->updateOrInsert([
            'nombre'=>'Revisor',
        ]);
    }
}
