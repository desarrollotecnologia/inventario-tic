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
        Schema::create('biometricos', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('departamento')->nullable();
            $table->string('area')->nullable();
            $table->string('usuario')->nullable();
            $table->string('cargo')->nullable();
            $table->string('marca_referencia')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serial')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('en_traslado')->default(false);
            $table->boolean('en_baja')->default(false);
            $table->boolean('en_disponible')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometricos');
    }
};
