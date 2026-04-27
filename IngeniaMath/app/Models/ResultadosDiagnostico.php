<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ResultadosDiagnostico extends Model
{
    use HasFactory;

    protected $table = 'resultados_diagnostico';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'diagnostico_id',
        'modulo_id',
        'puntaje',
        'estado',
    ];

    protected $casts = [
        'puntaje' => 'decimal:2',
    ];

    public function diagnostico(): BelongsTo
    {
        return $this->belongsTo(Diagnosticos::class, 'diagnostico_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulos::class, 'modulo_id');
    }
}
