<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Ruta;
use App\Models\Recorrido;
use Illuminate\Http\Request;

class EncargadoDashboardController extends Controller
{
    public function index()
    {
        $totalCamiones = Camion::count();
        $camionesActivos = Camion::where('estado', 'activo')->count();
        $totalRutas = Ruta::where('estado', 'activa')->count();
        $recorridosHoy = Recorrido::whereDate('fecha_inicio', today())->count();
        $recorridosActivos = Recorrido::where('estado', 'en_curso')->count();

        return view('dashboards.encargado', compact(
            'totalCamiones',
            'camionesActivos',
            'totalRutas',
            'recorridosHoy',
            'recorridosActivos'
        ));
    }
}