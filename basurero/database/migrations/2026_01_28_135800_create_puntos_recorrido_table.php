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
        Schema::create('puntos_recorrido', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();

            $table->foreignId('recorrido_id')->constrained('recorridos')->cascadeOnDelete();

            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('precision_m', 8, 2)->nullable();
            $table->decimal('velocidad_mps', 8, 2)->nullable();
            $table->decimal('rumbo_grados', 8, 2)->nullable();

            $table->dateTime('fecha_gps'); // timestamp del GPS
            $table->timestamps();

            $table->index(['recorrido_id', 'fecha_gps']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_recorrido');
    }
};
