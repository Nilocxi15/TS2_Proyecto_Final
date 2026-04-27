<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Recursos extends Model
{
    use HasFactory;

    protected $table = 'recursos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'modulo_id',
        'subtema_id',
        'tipo',
        'url',
        'estado',
        'creado_por',
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
}
