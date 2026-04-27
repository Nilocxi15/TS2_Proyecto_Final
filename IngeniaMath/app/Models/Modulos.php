<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function subtemas(): HasMany
    {
        return $this->hasMany(Subtemas::class, 'modulo_id');
    }

    public function ejercicios(): HasMany
    {
        return $this->hasMany(Ejercicios::class, 'modulo_id');
    }

    public function resultadosDiagnostico(): HasMany
    {
        return $this->hasMany(ResultadosDiagnostico::class, 'modulo_id');
    }

    public function recursos(): HasMany
    {
        return $this->hasMany(Recursos::class, 'modulo_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Posts::class, 'modulo_id');
    }
}
