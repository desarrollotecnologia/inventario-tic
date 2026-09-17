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
        Schema::table('celulares', function (Blueprint $table) {
            $table->string('almacenamiento')->nullable()->after('modelo');
            $table->string('ram')->nullable()->after('almacenamiento');
            $table->string('ram_expansiva')->nullable()->after('ram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('celulares', function (Blueprint $table) {
            $table->dropColumn(['almacenamiento', 'ram', 'ram_expansiva']);
        });
    }
};
