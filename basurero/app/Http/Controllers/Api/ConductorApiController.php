<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use App\Models\Recorrido;
use App\Models\Ruta;
use App\Models\PuntoRecorrido;
use App\Models\EventoRecorrido;
use App\Models\HorarioDia;
use App\Models\Configuracion;
use App\Support\Geo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConductorApiController extends Controller
{
    /**
     * Obtener camiones asignados al conductor
     */
    public function getCamiones(Request $request)
    {
        $user = $request->user();
        
        $camiones = $user->camionesAsignados()
            ->with('rutas')
            ->where('estado', 'activo')
            ->orderBy('placa')
            ->get()
            ->map(function($camion) {
                return [
                    'id' => $camion->id,
                    'placa' => $camion->placa,
                    'codigo' => $camion->codigo,
                    'rutas' => $camion->rutas->map(function($ruta) {
                        return [
                            'id' => $ruta->id,
                            'nombre' => $ruta->nombre,
                        ];
                    }),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $camiones
        ]);
    }

    /**
     * Obtener rutas disponibles para hoy
     */
    public function getRutasHoy(Request $request)
    {
        $user = $request->user();

        $camionId = $request->integer('camion_id');
        $diaHoy = strtolower(now()->locale('es')->dayName);
        $diaHoy = strtr($diaHoy, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
        $horaActual = now()->format('H:i:s');

        $camiones = $user->camionesAsignados()
            ->with('rutas')
            ->where('estado', 'activo')
            ->when($camionId, function ($query, $camionId) {
                $query->where('id', $camionId);
            })
            ->get();

        if ($camionId && $camiones->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El camión seleccionado no está asignado al conductor'
            ], 403);
        }

        $pivotIds = $camiones
            ->flatMap(fn ($camion) => $camion->rutas->pluck('pivot.id'))
            ->filter()
            ->values();

        $horariosHoyPorPivot = HorarioDia::whereIn('ruta_camion_id', $pivotIds)
            ->where('dia', $diaHoy)
            ->where('activo', true)
            ->get()
            ->keyBy('ruta_camion_id');

        $rutas = collect();

        foreach ($camiones as $camion) {
            foreach ($camion->rutas as $ruta) {
                if (!$ruta->pivot->activa || $ruta->estado !== 'activa') {
                    continue;
                }

                $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
                $horarioDia = $horariosHoyPorPivot->get($ruta->pivot->id);
                $programadaHoy = $horarioDia || in_array($diaHoy, $dias);

                if ($programadaHoy) {
                    // Obtener hora_fin efectiva y filtrar rutas ya finalizadas
                    $horaFin = $horarioDia?->hora_fin ?? $ruta->pivot->hora_fin;
                    if ($horaFin && $horaActual > $horaFin) {
                        continue; // La ruta ya terminó su horario
                    }

                    $rutas->push([
                        'id' => $ruta->id,
                        'nombre' => $ruta->nombre,
                        'camion_id' => $camion->id,
                        'camion_placa' => $camion->placa,
                        'horario' => [
                            'inicio' => $horarioDia?->hora_inicio ?? $ruta->pivot->hora_inicio,
                            'fin' => $horaFin,
                        ],
                        'tolerancia' => $ruta->tolerancia_metros,
                        'geometria' => json_decode($ruta->geometria_geojson),
                    ]);
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $rutas->values()
        ]);
    }

    /**
     * Iniciar nuevo recorrido
     */
    public function iniciarRecorrido(Request $request)
    {
        $request->validate([
            'camion_id' => 'required|exists:camiones,id',
            'ruta_id' => 'required|exists:rutas,id',
        ]);

        $user = $request->user();

        // Verificar que el camión pertenezca al conductor
        $camionAsignado = $user->camionesAsignados()
            ->where('id', $request->camion_id)
            ->exists();

        if (!$camionAsignado) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para usar este camión'
            ], 403);
        }

        // Verificar que la ruta esté asignada al camión
        $camion = Camion::find($request->camion_id);
        $rutaAsignada = $camion->rutas()
            ->where('rutas.id', $request->ruta_id)
            ->wherePivot('activa', true)
            ->first();

        if (!$rutaAsignada) {
            return response()->json([
                'success' => false,
                'message' => 'Esta ruta no está asignada al camión'
            ], 400);
        }

        // Verificar si ya tiene un recorrido activo
        $yaActivo = Recorrido::where('conductor_id', $user->id)
            ->where('estado', 'en_curso')
            ->exists();

        if ($yaActivo) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes un recorrido en curso'
            ], 400);
        }

        try {
            $recorrido = Recorrido::create([
                'camion_id' => $request->camion_id,
                'ruta_id' => $request->ruta_id,
                'conductor_id' => $user->id,
                'estado' => 'en_curso',
                'fecha_inicio' => now(),
                'total_puntos' => 0,
                'eventos_fuera_ruta' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recorrido iniciado',
                'data' => [
                    'recorrido_id' => $recorrido->id,
                    'horario' => [
                        'inicio' => $rutaAsignada->pivot->hora_inicio,
                        'fin' => $rutaAsignada->pivot->hora_fin,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error iniciando recorrido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar recorrido'
            ], 500);
        }
    }

    /**
     * Obtener todos los puntos GPS del recorrido activo (para recuperar trayectoria al recargar)
     */
    public function getPuntosRecorridoActivo(Request $request)
    {
        $user = $request->user();

        $recorrido = Recorrido::where('conductor_id', $user->id)
            ->where('estado', 'en_curso')
            ->latest('id')
            ->first();

        if (!$recorrido) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $puntos = PuntoRecorrido::where('recorrido_id', $recorrido->id)
            ->orderBy('fecha_gps')
            ->get(['lat', 'lng', 'fecha_gps'])
            ->map(fn($p) => [
                'lat' => (float)$p->lat,
                'lng' => (float)$p->lng,
            ]);

        return response()->json([
            'success' => true,
            'recorrido_id' => $recorrido->id,
            'total' => $puntos->count(),
            'data' => $puntos,
        ]);
    }

    /**
     * Obtener recorrido activo del conductor
     */
    public function getRecorridoActivo(Request $request)
    {
        $user = $request->user();

        $recorrido = Recorrido::with(['camion', 'ruta'])
            ->where('conductor_id', $user->id)
            ->where('estado', 'en_curso')
            ->latest('id')
            ->first();

        if (!$recorrido) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        $geometria = $recorrido->ruta?->geometria_geojson
            ? json_decode($recorrido->ruta->geometria_geojson, true)
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $recorrido->id,
                'camion_id' => $recorrido->camion_id,
                'ruta_id' => $recorrido->ruta_id,
                'camion' => $recorrido->camion?->placa,
                'ruta' => $recorrido->ruta?->nombre,
                'fecha_inicio' => optional($recorrido->fecha_inicio)->toIso8601String(),
                'geometria' => $geometria,
            ],
        ]);
    }

    /**
     * Finalizar recorrido
     */
    public function finalizarRecorrido(Request $request)
    {
        $user = $request->user();
        
        $recorrido = Recorrido::where('conductor_id', $user->id)
            ->where('estado', 'en_curso')
            ->latest('id')
            ->first();

        if (!$recorrido) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes recorrido en curso'
            ], 400);
        }

        try {
            $recorrido->update([
                'estado' => 'finalizado',
                'fecha_fin' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recorrido finalizado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error finalizando recorrido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar recorrido'
            ], 500);
        }
    }

    /**
     * Obtener geometría de una ruta específica
     */
    public function getRuta(Ruta $ruta)
    {
        return response()->json([
            'id' => $ruta->id,
            'nombre' => $ruta->nombre,
            'geometria' => json_decode($ruta->geometria_geojson),
            'tolerancia_metros' => $ruta->tolerancia_metros,
        ]);
    }

    /**
     * Guardar punto GPS (puedes reutilizar el de ConductorGpsController)
     */
    public function guardarGps(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'fecha_gps' => 'required|date',
        ]);

        $user = $request->user();
        
        $recorrido = Recorrido::where('conductor_id', $user->id)
            ->where('estado', 'en_curso')
            ->latest('id')
            ->first();

        if (!$recorrido) {
            return response()->json([
                'success' => false,
                'message' => 'No hay recorrido en curso'
            ], 409);
        }

        try {
            // Verificar distancia mínima configurable desde el último punto
            $distanciaMinima = (float) Configuracion::obtener('gps_distancia_minima_metros', '10');
            $ultimoPunto = PuntoRecorrido::where('recorrido_id', $recorrido->id)
                ->latest('id')
                ->first();

            if ($ultimoPunto) {
                $distDesdeUltimo = Geo::haversine(
                    (float)$ultimoPunto->lat,
                    (float)$ultimoPunto->lng,
                    (float)$request->lat,
                    (float)$request->lng
                );
                if ($distDesdeUltimo < $distanciaMinima) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Punto omitido (distancia insuficiente)',
                        'distancia' => round($distDesdeUltimo, 1),
                    ]);
                }
            }

            PuntoRecorrido::create([
                'recorrido_id' => $recorrido->id,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'precision_m' => $request->precision_m,
                'velocidad_mps' => $request->velocidad_mps,
                'rumbo_grados' => $request->rumbo_grados,
                'fecha_gps' => $request->fecha_gps,
            ]);

            $recorrido->increment('total_puntos');

            // ── Verificar si está fuera de ruta ──
            try {
                $ruta = $recorrido->ruta;
                if ($ruta && $ruta->geometria_geojson) {
                    $geo = json_decode($ruta->geometria_geojson, true);

                    // Normalizar Feature/FeatureCollection → geometry
                    if (isset($geo['type']) && $geo['type'] === 'Feature') {
                        $geo = $geo['geometry'] ?? [];
                    } elseif (isset($geo['type']) && $geo['type'] === 'FeatureCollection') {
                        $geo = $geo['features'][0]['geometry'] ?? [];
                    }

                    // Extraer coordenadas planas [[lng, lat]]
                    $coords = [];
                    if (isset($geo['type']) && $geo['type'] === 'MultiLineString') {
                        $coords = array_merge(...$geo['coordinates']);
                    } elseif (!empty($geo['coordinates'])) {
                        $coords = $geo['coordinates'];
                    }

                    if (count($coords) >= 2) {
                        // Convertir [lng, lat] → [lat, lng] para Geo
                        $lineLatLngs = array_map(fn($c) => [(float)$c[1], (float)$c[0]], $coords);
                        $distancia   = Geo::pointToPolylineMeters(
                            (float)$request->lat,
                            (float)$request->lng,
                            $lineLatLngs
                        );
                        $tolerancia = $ruta->tolerancia_metros ?? 50;

                        if ($distancia > $tolerancia) {
                            // Crear evento solo si no hay uno reciente (evitar spam)
                            $hayReciente = EventoRecorrido::where('recorrido_id', $recorrido->id)
                                ->where('tipo', 'fuera_ruta')
                                ->where('fecha_evento', '>=', now()->subSeconds(60))
                                ->exists();

                            if (!$hayReciente) {
                                EventoRecorrido::create([
                                    'recorrido_id' => $recorrido->id,
                                    'tipo'         => 'fuera_ruta',
                                    'mensaje'      => 'Desviación de ' . round($distancia) . 'm (tolerancia: ' . $tolerancia . 'm)',
                                    'lat'          => $request->lat,
                                    'lng'          => $request->lng,
                                    'distancia_m'  => round($distancia),
                                    'fecha_evento' => now(),
                                ]);
                                $recorrido->increment('eventos_fuera_ruta');
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Verificación fuera de ruta: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Punto guardado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error guardando GPS: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error guardando punto'
            ], 500);
        }
    }

    /**
     * Obtener configuraciones del sistema
     */
    public function getConfiguraciones()
    {
        $configs = Configuracion::all()->mapWithKeys(fn($c) => [$c->clave => $c->valor]);
        return response()->json(['success' => true, 'data' => $configs]);
    }

    /**
     * Actualizar una configuración (solo admin)
     */
    public function updateConfiguracion(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:100',
            'valor' => 'required|string',
        ]);

        $config = Configuracion::where('clave', $request->clave)->first();
        if (!$config) {
            return response()->json(['success' => false, 'message' => 'Configuración no encontrada'], 404);
        }

        $config->update(['valor' => $request->valor]);

        return response()->json(['success' => true, 'message' => 'Configuración actualizada']);
    }
}