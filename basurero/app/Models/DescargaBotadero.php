<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DescargaBotadero extends Model
{
    protected $table = 'descargas_botadero';

    protected $fillable = [
        'recorrido_id',
        'numero_descarga',
        'estado',
        'lat_inicio',
        'lng_inicio',
        'fecha_inicio',
        'lat_fin',
        'lng_fin',
        'fecha_fin',
        'puntos_durante_descarga',
        'distancia_metros',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'lat_inicio' => 'float',
        'lng_inicio' => 'float',
        'lat_fin' => 'float',
        'lng_fin' => 'float',
        'distancia_metros' => 'float',
    ];

    // ========== RELACIONES ==========

    public function recorrido(): BelongsTo
    {
        return $this->belongsTo(Recorrido::class);
    }

    public function puntos(): HasMany
    {
        return $this->hasMany(PuntoRecorrido::class, 'descarga_id');
    }

    // ========== SCOPES ==========

    public function scopeActiva($query)
    {
        return $query->where('estado', 'en_descarga');
    }

    public function scopeFinalizada($query)
    {
        return $query->where('estado', 'finalizada');
    }

    // ========== METODOS ==========

    /**
     * Verifica si la descarga esta activa
     */
    public function estaActiva(): bool
    {
        return $this->estado === 'en_descarga';
    }

    /**
     * Calcula la duracion en minutos
     */
    public function getDuracionMinutosAttribute(): ?int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return null;
        }
        return $this->fecha_inicio->diffInMinutes($this->fecha_fin);
    }

    /**
     * Formatea la duracion para mostrar
     */
    public function getDuracionFormateadaAttribute(): string
    {
        $minutos = $this->duracion_minutos;
        if ($minutos === null) {
            return 'En curso';
        }

        if ($minutos < 60) {
            return "{$minutos} min";
        }

        $horas = floor($minutos / 60);
        $mins = $minutos % 60;
        return "{$horas}h {$mins}min";
    }
}
