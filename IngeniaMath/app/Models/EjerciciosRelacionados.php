<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EjerciciosRelacionados extends Model
{
    use HasFactory;

    protected $table = 'ejercicios_relacionados';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'ejercicio_id',
        'relacionado_id',
    ];

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicios::class, 'ejercicio_id');
    }

    public function relacionado(): BelongsTo
    {
        return $this->belongsTo(Ejercicios::class, 'relacionado_id');
    }
}
