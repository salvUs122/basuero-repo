<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaCamion extends Model
{
    protected $table = 'ruta_camion';

    protected $fillable = [
        'ruta_id',
        'camion_id',
        'activa',
        'hora_inicio',
        'hora_fin',
        'dias_semana'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'dias_semana' => 'array',
        'hora_inicio' => 'string',
        'hora_fin' => 'string'
    ];

    /**
     * Relación con horarios por día
     */
    public function horariosDias(): HasMany
    {
        return $this->hasMany(HorarioDia::class, 'ruta_camion_id');
    }

    /**
     * Relación con camión
     */
    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class);
    }

    /**
     * Relación con ruta
     */
    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }
}