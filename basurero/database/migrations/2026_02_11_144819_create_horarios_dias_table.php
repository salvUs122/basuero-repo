<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  // ✅ IMPORTANTE: Agregar esta línea

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // 1. CREAR TABLA PARA HORARIOS POR DÍA
        // ============================================
        Schema::create('horarios_dias', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tabla ruta_camion
            $table->foreignId('ruta_camion_id')
                  ->constrained('ruta_camion')
                  ->onDelete('cascade');
            
            // Día de la semana
            $table->enum('dia', [
                'lunes', 'martes', 'miercoles', 'jueves', 
                'viernes', 'sabado', 'domingo'
            ]);
            
            // Horarios específicos para ese día
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            
            // Si está activo o no
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
            
            // Un día no puede repetirse para la misma asignación
            $table->unique(['ruta_camion_id', 'dia']);
        });

        // ============================================
        // 2. MIGRAR DATOS EXISTENTES (OPCIONAL)
        // ============================================
        if (Schema::hasTable('ruta_camion')) {
            $asignaciones = DB::table('ruta_camion')->get();  // ✅ AHORA DB ESTÁ IMPORTADA
            
            foreach ($asignaciones as $asignacion) {
                $diasSemana = json_decode($asignacion->dias_semana ?? '[]', true);
                
                foreach ($diasSemana as $dia) {
                    DB::table('horarios_dias')->insert([
                        'ruta_camion_id' => $asignacion->id,
                        'dia' => $dia,
                        'hora_inicio' => $asignacion->hora_inicio,
                        'hora_fin' => $asignacion->hora_fin,
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_dias');
    }
};