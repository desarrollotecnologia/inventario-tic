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
        Schema::create('celulares', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('departamento')->nullable();
            $table->string('area')->nullable();
            $table->string('usuario')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serial')->nullable();
            $table->string('imei_1')->nullable();
            $table->string('imei_2')->nullable();
            $table->string('numero_celular')->nullable();
            $table->string('direccion_mac')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_garantia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celulares');
    }
};
