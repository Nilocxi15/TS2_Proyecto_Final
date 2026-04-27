<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Ejercicios extends Model
{
    use HasFactory;

    protected $table = 'ejercicios';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'modulo_id',
        'subtema_id',
        'dificultad',
        'tipo',
        'enunciado',
        'imagen',
        'respuesta_correcta',
        'solucion',
        'explicacion',
        'tiempo_estimado',
        'estado',
        'creado_por',
        'revisado_por',
        'created_at',
    ];

    protected $casts = [
        'tiempo_estimado' => 'integer',
        'created_at' => 'datetime',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulos::class, 'modulo_id');
    }

    public function subtema(): BelongsTo
    {
        return $this->belongsTo(Subtemas::class, 'subtema_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'creado_por');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'revisado_por');
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestasUsuario::class, 'ejercicio_id');
    }

    public function relacionados(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicios::class, 'ejercicios_relacionados', 'ejercicio_id', 'relacionado_id');
    }

    public function relacionadoDe(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicios::class, 'ejercicios_relacionados', 'relacionado_id', 'ejercicio_id');
    }

    public function usuariosGuardaron(): BelongsToMany
    {
        return $this->belongsToMany(Usuarios::class, 'ejercicios_guardados', 'ejercicio_id', 'usuario_id');
    }

    public function simulacros(): BelongsToMany
    {
        return $this->belongsToMany(Simulacros::class, 'simulacro_preguntas', 'ejercicio_id', 'simulacro_id')
            ->withPivot('es_correcta');
    }
}
