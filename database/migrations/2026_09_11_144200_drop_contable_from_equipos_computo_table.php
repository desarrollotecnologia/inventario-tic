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
        if (Schema::hasColumn('equipos_computo', 'contable')) {
            Schema::table('equipos_computo', function (Blueprint $table) {
                $table->dropColumn('contable');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('equipos_computo', 'contable')) {
            Schema::table('equipos_computo', function (Blueprint $table) {
                $table->string('contable')->nullable();
            });
        }
    }
};
