<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'modulo_id',
        'subtema_id',
        'contenido',
        'estado',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulos::class, 'modulo_id');
    }

    public function subtema(): BelongsTo
    {
        return $this->belongsTo(Subtemas::class, 'subtema_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestasPost::class, 'post_id');
    }
}
