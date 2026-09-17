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
        if (Schema::hasColumn('equipos_computo', 'foto_etiqueta_procesador')) {
            return;
        }

        Schema::table('equipos_computo', function (Blueprint $table) {
            $table->string('foto_etiqueta_procesador')->nullable()->after('foto_etiqueta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('equipos_computo', 'foto_etiqueta_procesador')) {
            return;
        }

        Schema::table('equipos_computo', function (Blueprint $table) {
            $table->dropColumn('foto_etiqueta_procesador');
        });
    }
};
