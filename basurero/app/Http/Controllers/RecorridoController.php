<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;
use App\Models\DescargaBotadero;
use App\Models\PuntoRecorrido;
use App\Models\Configuracion;

class RecorridoController extends Controller
{
    public function show(Recorrido $recorrido)
    {
        $recorrido->load(['ruta', 'descargas' => fn($q) => $q->orderBy('numero_descarga')]);
        
        // Obtener configuración del botadero global
        $botadero = [
            'lat' => Configuracion::obtener('botadero_lat', ''),
            'lng' => Configuracion::obtener('botadero_lng', ''),
            'nombre' => Configuracion::obtener('botadero_nombre', 'Botadero'),
        ];
        
        return view('recorridos.show', compact('recorrido', 'botadero'));
    }

    public function puntos(Recorrido $recorrido)
    {
        $puntos = $recorrido->puntos()
            ->orderBy('fecha_gps')
            ->get(['lat', 'lng', 'fecha_gps', 'velocidad_mps', 'fuera_ruta']);

        if ($puntos->count() < 2) {
            return response()->json($puntos->map(fn($p) => array_merge($p->toArray(), ['tiempo_detenido' => 0])));
        }

        $radioParada = 20; // metros
        $resultado = [];

        for ($i = 0; $i < $puntos->count(); $i++) {
            $punto = $puntos[$i];
            $tiempoDetenido = 0;

            // Calcular tiempo hasta el siguiente punto
            if ($i < $puntos->count() - 1) {
                $puntoSiguiente = $puntos[$i + 1];
                $fechaActual = \Carbon\Carbon::parse($punto->fecha_gps);
                $fechaSiguiente = \Carbon\Carbon::parse($puntoSiguiente->fecha_gps);
                
                $distancia = $this->haversine(
                    (float)$punto->lat, (float)$punto->lng,
                    (float)$puntoSiguiente->lat, (float)$puntoSiguiente->lng
                );

                // Si el siguiente punto está cerca (< 20m), el camión estuvo detenido
                if ($distancia <= $radioParada) {
                    $tiempoDetenido = $fechaActual->diffInSeconds($fechaSiguiente);
                }
            }

            $resultado[] = [
                'lat' => $punto->lat,
                'lng' => $punto->lng,
                'fecha_gps' => $punto->fecha_gps,
                'velocidad_mps' => $punto->velocidad_mps,
                'fuera_ruta' => $punto->fuera_ruta,
                'tiempo_detenido' => $tiempoDetenido,
            ];
        }

        return response()->json($resultado);
    }

    /**
     * Obtener puntos de una descarga específica
     */
    public function puntosDescarga(Recorrido $recorrido, DescargaBotadero $descarga)
    {
        // Verificar que la descarga pertenece al recorrido
        if ($descarga->recorrido_id !== $recorrido->id) {
            abort(404);
        }

        return PuntoRecorrido::where('descarga_id', $descarga->id)
            ->orderBy('fecha_gps')
            ->get(['lat', 'lng', 'fecha_gps']);
    }

