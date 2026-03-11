<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar configuración por defecto: distancia mínima entre puntos GPS (metros)
        DB::table('configuraciones')->insert([
            'clave'       => 'gps_distancia_minima_metros',
            'valor'       => '10',
            'descripcion' => 'Distancia mínima en metros entre puntos GPS para registrar un nuevo punto',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};
