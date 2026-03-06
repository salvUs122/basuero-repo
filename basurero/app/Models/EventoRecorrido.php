<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoRecorrido extends Model
{
    protected $table = 'eventos_recorrido';

    protected $fillable = [
        'recorrido_id',
        'tipo',
        'mensaje',
        'lat',
        'lng',
        'distancia_m',
        'fecha_evento',
    ];

    protected $casts = [
        'fecha_evento' => 'datetime',
    ];

    public function recorrido()
    {
        return $this->belongsTo(\App\Models\Recorrido::class);
    }
}
