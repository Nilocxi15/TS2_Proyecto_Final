<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RespuestasUsuario extends Model
{
    use HasFactory;

    protected $table = 'respuestas_usuario';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'ejercicio_id',
        'sesion_id',
        'respuesta',
        'es_correcta',
        'tiempo_respuesta',
        'created_at',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
        'tiempo_respuesta' => 'integer',
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicios::class, 'ejercicio_id');
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(SesionesPractica::class, 'sesion_id');
    }
}
