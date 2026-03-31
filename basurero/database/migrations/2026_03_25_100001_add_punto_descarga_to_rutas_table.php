<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega coordenadas del punto de descarga (botadero) a las rutas
     */
    public function up(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->decimal('punto_descarga_lat', 10, 7)->nullable()->after('geometria_geojson');
            $table->decimal('punto_descarga_lng', 10, 7)->nullable()->after('punto_descarga_lat');
            $table->string('punto_descarga_nombre', 150)->nullable()->after('punto_descarga_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->dropColumn(['punto_descarga_lat', 'punto_descarga_lng', 'punto_descarga_nombre']);
        });
    }
};
