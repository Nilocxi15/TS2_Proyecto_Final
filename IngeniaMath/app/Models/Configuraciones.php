<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuraciones extends Model
{
    protected $table = 'configuraciones';

    protected $primaryKey = 'clave';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
    ];

    public static function getValor(string $clave, $default = null)
    {
        $config = self::find($clave);
        return $config ? $config->valor : $default;
    }
}
