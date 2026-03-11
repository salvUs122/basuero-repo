<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'descripcion'];

    /**
     * Obtener el valor de una configuración por clave.
     */
    public static function obtener(string $clave, string $default = ''): string
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }
}
