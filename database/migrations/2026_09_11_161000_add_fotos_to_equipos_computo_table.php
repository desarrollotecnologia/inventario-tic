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
        Schema::table('equipos_computo', function (Blueprint $table) {
            if (! Schema::hasColumn('equipos_computo', 'foto_equipo')) {
                $table->string('foto_equipo')->nullable()->after('fecha_garantia');
            }

            if (! Schema::hasColumn('equipos_computo', 'foto_etiqueta')) {
                $table->string('foto_etiqueta')->nullable()->after('foto_equipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos_computo', function (Blueprint $table) {
            $columnas = array_values(array_filter(
                ['foto_equipo', 'foto_etiqueta'],
                fn (string $columna): bool => Schema::hasColumn('equipos_computo', $columna)
            ));

            if ($columnas !== []) {
                $table->dropColumn($columnas);
            }
        });
    }
};
