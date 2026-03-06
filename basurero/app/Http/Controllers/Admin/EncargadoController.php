<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EncargadoController extends Controller
{
    /**
     * Mostrar lista de encargados
     */
    public function index()
    {
        $encargados = User::whereHas('roles', function($q) {
            $q->where('name', 'encargado');
        })->orderBy('id', 'desc')->get();

        return view('admin.encargados.index', compact('encargados'));
    }

    /**
     * Mostrar formulario para crear encargado
     */
    public function create()
    {
        return view('admin.encargados.create');
    }

    /**
     * Guardar nuevo encargado
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $encargado = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'email_verified_at' => now(),
            ]);

            $encargado->assignRole('encargado');

            DB::commit();

            return redirect()->route('admin.encargados.index')
                ->with('success', '✅ Encargado creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando encargado: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', '❌ Error al crear encargado.')
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un encargado
     */
    public function show($id)
    {
        $encargado = User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'encargado'))
            ->findOrFail($id);

        return response()->json([
            'id' => $encargado->id,
            'name' => $encargado->name,
            'email' => $encargado->email,
            'telefono' => $encargado->telefono,
            'created_at' => $encargado->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $encargado = User::whereHas('roles', fn($q) => $q->where('name', 'encargado'))
            ->findOrFail($id);

        return view('admin.encargados.edit', compact('encargado'));
    }

    /**
     * Actualizar encargado
     */
    public function update(Request $request, $id)
    {
        $encargado = User::whereHas('roles', fn($q) => $q->where('name', 'encargado'))
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'telefono' => $request->telefono,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $encargado->update($data);

            DB::commit();

            return redirect()->route('admin.encargados.index')
                ->with('success', '✅ Encargado actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando encargado: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', '❌ Error al actualizar encargado.')
                ->withInput();
        }
    }

    /**
     * Eliminar encargado
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $encargado = User::whereHas('roles', fn($q) => $q->where('name', 'encargado'))
                ->findOrFail($id);

            $encargado->syncRoles([]);
            $encargado->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Encargado eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando encargado: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al eliminar encargado.'
            ], 500);
        }
    }
}