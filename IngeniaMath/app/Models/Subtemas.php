<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Subtemas extends Model
{
    use HasFactory;

    protected $table = 'subtemas';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'modulo_id',
        'nombre',
        'nivel_complejidad',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulos::class, 'modulo_id');
    }

    public function ejercicios(): HasMany
    {
        return $this->hasMany(Ejercicios::class, 'subtema_id');
    }

    public function rutaDetalle(): HasMany
    {
        return $this->hasMany(RutaDetalle::class, 'subtema_id');
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(Recursos::class, 'subtema_id');
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcards::class, 'subtema_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Posts::class, 'subtema_id');
    }

    public function rutasAprendizaje(): BelongsToMany
    {
        return $this->belongsToMany(RutasAprendizaje::class, 'ruta_detalle', 'subtema_id', 'ruta_id')
            ->withPivot(['prioridad', 'completado']);
    }
}
