<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // 1. TABLA RECORRIDOS - CASCADE CON CAMIONES
        // ============================================
        Schema::table('recorridos', function (Blueprint $table) {
            // Eliminar FK existente
            $table->dropForeign(['camion_id']);
            
            // Crear nueva FK con CASCADE
            $table->foreign('camion_id')
                  ->references('id')
                  ->on('camiones')
                  ->onDelete('cascade'); // ✅ Al eliminar camión, se eliminan sus recorridos
        });

        // ============================================
        // 2. TABLA RECORRIDOS - CASCADE CON RUTAS
        // ============================================
        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropForeign(['ruta_id']);
            
            $table->foreign('ruta_id')
                  ->references('id')
                  ->on('rutas')
                  ->onDelete('restrict'); // ⚠️ NO eliminar rutas si tienen recorridos
        });

        // ============================================
        // 3. TABLA RECORRIDOS - CASCADE CON USERS
        // ============================================
        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropForeign(['conductor_id']);
            
            $table->foreign('conductor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // ⚠️ NO eliminar conductores si tienen recorridos
        });

        // ============================================
        // 4. TABLA RUTA_CAMION - CASCADE COMPLETO
        // ============================================
        Schema::table('ruta_camion', function (Blueprint $table) {
            // Eliminar FKs existentes
            $table->dropForeign(['camion_id']);
            $table->dropForeign(['ruta_id']);
            
            // Crear nuevas FKs con CASCADE
            $table->foreign('camion_id')
                  ->references('id')
                  ->on('camiones')
                  ->onDelete('cascade'); // ✅ Al eliminar camión, se eliminan sus asignaciones
            
            $table->foreign('ruta_id')
                  ->references('id')
                  ->on('rutas')
                  ->onDelete('cascade'); // ✅ Al eliminar ruta, se eliminan sus asignaciones
        });

        // ============================================
        // 5. TABLA PUNTOS_RECORRIDO - CASCADE
        // ============================================
        Schema::table('puntos_recorrido', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
            
            $table->foreign('recorrido_id')
                  ->references('id')
                  ->on('recorridos')
                  ->onDelete('cascade'); // ✅ Al eliminar recorrido, se eliminan sus puntos
        });

        // ============================================
        // 6. TABLA EVENTOS_RECORRIDO - CASCADE
        // ============================================
        Schema::table('eventos_recorrido', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
            
            $table->foreign('recorrido_id')
                  ->references('id')
                  ->on('recorridos')
                  ->onDelete('cascade'); // ✅ Al eliminar recorrido, se eliminan sus eventos
        });
    }

    public function down(): void
    {
        // Revertir cambios si es necesario
        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropForeign(['camion_id']);
            $table->foreign('camion_id')->references('id')->on('camiones');
            
            $table->dropForeign(['ruta_id']);
            $table->foreign('ruta_id')->references('id')->on('rutas');
            
            $table->dropForeign(['conductor_id']);
            $table->foreign('conductor_id')->references('id')->on('users');
        });

        Schema::table('ruta_camion', function (Blueprint $table) {
            $table->dropForeign(['camion_id']);
            $table->foreign('camion_id')->references('id')->on('camiones');
            
            $table->dropForeign(['ruta_id']);
            $table->foreign('ruta_id')->references('id')->on('rutas');
        });

        Schema::table('puntos_recorrido', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
            $table->foreign('recorrido_id')->references('id')->on('recorridos');
        });

        Schema::table('eventos_recorrido', function (Blueprint $table) {
            $table->dropForeign(['recorrido_id']);
            $table->foreign('recorrido_id')->references('id')->on('recorridos');
        });
    }
};