<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruta_camion', function (Blueprint $table) {
            $table->time('hora_inicio')->nullable()->after('activa');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->json('dias_semana')->nullable()->after('hora_fin');
        });
    }

    public function down(): void
    {
        Schema::table('ruta_camion', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio', 'hora_fin', 'dias_semana']);
        });
    }
};