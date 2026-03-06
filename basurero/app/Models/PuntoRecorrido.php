<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoRecorrido extends Model
{
    protected $table = 'puntos_recorrido';

    protected $fillable = [
        'recorrido_id',
        'lat',
        'lng',
        'precision_m',
        'velocidad_mps',
        'rumbo_grados',
        'fecha_gps',
    ];

    protected $casts = [
        'fecha_gps' => 'datetime',
    ];

    public function recorrido()
    {
        return $this->belongsTo(Recorrido::class);
    }
}
