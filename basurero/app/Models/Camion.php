<?php

namespace App\Models;

use App\Models\Ruta;
use App\Models\User;
use App\Models\Recorrido;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Camion extends Model
{
    protected $table = 'camiones';

    protected $fillable = [
        'placa',
        'codigo',
        'estado',
        'conductor_id',  // ✅ NUEVO CAMPO
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el conductor asignado
     */
    public function conductorAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conductor_id');
    }

    /**
     * Relación con Rutas (muchos a muchos)
     */
    public function rutas(): BelongsToMany
    {
        return $this->belongsToMany(Ruta::class, 'ruta_camion')
            ->withPivot(['id', 'activa', 'hora_inicio', 'hora_fin', 'dias_semana'])
            ->withTimestamps();
    }

    /**
     * Relación con Recorridos (uno a muchos)
     */
    public function recorridos(): HasMany
    {
        return $this->hasMany(Recorrido::class);
    }

    /**
     * Obtener las rutas activas para HOY
     */
    public function getRutasActivasHoy()
    {
        $diaHoy = strtolower(now()->locale('es')->dayName);
        
        return $this->rutas()
            ->wherePivot('activa', true)
            ->get()
            ->filter(function($ruta) use ($diaHoy) {
                $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
                return in_array($diaHoy, $dias);
            });
    }

    /**
     * Obtener horario para hoy
     */
    public function getHorarioHoy($rutaId)
    {
        $ruta = $this->rutas()->where('rutas.id', $rutaId)->first();
        
        if (!$ruta) {
            return null;
        }

        $diaHoy = strtolower(now()->locale('es')->dayName);
        $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
        
        if (!in_array($diaHoy, $dias)) {
            return null;
        }

        return [
            'inicio' => $ruta->pivot->hora_inicio,
            'fin' => $ruta->pivot->hora_fin,
        ];
    }
}