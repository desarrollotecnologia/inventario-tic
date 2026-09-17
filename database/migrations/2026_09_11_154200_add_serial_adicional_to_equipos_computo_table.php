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
        if (Schema::hasColumn('equipos_computo', 'serial_adicional')) {
            return;
        }

        Schema::table('equipos_computo', function (Blueprint $table) {
            $table->string('serial_adicional')->nullable()->after('service_tag_product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('equipos_computo', 'serial_adicional')) {
            return;
        }

        Schema::table('equipos_computo', function (Blueprint $table) {
            $table->dropColumn('serial_adicional');
        });
    }
};
