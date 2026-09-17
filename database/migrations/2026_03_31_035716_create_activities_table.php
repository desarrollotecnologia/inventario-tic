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
        Schema::create('activities', function (Blueprint $table) {
              $table->id();
              $table->string('nombre');
              $table->string('semana');
              $table->date('fecha_entrega')->nullable();
              $table->string('responsables'); // Nombres separados por punto y coma
              $table->enum('estado', ['realizado', 'pendiente', 'seguimiento']);
              $table->text('observaciones')->nullable();
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
