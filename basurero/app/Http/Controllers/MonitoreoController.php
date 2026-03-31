<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;
use App\Models\PuntoRecorrido;
use App\Models\EventoRecorrido;
use Illuminate\Http\Request;

class MonitoreoController extends Controller
{
    public function index()
    {
        $recorridosActivos = Recorrido::with(['ruta', 'camion', 'conductor'])
            ->where('estado', 'en_curso')
            ->latest('id')
            ->get();

        $recorridosJs = $recorridosActivos->map(function ($r) {
            return [
                'id' => $r->id,
                'ruta_geojson' => optional($r->ruta)->geometria_geojson,
                'tolerancia' => optional($r->ruta)->tolerancia_metros,
            ];
        })->values();

        return view('monitoreo.index', compact('recorridosActivos', 'recorridosJs'));
    }


    public function puntos(Recorrido $recorrido)
    {
        // Devuelve puntos del recorrido en orden con tiempo detenido calculado
        $puntos = PuntoRecorrido::where('recorrido_id', $recorrido->id)
            ->orderBy('fecha_gps')
            ->get();

        if ($puntos->count() < 2) {
            return response()->json($puntos->map(fn($p) => array_merge($p->toArray(), ['tiempo_detenido' => 0])));
        }

        $radioParada = 20; // metros
        $resultado = [];

        for ($i = 0; $i < $puntos->count(); $i++) {
            $punto = $puntos[$i];
            $tiempoDetenido = 0;

            if ($i < $puntos->count() - 1) {
                $puntoSiguiente = $puntos[$i + 1];
                $fechaActual = \Carbon\Carbon::parse($punto->fecha_gps);
                $fechaSiguiente = \Carbon\Carbon::parse($puntoSiguiente->fecha_gps);
                
                $distancia = $this->haversine(
                    (float)$punto->lat, (float)$punto->lng,
                    (float)$puntoSiguiente->lat, (float)$puntoSiguiente->lng
                );

                if ($distancia <= $radioParada) {
                    $tiempoDetenido = $fechaActual->diffInSeconds($fechaSiguiente);
                }
            }

            $puntoArray = $punto->toArray();
            $puntoArray['tiempo_detenido'] = $tiempoDetenido;
            $resultado[] = $puntoArray;
        }

        return response()->json($resultado);
    }

    /**
     * Calcular distancia entre dos puntos usando fórmula Haversine
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // metros
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function eventos(Recorrido $recorrido)
    {
        $eventos = EventoRecorrido::where('recorrido_id', $recorrido->id)
            ->orderBy('id', 'desc')
            ->take(50)
            ->get(['id','tipo','mensaje','lat','lng','distancia_m','fecha_evento']);

        return response()->json($eventos);
    }

    // Agrega este método a MonitoreoController:

    public function puntosActivos()
    {
        // Obtener todos los recorridos activos
        $recorridosActivos = Recorrido::with(['ruta', 'camion', 'conductor'])
            ->where('estado', 'en_curso')
            ->get();

        $puntosPorRecorrido = [];
        
        foreach ($recorridosActivos as $recorrido) {
            // Obtener últimos 100 puntos del recorrido (más para ver la ruta completa)
            $puntosRaw = PuntoRecorrido::where('recorrido_id', $recorrido->id)
                ->orderBy('fecha_gps', 'asc')
                ->take(100)
                ->get(['lat', 'lng', 'fecha_gps', 'velocidad_mps']);

            // Formatear fecha_gps como ISO 8601 con timezone (-04:00 Bolivia)
            $puntos = $puntosRaw->map(function ($p) {
                return [
                    'lat'           => $p->lat,
                    'lng'           => $p->lng,
                    'velocidad_mps' => $p->velocidad_mps,
                    'fecha_gps'     => $p->fecha_gps
                        ? \Carbon\Carbon::parse($p->fecha_gps)->toIso8601String()
                        : null,
                ];
            });

            // Obtener ruta planificada completa
            $rutaGeojson = null;
            $rutaCoords = [];
            
            if ($recorrido->ruta && $recorrido->ruta->geometria_geojson) {
                $rutaGeojson = $recorrido->ruta->geometria_geojson;
                
                // Parsear coordenadas para fácil acceso
                try {
                    $geo = json_decode($rutaGeojson, true);

                    // Normalizar: Feature → coger geometry
                    if (isset($geo['type']) && $geo['type'] === 'Feature') {
                        $geo = $geo['geometry'] ?? [];
                    } elseif (isset($geo['type']) && $geo['type'] === 'FeatureCollection') {
                        // Tomar la primera feature
                        $geo = $geo['features'][0]['geometry'] ?? [];
                    }

                    if (isset($geo['type']) && $geo['type'] === 'MultiLineString') {
                        // Aplanar todos los segmentos en un solo array
                        $rutaCoords = array_merge(...$geo['coordinates']);
                    } elseif (isset($geo['coordinates'])) {
                        $rutaCoords = $geo['coordinates'];
                    }
                } catch (\Exception $e) {
                    // Ignorar error
                }
            }
            
            $puntosPorRecorrido[] = [
                'recorrido_id' => $recorrido->id,
                'camion' => $recorrido->camion->placa ?? 'N/A',
                'conductor' => $recorrido->conductor->name ?? 'N/A',
                'ruta' => $recorrido->ruta->nombre ?? 'Sin ruta',
                'ruta_geojson' => $rutaGeojson,
                'ruta_coords' => $rutaCoords, // Nuevo: coordenadas parseadas
                'tolerancia' => $recorrido->ruta->tolerancia_metros ?? 50,
                'puntos' => $puntos->values(),
                'ultimo_punto' => $puntos->last(),
                'total_puntos' => $recorrido->total_puntos,
                'eventos_fuera_ruta' => $recorrido->eventos_fuera_ruta
            ];
        }

        return response()->json($puntosPorRecorrido);
    }
    }

