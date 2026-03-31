<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Aquí irán las rutas protegidas
    Route::get('/conductor/camiones', [App\Http\Controllers\Api\ConductorApiController::class, 'getCamiones']);
    Route::get('/conductor/rutas-hoy', [App\Http\Controllers\Api\ConductorApiController::class, 'getRutasHoy']);
    Route::get('/conductor/recorrido/activo', [App\Http\Controllers\Api\ConductorApiController::class, 'getRecorridoActivo']);
    Route::get('/conductor/recorrido/puntos', [App\Http\Controllers\Api\ConductorApiController::class, 'getPuntosRecorridoActivo']);
    Route::get('/conductor/recorrido/paradas', [App\Http\Controllers\Api\ConductorApiController::class, 'getParadas']);
    Route::post('/conductor/recorrido/iniciar', [App\Http\Controllers\Api\ConductorApiController::class, 'iniciarRecorrido']);
    Route::post('/conductor/recorrido/finalizar', [App\Http\Controllers\Api\ConductorApiController::class, 'finalizarRecorrido']);
    Route::post('/conductor/gps', [App\Http\Controllers\Api\ConductorApiController::class, 'guardarGps']);
    Route::get('/rutas/{ruta}', [App\Http\Controllers\Api\ConductorApiController::class, 'getRuta']);

    // Descarga al botadero
    Route::post('/conductor/descarga/iniciar', [App\Http\Controllers\Api\ConductorApiController::class, 'iniciarDescarga']);
    Route::post('/conductor/descarga/finalizar', [App\Http\Controllers\Api\ConductorApiController::class, 'finalizarDescarga']);
    Route::get('/conductor/descarga/activa', [App\Http\Controllers\Api\ConductorApiController::class, 'getDescargaActiva']);

    // Configuraciones del sistema
    Route::get('/configuraciones', [App\Http\Controllers\Api\ConductorApiController::class, 'getConfiguraciones']);
    Route::put('/configuraciones', [App\Http\Controllers\Api\ConductorApiController::class, 'updateConfiguracion']);
});