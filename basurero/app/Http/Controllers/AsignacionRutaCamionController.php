<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Ruta;
use App\Models\RutaCamion;
use App\Models\HorarioDia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AsignacionRutaCamionController extends Controller
{
    public function edit(Camion $camion)
    {
        $rutas = Ruta::where('estado', 'activa')->orderBy('nombre')->get();
        
        // Obtener rutas asignadas con sus datos del pivot
        $rutasAsignadas = $camion->rutas()->get()->keyBy('id');
        $asignadasConHorario = [];
        
        foreach ($rutasAsignadas as $ruta) {
            // Obtener horarios por día de la nueva tabla
            $horariosPorDia = HorarioDia::where('ruta_camion_id', $ruta->pivot->id)
                ->get()
                ->keyBy('dia')
                ->toArray();
            
            $asignadasConHorario[$ruta->id] = [
                'pivot_id' => $ruta->pivot->id,
                'activa' => $ruta->pivot->activa ?? true,
                'hora_inicio' => $ruta->pivot->hora_inicio ?? '08:00',
                'hora_fin' => $ruta->pivot->hora_fin ?? '17:00',
                'dias_semana' => json_decode($ruta->pivot->dias_semana ?? '["lunes","martes","miercoles","jueves","viernes"]', true),
                'horarios_por_dia' => $horariosPorDia // NUEVO: horarios específicos
            ];
        }

        return view('camiones.asignar_rutas', compact('camion', 'rutas', 'asignadasConHorario'));
    }

    public function update(Request $request, Camion $camion)
    {
        $request->validate([
            'rutas' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();
            
            $syncData = [];
            
            if ($request->has('rutas') && is_array($request->rutas)) {
                foreach ($request->rutas as $rutaId => $data) {
                    if (isset($data['activa']) && $data['activa'] == '1') {
                        $diasSemana = isset($data['dias_semana']) && is_array($data['dias_semana']) 
                            ? $data['dias_semana'] 
                            : [];
                        
                        $syncData[$rutaId] = [
                            'activa' => true,
                            'hora_inicio' => $data['hora_inicio'] ?? '08:00',
                            'hora_fin' => $data['hora_fin'] ?? '17:00',
                            'dias_semana' => json_encode($diasSemana),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Sincronizar rutas
            $camion->rutas()->sync($syncData);
            
            // Guardar horarios por día (si se enviaron)
            if ($request->has('horarios_por_dia')) {
                foreach ($request->horarios_por_dia as $pivotId => $horarios) {
                    // Eliminar horarios existentes
                    HorarioDia::where('ruta_camion_id', $pivotId)->delete();
                    
                    // Crear nuevos horarios
                    foreach ($horarios as $dia => $horario) {
                        if (isset($horario['activo']) && $horario['activo']) {
                            HorarioDia::create([
                                'ruta_camion_id' => $pivotId,
                                'dia' => $dia,
                                'hora_inicio' => $horario['hora_inicio'] ?? null,
                                'hora_fin' => $horario['hora_fin'] ?? null,
                                'activo' => true
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('camiones.index')
                ->with('success', '✅ Rutas y horarios actualizados correctamente para el camión ' . $camion->placa);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error asignando rutas: ' . $e->getMessage());
            
            return back()->with('error', '❌ Error al asignar rutas: ' . $e->getMessage())
                ->withInput();
        }
    }

    // NUEVO: Método para guardar horarios por día desde el modal
    public function guardarHorariosPorDia(Request $request, Camion $camion)
    {
        $request->validate([
            'pivot_id' => 'required|exists:ruta_camion,id',
            'horarios' => 'required|array'
        ]);

        try {
            DB::beginTransaction();
            
            // Eliminar horarios existentes
            HorarioDia::where('ruta_camion_id', $request->pivot_id)->delete();
            
            // Crear nuevos horarios
            foreach ($request->horarios as $dia => $horario) {
                if (isset($horario['activo']) && $horario['activo']) {
                    HorarioDia::create([
                        'ruta_camion_id' => $request->pivot_id,
                        'dia' => $dia,
                        'hora_inicio' => $horario['hora_inicio'] ?? null,
                        'hora_fin' => $horario['hora_fin'] ?? null,
                        'activo' => true
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Horarios por día guardados correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error guardando horarios por día: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al guardar horarios: ' . $e->getMessage()
            ], 500);
        }
    }
}