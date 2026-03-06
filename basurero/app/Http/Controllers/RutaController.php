<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::with('camiones')->orderBy('id', 'desc')->get();
        return view('rutas.index', compact('rutas'));
    }

    public function create()
    {
        return view('rutas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:150',
            'estado' => 'required|in:activa,inactiva',
            'tolerancia_metros' => 'required|integer|min:5|max:500',
            'geometria_geojson' => 'required',
        ]);

        try {
            DB::beginTransaction();

            Ruta::create([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
                'tolerancia_metros' => $request->tolerancia_metros,
                'geometria_geojson' => $request->geometria_geojson,
            ]);

            DB::commit();

            return redirect()->route('rutas.index')
                ->with('success', '✅ Ruta creada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando ruta: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al crear la ruta.')
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de una ruta (para el modal)
     */
    public function show($id)
{
    $ruta = Ruta::withCount('camiones')->findOrFail($id);

    // Si es una petición AJAX (desde el botón Ver)
    if (request()->wantsJson()) {
        return response()->json([
            'id' => $ruta->id,
            'nombre' => $ruta->nombre,
            'estado' => $ruta->estado,
            'tolerancia_metros' => $ruta->tolerancia_metros,
            'camiones_count' => $ruta->camiones_count,
            'geometria_geojson' => $ruta->geometria_geojson,
            'created_at' => $ruta->created_at->format('d/m/Y H:i'),
        ]);
    }

    // Si es una petición normal, mostrar vista completa
    return view('rutas.show', compact('ruta'));
}

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $ruta = Ruta::findOrFail($id);
        return view('rutas.edit', compact('ruta'));
    }

    /**
     * Actualizar ruta
     */
    public function update(Request $request, $id)
    {
        $ruta = Ruta::findOrFail($id);

        $request->validate([
            'nombre' => 'required|max:150',
            'estado' => 'required|in:activa,inactiva',
            'tolerancia_metros' => 'required|integer|min:5|max:500',
            'geometria_geojson' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $ruta->update([
                'nombre' => $request->nombre,
                'estado' => $request->estado,
                'tolerancia_metros' => $request->tolerancia_metros,
                'geometria_geojson' => $request->geometria_geojson,
            ]);

            DB::commit();

            return redirect()->route('rutas.index')
                ->with('success', '✅ Ruta actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando ruta: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al actualizar la ruta.')
                ->withInput();
        }
    }

    /**
     * Eliminar ruta
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $ruta = Ruta::findOrFail($id);

            // Verificar si tiene camiones asignados
            if ($ruta->camiones()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ No se puede eliminar la ruta porque tiene camiones asignados.'
                ], 422);
            }

            $ruta->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Ruta eliminada correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando ruta: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al eliminar la ruta.'
            ], 500);
        }
    }
}