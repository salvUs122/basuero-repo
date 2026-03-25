<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descargas_botadero', function (Blueprint $table) {
            $table->id();

            $table->foreignId('recorrido_id')
                  ->constrained('recorridos')
                  ->cascadeOnDelete();

            // Numero secuencial de descarga dentro del recorrido (1, 2, 3...)
            $table->unsignedSmallInteger('numero_descarga')->default(1);

            // Estado: en_descarga, finalizada, cancelada
            $table->enum('estado', ['en_descarga', 'finalizada', 'cancelada'])
                  ->default('en_descarga');

            // Punto de inicio de descarga (donde presiono "Descargar camion")
            $table->decimal('lat_inicio', 10, 7);
            $table->decimal('lng_inicio', 10, 7);
            $table->dateTime('fecha_inicio');

            // Punto de fin de descarga (donde presiono "Continuar ruta")
            $table->decimal('lat_fin', 10, 7)->nullable();
            $table->decimal('lng_fin', 10, 7)->nullable();
            $table->dateTime('fecha_fin')->nullable();

            // Estadisticas calculadas al finalizar
            $table->integer('puntos_durante_descarga')->default(0);
            $table->decimal('distancia_metros', 10, 2)->nullable();

            // Notas opcionales del conductor
            $table->string('observaciones', 255)->nullable();

            $table->timestamps();

            // Indices para consultas rapidas
            $table->index(['recorrido_id', 'estado']);
            $table->index(['recorrido_id', 'numero_descarga']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descargas_botadero');
    }
};
