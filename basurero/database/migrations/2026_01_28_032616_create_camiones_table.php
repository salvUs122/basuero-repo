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
       Schema::create('camiones', function (Blueprint $table) {
        $table->id();

        $table->string('placa', 20)->unique();          // Ej: EJHI234
        $table->string('codigo', 50)->unique();         // Código interno
        $table->enum('estado', ['activo', 'inactivo', 'mantenimiento'])
            ->default('activo');

        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camiones');
    }
};
