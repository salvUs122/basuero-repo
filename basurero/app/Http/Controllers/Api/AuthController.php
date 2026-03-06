<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // ✅ AGREGAR

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            Log::info('Intento de login', ['email' => $request->email]); // ✅ LOG
            
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('Credenciales incorrectas', ['email' => $request->email]); // ✅ LOG
                return response()->json(['message' => 'Credenciales incorrectas'], 401);
            }

            Log::info('Usuario encontrado', ['user_id' => $user->id]); // ✅ LOG

            // Verificar que sea conductor
            if (!$user->hasRole('conductor')) {
                Log::warning('Usuario no es conductor', ['user_id' => $user->id]); // ✅ LOG
                return response()->json(['message' => 'No tienes permisos de conductor'], 403);
            }

            Log::info('Generando token...'); // ✅ LOG
            
            $token = $user->createToken('mobile')->plainTextToken;

            Log::info('Login exitoso', ['user_id' => $user->id]); // ✅ LOG

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ]);
            
        } catch (\Exception $e) {
            Log::error('ERROR EN LOGIN: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]); // ✅ LOG DE ERROR
            
            return response()->json([
                'error' => true,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}