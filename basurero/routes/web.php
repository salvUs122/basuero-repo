<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\RecorridoController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\RecorridosAdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AsignacionRutaCamionController;
use App\Models\Camion;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// RUTAS PÚBLICAS
// ============================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================
// DASHBOARD PRINCIPAL (REDIRECCIÓN POR ROL)
// ============================================
// Dashboard principal (redirige según rol)
Route::get('/dashboard', function () {
    /** @var \App\Models\User $u */
    $u = Auth::user();

    if ($u->hasRole('administrador')) return redirect()->route('admin.dashboard');
    if ($u->hasRole('encargado'))     return redirect()->route('encargado.dashboard');
    if ($u->hasRole('conductor'))     abort(403, 'Acceso solo desde la app móvil.');

    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============================================
// PERFIL DE USUARIO (TODOS LOS ROLES)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// RUTAS PARA ADMINISTRADOR
// ============================================
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // ===== GESTIÓN DE ENCARGADOS =====
    Route::prefix('encargados')->name('encargados.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\EncargadoController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\Admin\EncargadoController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\EncargadoController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\EncargadoController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [App\Http\Controllers\Admin\EncargadoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\EncargadoController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\EncargadoController::class, 'destroy'])->name('destroy');
    });
});

// ============================================
// GESTIÓN DE CONDUCTORES (Admin y Encargado)
// ============================================
Route::middleware(['auth', 'role:administrador|encargado'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('conductores')->name('conductores.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ConductorController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\Admin\ConductorController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ConductorController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\ConductorController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [App\Http\Controllers\Admin\ConductorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\ConductorController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\ConductorController::class, 'destroy'])->name('destroy');
    });
    Route::get('/conductores/{conductor}/camiones', [App\Http\Controllers\Admin\AsignarConductorController::class, 'getCamionesConductor'])
        ->name('conductores.camiones');
    Route::get('/camiones/{camion}/rutas-hoy', [App\Http\Controllers\Admin\AsignarConductorController::class, 'getRutasHoy'])
        ->name('camiones.rutas_hoy');
});

// ============================================
// RUTAS PARA ADMINISTRADOR (GESTIÓN DE CAMIONES Y RUTAS)
// ============================================
Route::middleware(['auth', 'role:administrador|encargado'])->group(function () {
    
    // ===== GESTIÓN DE CAMIONES =====
    Route::prefix('camiones')->name('camiones.')->group(function () {
        Route::get('/', [CamionController::class, 'index'])->name('index');
        Route::get('/crear', [CamionController::class, 'create'])->name('create');
        Route::post('/', [CamionController::class, 'store'])->name('store');
        Route::get('/{camion}/editar', [CamionController::class, 'edit'])->name('edit');
        Route::put('/{camion}', [CamionController::class, 'update'])->name('update');
        Route::delete('/{camion}', [CamionController::class, 'destroy'])->name('destroy');
        Route::get('/{camion}/datos', [CamionController::class, 'getCamion'])->name('datos');
        Route::get('/{camion}/detalles', function(Camion $camion) {
            $html = view('camiones.partials.detalles', compact('camion'))->render();
            return response()->json(['html' => $html]);
        })->name('detalles');
    });
    
    // ===== ASIGNACIÓN DE RUTAS A CAMIONES =====
    Route::prefix('camiones/{camion}')->name('camiones.')->group(function () {
        Route::get('/asignar-rutas', [AsignacionRutaCamionController::class, 'edit'])->name('asignar_rutas');
        Route::post('/asignar-rutas', [AsignacionRutaCamionController::class, 'update'])->name('guardar_rutas');
        Route::delete('/eliminar-ruta', [AsignacionRutaCamionController::class, 'destroyAsignacion'])->name('eliminar_ruta');
        Route::post('/horarios', [AsignacionRutaCamionController::class, 'updateHorariosPorDia'])->name('horarios');
        Route::post('/guardar-horarios-dia', [AsignacionRutaCamionController::class, 'guardarHorariosPorDia'])->name('guardar_horarios_dia');
    });
});

