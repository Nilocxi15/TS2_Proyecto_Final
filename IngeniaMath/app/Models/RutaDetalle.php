<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RutaDetalle extends Model
{
    use HasFactory;

    protected $table = 'ruta_detalle';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'ruta_id',
        'subtema_id',
        'prioridad',
        'completado',
    ];

    protected $casts = [
        'prioridad' => 'integer',
        'completado' => 'boolean',
    ];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(RutasAprendizaje::class, 'ruta_id');
    }

    public function subtema(): BelongsTo
    {
        return $this->belongsTo(Subtemas::class, 'subtema_id');
    }
}
