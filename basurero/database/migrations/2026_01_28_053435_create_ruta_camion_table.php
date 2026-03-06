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
       Schema::create('ruta_camion', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ruta_id')->constrained('rutas')->cascadeOnDelete();
        $table->foreignId('camion_id')->constrained('camiones')->cascadeOnDelete();
        $table->boolean('activa')->default(true);
        $table->timestamps();

        $table->unique(['ruta_id', 'camion_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta_camion');
    }
};
