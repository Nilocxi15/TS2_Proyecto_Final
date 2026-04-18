<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Flashcards extends Model
{
    use HasFactory;

    protected $table = 'flashcards';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'subtema_id',
        'pregunta',
        'respuesta',
    ];

    public function subtema(): BelongsTo
    {
        return $this->belongsTo(Subtemas::class, 'subtema_id');
    }
}
