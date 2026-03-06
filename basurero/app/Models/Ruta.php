<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Camion;


class Ruta extends Model
{
    protected $table = 'rutas';

    protected $fillable = [
        'nombre',
        'estado',
        'tolerancia_metros',
        'geometria_geojson',
    ];

    public function camiones()
    {
        return $this->belongsToMany(Camion::class, 'ruta_camion')
            ->withPivot('activa')
            ->withTimestamps();
    }

}
