<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camiones', function (Blueprint $table) {
            $table->foreignId('conductor_id')
                  ->nullable()
                  ->after('estado')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('camiones', function (Blueprint $table) {
            $table->dropForeign(['conductor_id']);
            $table->dropColumn('conductor_id');
        });
    }
};