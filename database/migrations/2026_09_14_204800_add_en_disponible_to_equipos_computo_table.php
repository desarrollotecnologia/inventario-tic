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
            if (! Schema::hasColumn('equipos_computo', 'en_disponible')) {
                $table->boolean('en_disponible')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos_computo', function (Blueprint $table) {
            if (Schema::hasColumn('equipos_computo', 'en_disponible')) {
                $table->dropColumn('en_disponible');
            }
        });
    }
};
