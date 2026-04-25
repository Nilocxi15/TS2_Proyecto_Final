<?php

namespace App\Enums;

enum RolEnum:int
{
    case ESTUDIANTE = 1;
    case TUTOR = 2;
    case ADMINISTRADOR = 3;
    case REVISOR = 4;

    public function label(): string
    {
        return match($this) {
            self::ESTUDIANTE => 'Estudiante',
            self::TUTOR => 'Tutor',
            self::ADMINISTRADOR => 'Administrador',
            self::REVISOR => 'Revisor',
        };
    }
}
