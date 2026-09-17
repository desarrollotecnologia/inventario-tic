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
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_computo_id')->constrained('equipos_computo')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('usuario_entrega')->nullable();
            $table->string('cargo_entrega')->nullable();
            $table->string('departamento_entrega')->nullable();
            $table->string('area_entrega')->nullable();
            $table->string('codigo')->nullable();
            $table->string('tipo')->nullable();
            $table->string('descripcion_equipo')->nullable();
            $table->string('serial')->nullable();
            $table->string('usuario_recibe');
            $table->string('cargo_recibe')->nullable();
            $table->string('departamento_recibe')->nullable();
            $table->string('area_recibe')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
