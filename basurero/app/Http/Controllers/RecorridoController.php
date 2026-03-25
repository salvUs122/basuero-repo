<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;
use App\Models\DescargaBotadero;
use App\Models\PuntoRecorrido;

class RecorridoController extends Controller
{
    public function show(Recorrido $recorrido)
    {
        $recorrido->load(['ruta', 'descargas' => fn($q) => $q->orderBy('numero_descarga')]);
        return view('recorridos.show', compact('recorrido'));
    }

    public function puntos(Recorrido $recorrido)
    {
        return $recorrido->puntos()
            ->orderBy('fecha_gps')
            ->get(['lat','lng','fecha_gps']);
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
