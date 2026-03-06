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
        Schema::create('eventos_recorrido', function (Blueprint $table) {
        $table->id();
        $table->foreignId('recorrido_id')->constrained('recorridos')->cascadeOnDelete();
        $table->string('tipo'); // fuera_ruta, incidente, reemplazo, etc
        $table->string('mensaje')->nullable();
        $table->decimal('lat', 10, 7)->nullable();
        $table->decimal('lng', 10, 7)->nullable();
        $table->integer('distancia_m')->nullable();
        $table->dateTime('fecha_evento');
        $table->timestamps();

        $table->index(['recorrido_id', 'tipo', 'fecha_evento']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_recorrido');
    }
};
