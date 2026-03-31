<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::with('camiones')->orderBy('id', 'desc')->get();

        // Obtener configuración del botadero global
        $botadero = [
            'lat' => Configuracion::obtener('botadero_lat', ''),
            'lng' => Configuracion::obtener('botadero_lng', ''),
            'nombre' => Configuracion::obtener('botadero_nombre', 'Botadero'),
        ];

        return view('rutas.index', compact('rutas', 'botadero'));
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

    /**
     * Obtener configuración del botadero global.
     */
    public function getBotadero()
    {
        return response()->json([
            'lat' => Configuracion::obtener('botadero_lat', ''),
            'lng' => Configuracion::obtener('botadero_lng', ''),
            'nombre' => Configuracion::obtener('botadero_nombre', 'Botadero'),
        ]);
    }

    /**
     * Guardar configuración del botadero global.
     */
    public function saveBotadero(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'nombre' => 'required|string|max:150',
        ]);

        try {
            DB::beginTransaction();

            // Guardar o actualizar cada configuración
            Configuracion::updateOrCreate(
                ['clave' => 'botadero_lat'],
                ['valor' => $request->lat, 'descripcion' => 'Latitud del botadero']
            );

            Configuracion::updateOrCreate(
                ['clave' => 'botadero_lng'],
                ['valor' => $request->lng, 'descripcion' => 'Longitud del botadero']
            );

            Configuracion::updateOrCreate(
                ['clave' => 'botadero_nombre'],
                ['valor' => $request->nombre, 'descripcion' => 'Nombre del botadero']
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Ubicación del botadero guardada correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error guardando botadero: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '❌ Error al guardar la ubicación del botadero.'
            ], 500);
        }
    }

    /**
     * Eliminar configuración del botadero global.
     */
    public function deleteBotadero(Request $request)
    {
        try {
            DB::beginTransaction();

            // Eliminar las configuraciones del botadero
            Configuracion::where('clave', 'botadero_lat')->delete();
            Configuracion::where('clave', 'botadero_lng')->delete();
            Configuracion::where('clave', 'botadero_nombre')->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Botadero eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando botadero: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => '❌ Error al eliminar el botadero.'
            ], 500);
        }
    }
}