// ============================================
// RUTAS PARA ADMINISTRADOR Y ENCARGADO
// ============================================
Route::middleware(['auth', 'role:administrador|encargado'])->group(function () {
    // ===== ASIGNAR CONDUCTOR A CAMIÓN =====
    Route::get('/camiones/{camion}/asignar-conductor', [App\Http\Controllers\Admin\AsignarConductorController::class, 'edit'])
        ->name('camiones.asignar_conductor');
    Route::put('/camiones/{camion}/asignar-conductor', [App\Http\Controllers\Admin\AsignarConductorController::class, 'update'])
        ->name('camiones.asignar_conductor.update');
    // ===== GESTIÓN DE RUTAS =====
    Route::prefix('rutas')->name('rutas.')->group(function () {
        Route::get('/', [RutaController::class, 'index'])->name('index');
        Route::get('/crear', [RutaController::class, 'create'])->name('create');
        Route::post('/', [RutaController::class, 'store'])->name('store');
        Route::get('/{id}', [RutaController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [RutaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RutaController::class, 'update'])->name('update');
        Route::delete('/{id}', [RutaController::class, 'destroy'])->name('destroy');
    });

    // ===== CONFIGURACIÓN DEL BOTADERO (GLOBAL) =====
    Route::get('/botadero', [RutaController::class, 'getBotadero'])->name('botadero.get');
    Route::post('/botadero', [RutaController::class, 'saveBotadero'])->name('botadero.save');
    Route::delete('/botadero', [RutaController::class, 'deleteBotadero'])->name('botadero.delete');
    
    // ===== MONITOREO EN VIVO =====
    Route::prefix('monitoreo')->name('monitoreo.')->group(function () {
        Route::get('/', [MonitoreoController::class, 'index'])->name('index');
        Route::get('/puntos-activos', [MonitoreoController::class, 'puntosActivos'])->name('puntos_activos');
        Route::get('/{recorrido}/puntos', [MonitoreoController::class, 'puntos'])->name('puntos');
        Route::get('/{recorrido}/eventos', [MonitoreoController::class, 'eventos'])->name('eventos');
    });
    
    // ===== HISTORIAL DE RECORRIDOS =====
    Route::prefix('recorridos')->name('recorridos.')->group(function () {
        Route::get('/', [RecorridosAdminController::class, 'index'])->name('index');
        Route::get('/{recorrido}', [RecorridosAdminController::class, 'show'])->name('show.historial');
        Route::get('/{recorrido}/detalle', [RecorridoController::class, 'show'])->name('show');
        Route::get('/{recorrido}/puntos', [RecorridoController::class, 'puntos'])->name('puntos');
        Route::get('/{recorrido}/paradas', [RecorridoController::class, 'paradas'])->name('paradas');
        Route::get('/{recorrido}/descargas/{descarga}/puntos', [RecorridoController::class, 'puntosDescarga'])->name('descarga.puntos');

        // Exportaciones
        Route::get('/{recorrido}/exportar/gps', [RecorridoController::class, 'exportarGPS'])->name('exportar.gps');
        Route::get('/{recorrido}/exportar/kml', [RecorridoController::class, 'exportarKML'])->name('exportar.kml');
        Route::get('/{recorrido}/exportar/csv', [RecorridoController::class, 'exportarCSV'])->name('exportar.csv');
    });
});
// ============================================
// RUTAS PARA ENCARGADO
// ============================================
Route::middleware(['auth', 'role:encargado'])->group(function () {
    // Dashboard del encargado
    Route::get('/encargado/dashboard', [App\Http\Controllers\EncargadoDashboardController::class, 'index'])->name('encargado.dashboard');
    
    // Redirección desde /encargado
    Route::get('/encargado', function () {
        return redirect()->route('encargado.dashboard');
    })->name('encargado.inicio');
});

// ============================================
// ARCHIVOS DE AUTENTICACIÓN
// ============================================
require __DIR__.'/auth.php';