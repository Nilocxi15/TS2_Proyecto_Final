<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UsuarioRoles extends Model
{
    use HasFactory;

    protected $table = 'usuario_roles';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'rol_id',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'rol_id');
    }
}
