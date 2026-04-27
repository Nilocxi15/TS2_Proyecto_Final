<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class RutasAprendizaje extends Model
{
    use HasFactory;

    protected $table = 'rutas_aprendizaje';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'activa',
        'created_at',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(RutaDetalle::class, 'ruta_id');
    }

    public function subtemas(): BelongsToMany
    {
        return $this->belongsToMany(Subtemas::class, 'ruta_detalle', 'ruta_id', 'subtema_id')
            ->withPivot(['prioridad', 'completado']);
    }
}
