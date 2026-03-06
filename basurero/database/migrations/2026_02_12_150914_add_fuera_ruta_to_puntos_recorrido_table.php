<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // migration
public function up()
{
    Schema::table('puntos_recorrido', function (Blueprint $table) {
        $table->boolean('fuera_ruta')->default(false)->after('precision_m');
    });
}

public function down()
{
    Schema::table('puntos_recorrido', function (Blueprint $table) {
        $table->dropColumn('fuera_ruta');
    });
}
};
