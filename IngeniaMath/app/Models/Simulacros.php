<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Simulacros extends Model
{
    use HasFactory;

    protected $table = 'simulacros';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'fecha',
        'duracion',
        'puntaje',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'duracion' => 'integer',
        'puntaje' => 'decimal:2',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function simulacroPreguntas(): HasMany
    {
        return $this->hasMany(SimulacroPreguntas::class, 'simulacro_id');
    }

    public function ejercicios(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicios::class, 'simulacro_preguntas', 'simulacro_id', 'ejercicio_id')
            ->withPivot('es_correcta');
    }
}
