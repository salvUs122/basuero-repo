<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puntos_recorrido', function (Blueprint $table) {
            // FK nullable: si es null, el punto es parte del recorrido normal
            // si tiene valor, el punto fue capturado durante una descarga
            $table->foreignId('descarga_id')
                  ->nullable()
                  ->after('recorrido_id')
                  ->constrained('descargas_botadero')
                  ->nullOnDelete();

            $table->index('descarga_id');
        });
    }

    public function down(): void
    {
        Schema::table('puntos_recorrido', function (Blueprint $table) {
            $table->dropForeign(['descarga_id']);
            $table->dropColumn('descarga_id');
        });
    }
};
