<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('equipos_computo', 'serial_cpu_express_code')) {
            return;
        }

        DB::table('equipos_computo')
            ->where(function ($query) {
                $query->whereNull('service_tag_product_name')
                    ->orWhere('service_tag_product_name', '');
            })
            ->update([
                'service_tag_product_name' => DB::raw('serial_cpu_express_code'),
            ]);

        Schema::table('equipos_computo', function (Blueprint $table) {
            $table->dropColumn('serial_cpu_express_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('equipos_computo', 'serial_cpu_express_code')) {
            Schema::table('equipos_computo', function (Blueprint $table) {
                $table->string('serial_cpu_express_code')->nullable();
            });
        }
    }
};
