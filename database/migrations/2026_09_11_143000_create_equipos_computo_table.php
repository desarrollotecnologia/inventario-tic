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
        Schema::create('equipos_computo', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('codigo')->nullable()->unique();
            $table->string('departamento')->nullable();
            $table->string('area')->nullable();
            $table->string('usuario')->nullable();
            $table->string('cargo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('marca_referencia')->nullable();
            $table->string('service_tag_product_name')->nullable();
            $table->string('serial_adicional')->nullable();
            $table->string('procesador')->nullable();
            $table->string('memoria')->nullable();
            $table->string('disco')->nullable();
            $table->string('windows')->nullable();
            $table->string('serial_windows')->nullable();
            $table->string('office')->nullable();
            $table->string('serial_office')->nullable();
            $table->string('cuenta_office365')->nullable();
            $table->string('antivirus')->nullable();
            $table->string('usuario_deslock_eset')->nullable();
            $table->string('contrasena_deslock_eset')->nullable();
            $table->string('nombre_equipo')->nullable();
            $table->string('mac_cableada')->nullable();
            $table->string('mac_inalambrica')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->date('fecha_garantia')->nullable();
            $table->string('foto_equipo')->nullable();
            $table->string('foto_etiqueta')->nullable();
            $table->string('foto_etiqueta_procesador')->nullable();
            $table->boolean('en_traslado')->default(false);
            $table->boolean('en_baja')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos_computo');
    }
};
