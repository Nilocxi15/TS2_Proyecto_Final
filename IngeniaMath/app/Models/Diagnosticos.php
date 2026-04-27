<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Diagnosticos extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(ResultadosDiagnostico::class, 'diagnostico_id');
    }
}
