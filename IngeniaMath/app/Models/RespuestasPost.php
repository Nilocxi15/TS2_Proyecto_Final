<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RespuestasPost extends Model
{
    use HasFactory;

    protected $table = 'respuestas_post';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'usuario_id',
        'contenido',
        'es_solucion',
        'created_at',
    ];

    protected $casts = [
        'es_solucion' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }
}
