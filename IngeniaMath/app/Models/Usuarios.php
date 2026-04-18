<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password_hash',
        'foto_perfil',
        'activo',
        'created_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'usuario_roles', 'usuario_id', 'rol_id');
    }

    public function ejerciciosCreados(): HasMany
    {
        return $this->hasMany(Ejercicios::class, 'creado_por');
    }

    public function ejerciciosRevisados(): HasMany
    {
        return $this->hasMany(Ejercicios::class, 'revisado_por');
    }

    public function sesionesPractica(): HasMany
    {
        return $this->hasMany(SesionesPractica::class, 'usuario_id');
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestasUsuario::class, 'usuario_id');
    }

    public function diagnosticos(): HasMany
    {
        return $this->hasMany(Diagnosticos::class, 'usuario_id');
    }

    public function rutasAprendizaje(): HasMany
    {
        return $this->hasMany(RutasAprendizaje::class, 'usuario_id');
    }

    public function simulacros(): HasMany
    {
        return $this->hasMany(Simulacros::class, 'usuario_id');
    }

    public function recursosCreados(): HasMany
    {
        return $this->hasMany(Recursos::class, 'creado_por');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Posts::class, 'usuario_id');
    }

    public function respuestasPost(): HasMany
    {
        return $this->hasMany(RespuestasPost::class, 'usuario_id');
    }

    public function ejerciciosGuardados(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicios::class, 'ejercicios_guardados', 'usuario_id', 'ejercicio_id');
    }
}