    /**
     * Detectar puntos donde el camión se detuvo
     * Una parada es cuando el camión está en el mismo lugar (< 20m) por más de 2 minutos
     */
    public function paradas(Recorrido $recorrido)
    {
        $puntos = $recorrido->puntos()
            ->orderBy('fecha_gps')
            ->get(['lat', 'lng', 'fecha_gps']);

        if ($puntos->count() < 2) {
            return response()->json([]);
        }

        $paradas = [];
        $radioParada = 20; // metros - distancia máxima para considerar mismo lugar
        $tiempoMinimo = 120; // segundos - tiempo mínimo para considerar parada (2 min)

        $i = 0;
        while ($i < $puntos->count()) {
            $puntoInicio = $puntos[$i];
            $latInicio = (float) $puntoInicio->lat;
            $lngInicio = (float) $puntoInicio->lng;
            $fechaInicio = \Carbon\Carbon::parse($puntoInicio->fecha_gps);

            // Buscar puntos consecutivos cercanos
            $j = $i + 1;
            $fechaFin = $fechaInicio;

            while ($j < $puntos->count()) {
                $puntoCurrent = $puntos[$j];
                $latCurrent = (float) $puntoCurrent->lat;
                $lngCurrent = (float) $puntoCurrent->lng;

                // Calcular distancia usando Haversine
                $distancia = $this->haversine($latInicio, $lngInicio, $latCurrent, $lngCurrent);

                if ($distancia <= $radioParada) {
                    // Sigue en el mismo lugar
                    $fechaFin = \Carbon\Carbon::parse($puntoCurrent->fecha_gps);
                    $j++;
                } else {
                    // Se movió
                    break;
                }
            }

            // Calcular tiempo de parada
            $segundosDetenido = $fechaInicio->diffInSeconds($fechaFin);

            if ($segundosDetenido >= $tiempoMinimo) {
                $paradas[] = [
                    'lat' => $latInicio,
                    'lng' => $lngInicio,
                    'inicio' => $fechaInicio->format('H:i:s'),
                    'fin' => $fechaFin->format('H:i:s'),
                    'segundos' => $segundosDetenido,
                    'duracion' => $this->formatearDuracion($segundosDetenido),
                ];
            }

            // Avanzar al siguiente grupo
            $i = $j > $i ? $j : $i + 1;
        }

        return response()->json($paradas);
    }

    /**
     * Calcular distancia entre dos puntos usando fórmula Haversine
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000; // Radio de la Tierra en metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    /**
     * Formatear duración en texto legible
     */
    private function formatearDuracion(int $segundos): string
    {
        if ($segundos < 60) {
            return "{$segundos} seg";
        }

        $minutos = floor($segundos / 60);
        $seg = $segundos % 60;

        if ($minutos < 60) {
            return $seg > 0 ? "{$minutos}min {$seg}s" : "{$minutos}min";
        }

        $horas = floor($minutos / 60);
        $min = $minutos % 60;

        return $min > 0 ? "{$horas}h {$min}min" : "{$horas}h";
    }

    /**
     * EXPORTAR CSV - SOLO RESUMEN
     */
    public function exportarCSV(Recorrido $recorrido)
    {
        $totalPuntos = $recorrido->puntos()->count();
        $puntosFueraRuta = $recorrido->eventos()->where('tipo', 'fuera_ruta')->count();
        
        $filename = "resumen_recorrido_{$recorrido->id}_" . now()->format('Ymd_His') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($recorrido, $totalPuntos, $puntosFueraRuta) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8 (Excel)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // ============================================
            // 1. INFORMACIÓN DEL RECORRIDO (formato solicitado)
            // ============================================
            fputcsv($file, ['RECORRIDO #' . $recorrido->id]);
            fputcsv($file, ['Fecha', $recorrido->fecha_inicio->format('d/m/Y')]);
            fputcsv($file, ['Conductor', $recorrido->conductor?->name ?? 'N/A']);
            fputcsv($file, ['Camión', $recorrido->camion?->placa ?? 'N/A']);
            fputcsv($file, ['Ruta', '"' . ($recorrido->ruta?->nombre ?? 'N/A') . '"']); // Con comillas
            fputcsv($file, []); // Línea en blanco
            
            // ============================================
            // 2. RESUMEN DE PUNTOS
            // ============================================
            fputcsv($file, ['RESUMEN']);
            fputcsv($file, ['Total de puntos registrados', $totalPuntos]);
            fputcsv($file, ['Puntos fuera de ruta', $puntosFueraRuta]);
            fputcsv($file, ['Porcentaje fuera de ruta', $totalPuntos > 0 ? round(($puntosFueraRuta / $totalPuntos) * 100, 1) . '%' : '0%']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

}
