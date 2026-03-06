<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('recorridos', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->id();

        $table->foreignId('ruta_id')->constrained('rutas');
        $table->foreignId('camion_id')->constrained('camiones');
        $table->foreignId('conductor_id')->constrained('users');

        $table->enum('estado', ['en_curso', 'finalizado', 'cancelado'])->default('en_curso');

        $table->dateTime('fecha_inicio');
        $table->dateTime('fecha_fin')->nullable();

        $table->decimal('lat_inicio', 10, 7)->nullable();
        $table->decimal('lng_inicio', 10, 7)->nullable();
        $table->decimal('lat_fin', 10, 7)->nullable();
        $table->decimal('lng_fin', 10, 7)->nullable();

        $table->integer('total_puntos')->default(0);
        $table->integer('eventos_fuera_ruta')->default(0);

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorridos');
    }
};
