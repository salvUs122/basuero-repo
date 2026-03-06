<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;

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
        $recorrido->load(['ruta','camion','conductor']);
        return view('recorridos.show', compact('recorrido')); // usa tu show.blade existente
    }
}
