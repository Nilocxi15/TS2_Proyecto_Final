<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EjerciciosGuardados extends Model
{
    use HasFactory;

    protected $table = 'ejercicios_guardados';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'ejercicio_id',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicios::class, 'ejercicio_id');
    }
}
