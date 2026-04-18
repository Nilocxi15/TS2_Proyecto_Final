<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SimulacroPreguntas extends Model
{
    use HasFactory;

    protected $table = 'simulacro_preguntas';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'simulacro_id',
        'ejercicio_id',
        'es_correcta',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function simulacro(): BelongsTo
    {
        return $this->belongsTo(Simulacros::class, 'simulacro_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicios::class, 'ejercicio_id');
    }
}
