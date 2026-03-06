<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioDia extends Model
{
    protected $table = 'horarios_dias';

    protected $fillable = [
        'ruta_camion_id',
        'dia',
        'hora_inicio',
        'hora_fin',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'hora_inicio' => 'string',
        'hora_fin' => 'string'
    ];

    /**
     * Relación con la tabla pivote ruta_camion
     */
    public function rutaCamion(): BelongsTo
    {
        return $this->belongsTo(RutaCamion::class, 'ruta_camion_id');
    }
}