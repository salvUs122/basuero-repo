<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsignarConductorController extends Controller
{
    /**
     * Mostrar formulario para asignar conductor a camión
     */
    public function edit(Camion $camion)
    {
        $conductores = User::whereHas('roles', function($q) {
            $q->where('name', 'conductor');
        })->orderBy('name')->get();

        return view('admin.camiones.asignar_conductor', compact('camion', 'conductores'));
    }

    /**
     * Actualizar asignación de conductor
     */
    public function update(Request $request, Camion $camion)
    {
        $request->validate([
            'conductor_id' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $camion->update([
                'conductor_id' => $request->conductor_id,
            ]);

            DB::commit();

            return redirect()->route('camiones.index')
                ->with('success', '✅ Conductor asignado correctamente al camión ' . $camion->placa);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error asignando conductor: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al asignar conductor')
                ->withInput();
        }
    }

    /**
     * Obtener camiones asignados a un conductor (para API)
     */
    public function getCamionesConductor(User $conductor)
    {
        $camiones = $conductor->camionesAsignados()
            ->with('rutas')
            ->where('estado', 'activo')
            ->get();

        return response()->json($camiones);
    }

    /**
     * Obtener rutas disponibles para un camión en el día de hoy
     */
    public function getRutasHoy(Camion $camion)
    {
        $diaHoy = strtolower(now()->locale('es')->dayName);
        
        $rutas = $camion->rutas()
            ->wherePivot('activa', true)
            ->get()
            ->filter(function($ruta) use ($diaHoy) {
                $dias = json_decode($ruta->pivot->dias_semana ?? '[]', true);
                return in_array($diaHoy, $dias);
            })
            ->map(function($ruta) use ($camion) {
                return [
                    'id' => $ruta->id,
                    'nombre' => $ruta->nombre,
                    'horario' => [
                        'inicio' => $ruta->pivot->hora_inicio,
                        'fin' => $ruta->pivot->hora_fin,
                    ]
                ];
            });

        return response()->json($rutas);
    }
}