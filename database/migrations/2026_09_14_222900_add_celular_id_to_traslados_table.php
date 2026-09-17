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
        Schema::table('traslados', function (Blueprint $table) {
            $table->dropForeign(['equipo_computo_id']);
        });

        Schema::table('traslados', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_computo_id')->nullable()->change();
            $table->foreign('equipo_computo_id')->references('id')->on('equipos_computo')->cascadeOnDelete();
            $table->foreignId('celular_id')->nullable()->after('equipo_computo_id')->constrained('celulares')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->dropConstrainedForeignId('celular_id');
            $table->dropForeign(['equipo_computo_id']);
        });

        Schema::table('traslados', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_computo_id')->nullable(false)->change();
            $table->foreign('equipo_computo_id')->references('id')->on('equipos_computo')->cascadeOnDelete();
        });
    }
};
