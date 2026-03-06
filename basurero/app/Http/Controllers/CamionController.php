<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\HorarioDia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CamionController extends Controller
{
    public function index()
    {
        $camiones = Camion::with(['rutas', 'conductorAsignado'])->orderBy('id', 'desc')->get();

        $pivotIds = $camiones
            ->flatMap(fn($camion) => $camion->rutas->pluck('pivot.id'))
            ->filter()
            ->values();

        $horariosPorPivot = HorarioDia::whereIn('ruta_camion_id', $pivotIds)
            ->where('activo', true)
            ->orderBy('dia')
            ->get()
            ->groupBy('ruta_camion_id');

        return view('camiones.index', compact('camiones', 'horariosPorPivot'));
    }

    public function create()
    {
        return view('camiones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:20', 'unique:camiones,placa'],
            'codigo' => ['required', 'string', 'max:50', 'unique:camiones,codigo'],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
        ]);

        try {
            DB::beginTransaction();
            
            Camion::create($validated);
            
            DB::commit();
            
            return redirect()->route('camiones.index')
                ->with('success', '✅ Camión creado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando camión: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al crear el camión. Por favor intenta de nuevo.')
                ->withInput();
        }
    }

    public function edit(Camion $camion)
    {
        return view('camiones.edit', compact('camion'));
    }

    public function update(Request $request, Camion $camion)
    {
        $validated = $request->validate([
            'placa' => ['required', 'string', 'max:20', Rule::unique('camiones')->ignore($camion->id)],
            'codigo' => ['required', 'string', 'max:50', Rule::unique('camiones')->ignore($camion->id)],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'mantenimiento'])],
        ]);

        try {
            DB::beginTransaction();
            
            $camion->update($validated);
            
            DB::commit();
            
            return redirect()->route('camiones.index')
                ->with('success', '✅ Camión actualizado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando camión: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al actualizar el camión. Por favor intenta de nuevo.')
                ->withInput();
        }
    }

    public function destroy(Camion $camion)
    {
        try {
            DB::beginTransaction();
            
            // 1. Verificar recorridos EN CURSO (NO SE PUEDEN ELIMINAR)
            $recorridosActivos = $camion->recorridos()
                ->where('estado', 'en_curso')
                ->exists();
                
            if ($recorridosActivos) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '❌ No se puede eliminar el camión porque tiene RECORRIDOS ACTIVOS. Finaliza los recorridos primero.'
                ], 422);
            }
            
            // 2. ELIMINAR EL CAMIÓN
            // ✅ Con CASCADE en la BD, se eliminarán automáticamente:
            //    - Todos sus recorridos (finalizados)
            //    - Todos sus puntos de recorrido
            //    - Todos sus eventos
            //    - Todas sus asignaciones de rutas
            $camion->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Camión eliminado correctamente. Todos sus datos han sido eliminados.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando camión ID ' . $camion->id . ': ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al eliminar el camión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCamion(Camion $camion)
    {
        try {
            $camion->load('rutas');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $camion->id,
                    'placa' => $camion->placa,
                    'codigo' => $camion->codigo,
                    'estado' => $camion->estado,
                    'rutas' => $camion->rutas->map(function($ruta) {
                        return [
                            'id' => $ruta->id,
                            'nombre' => $ruta->nombre,
                            'pivot' => [
                                'id' => $ruta->pivot->id,
                                'activa' => (bool)$ruta->pivot->activa,
                                'hora_inicio' => $ruta->pivot->hora_inicio,
                                'hora_fin' => $ruta->pivot->hora_fin,
                                'dias_semana' => json_decode($ruta->pivot->dias_semana ?? '[]', true)
                            ]
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo camión: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del camión'
            ], 500);
        }
    }
}