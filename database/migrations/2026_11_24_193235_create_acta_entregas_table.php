<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actas_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('ciudad')->default('Floridablanca');
            $table->date('fecha');
            $table->string('representante');
            $table->string('destino');
            $table->string('proposito')->nullable();
            $table->string('entregado_por_nombre');
            $table->string('entregado_por_cargo');
            $table->string('recibido_por_nombre');
            $table->string('recibido_por_documento')->nullable();
            $table->json('equipos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actas_entrega');
    }
};
