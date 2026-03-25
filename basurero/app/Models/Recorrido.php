<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    protected $table = 'recorridos';

    protected $fillable = [
        'ruta_id',
        'camion_id',
        'conductor_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'lat_inicio',
        'lng_inicio',
        'lat_fin',
        'lng_fin',
        'total_puntos',
        'eventos_fuera_ruta',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function ruta()
    {
        return $this->belongsTo(\App\Models\Ruta::class);
    }

    public function camion()
    {
        return $this->belongsTo(\App\Models\Camion::class);
    }

    public function conductor()
    {
        return $this->belongsTo(\App\Models\User::class, 'conductor_id');
    }

    public function puntos()
    {
        return $this->hasMany(\App\Models\PuntoRecorrido::class);
    }

    public function eventos()
    {
        return $this->hasMany(\App\Models\EventoRecorrido::class);
    }

    public function descargas()
    {
        return $this->hasMany(\App\Models\DescargaBotadero::class);
    }

    public function descargaActiva()
    {
        return $this->hasOne(\App\Models\DescargaBotadero::class)
                    ->where('estado', 'en_descarga')
                    ->latest('id');
    }

    /**
     * Verifica si el recorrido tiene una descarga activa
     */
    public function tieneDescargaActiva(): bool
    {
        return $this->descargas()->where('estado', 'en_descarga')->exists();
    }

    /**
     * Obtiene el numero de la siguiente descarga
     */
    public function getSiguienteNumeroDescarga(): int
    {
        return ($this->descargas()->max('numero_descarga') ?? 0) + 1;
    }

}
