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
            if (! Schema::hasColumn('equipos_computo', 'en_traslado')) {
                $table->boolean('en_traslado')->default(false);
            }

            if (! Schema::hasColumn('equipos_computo', 'en_baja')) {
                $table->boolean('en_baja')->default(false);
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
                ['en_traslado', 'en_baja'],
                fn (string $columna): bool => Schema::hasColumn('equipos_computo', $columna)
            ));

            if ($columnas !== []) {
                $table->dropColumn($columnas);
            }
        });
    }
};
