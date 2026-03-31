<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;
use App\Models\Configuracion;

class RecorridosAdminController extends Controller
{
    public function index()
    {
        $recorridos = Recorrido::with(['ruta','camion','conductor'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('recorridos.index', compact('recorridos'));
    }

    public function show(Recorrido $recorrido)
    {
        $recorrido->load(['ruta','camion','conductor','descargas']);
        
        // Obtener configuración del botadero
        $botadero = [
            'lat' => Configuracion::obtener('botadero_lat'),
            'lng' => Configuracion::obtener('botadero_lng'),
            'nombre' => Configuracion::obtener('botadero_nombre', 'Botadero Municipal'),
        ];
        
        return view('recorridos.show', compact('recorrido', 'botadero'));
    }
}
