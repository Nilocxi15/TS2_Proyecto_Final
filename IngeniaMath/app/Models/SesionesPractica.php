<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class SesionesPractica extends Model
{
    use HasFactory;

    protected $table = 'sesiones_practica';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'modo',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestasUsuario::class, 'sesion_id');
    }
}
