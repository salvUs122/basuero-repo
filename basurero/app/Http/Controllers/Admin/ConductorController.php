<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class ConductorController extends Controller
{
    /**
     * Mostrar lista de conductores
     */
    public function index()
    {
        $conductores = User::whereHas('roles', function($q) {
            $q->where('name', 'conductor');
        })->orderBy('id', 'desc')->get();

        return view('admin.conductores.index', compact('conductores'));
    }

    /**
     * Mostrar formulario para crear conductor
     */
    public function create()
    {
        return view('admin.conductores.create');
    }

    /**
     * Guardar nuevo conductor
     */
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'licencia' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario
            $conductor = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'licencia' => $request->licencia,
                'direccion' => $request->direccion,
                'email_verified_at' => now(),
            ]);

            // Asignar rol de conductor
            $conductor->assignRole('conductor');

            DB::commit();

            return redirect()->route('admin.conductores.index')
                ->with('success', '✅ Conductor creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', '❌ Error al crear conductor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un conductor
     */
    public function show($id)
    {
        $conductor = User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'conductor'))
            ->findOrFail($id);

        return response()->json([
            'id' => $conductor->id,
            'name' => $conductor->name,
            'email' => $conductor->email,
            'telefono' => $conductor->telefono,
            'licencia' => $conductor->licencia,
            'direccion' => $conductor->direccion,
            'created_at' => $conductor->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $conductor = User::whereHas('roles', fn($q) => $q->where('name', 'conductor'))
            ->findOrFail($id);

        return view('admin.conductores.edit', compact('conductor'));
    }

    /**
     * Actualizar conductor
     */
    public function update(Request $request, $id)
    {
        $conductor = User::whereHas('roles', fn($q) => $q->where('name', 'conductor'))
            ->findOrFail($id);

        // Validar datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'licencia' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Datos a actualizar
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'licencia' => $request->licencia,
                'direccion' => $request->direccion,
            ];

            // Si se proporciona nueva contraseña
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $conductor->update($data);

            DB::commit();

            return redirect()->route('admin.conductores.index')
                ->with('success', '✅ Conductor actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', '❌ Error al actualizar conductor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar conductor
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $conductor = User::whereHas('roles', fn($q) => $q->where('name', 'conductor'))
                ->findOrFail($id);

            // Verificar si tiene recorridos activos
            $recorridosActivos = $conductor->recorridos()
                ->where('estado', 'en_curso')
                ->exists();

            if ($recorridosActivos) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ No se puede eliminar el conductor porque tiene recorridos activos.'
                ], 422);
            }

            // Eliminar relaciones
            $conductor->syncRoles([]); // Quitar roles
            
            // Eliminar conductor
            $conductor->delete();

            DB::commit(); 

            return response()->json([
                'success' => true,
                'message' => '✅ Conductor eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ Error al eliminar conductor: ' . $e->getMessage()
            ], 500);
        }
    }
}