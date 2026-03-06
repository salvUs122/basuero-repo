<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Recorrido;
use App\Models\PuntoRecorrido;
use App\Models\User;
use App\Models\Ruta;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Obtener camiones activos
        $camionesActivos = Camion::where('estado', 'activo')->count();
        $totalCamiones = Camion::count();
        
        // Obtener recorridos en curso con relaciones
        $recorridosEnCurso = Recorrido::with(['ruta', 'camion', 'conductor'])
            ->where('estado', 'en_curso')
            ->orderByDesc('id')
            ->get();
        
        // Obtener último punto de cada recorrido
        $ultimos = [];
        foreach ($recorridosEnCurso as $recorrido) {
            $ultimos[$recorrido->id] = PuntoRecorrido::where('recorrido_id', $recorrido->id)
                ->orderByDesc('id')
                ->first();
        }
        
        // Determinar estados
        $estados = [];
        $alertasActivas = 0;
        
        foreach ($recorridosEnCurso as $recorrido) {
            $punto = $ultimos[$recorrido->id] ?? null;
            
            if (!$punto) {
                $estados[$recorrido->id] = ['label' => 'SIN GPS', 'color' => '#f59e0b'];
                $alertasActivas++;
                continue;
            }
            
            // Verificar si el último punto es viejo (> 30 segundos)
            $segundos = now()->diffInSeconds($punto->fecha_gps);
            if ($segundos > 30) {
                $estados[$recorrido->id] = ['label' => 'GPS ATRASADO', 'color' => '#f59e0b'];
                $alertasActivas++;
                continue;
            }
            
            // Verificar si hay eventos fuera de ruta
            if (($recorrido->eventos_fuera_ruta ?? 0) > 0) {
                $estados[$recorrido->id] = ['label' => 'FUERA DE RUTA', 'color' => '#ef4444'];
                $alertasActivas++;
            } else {
                $estados[$recorrido->id] = ['label' => 'OK', 'color' => '#22c55e'];
            }
        }
        
        // Estadísticas adicionales
        $totalConductores = User::role('conductor')->count();
        $recorridosHoy = Recorrido::whereDate('created_at', Carbon::today())->count();
        $totalRutas = Ruta::where('estado', 'activa')->count();
        $recorridosActivosCount = $recorridosEnCurso->count();
        
        // Pasar datos a la vista
        return view('dashboards.admin', compact(
            'camionesActivos',
            'totalCamiones',
            'recorridosEnCurso',
            'ultimos',
            'estados',
            'alertasActivas',
            'totalConductores',
            'recorridosHoy',
            'totalRutas',
            'recorridosActivosCount'
        ));
    }
}