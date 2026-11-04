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
        Schema::create('lectores_codigos_barras', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('codigo_contable')->nullable();
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
            $table->softDeletes();
        });

        Schema::table('bajas', function (Blueprint $table): void {
            $table->foreignId('lector_codigo_barras_id')
                ->nullable()
                ->after('biometrico_id')
                ->constrained('lectores_codigos_barras')
                ->nullOnDelete();
        });

        Schema::table('traslados', function (Blueprint $table): void {
            $table->foreignId('lector_codigo_barras_id')
                ->nullable()
                ->after('biometrico_id')
                ->constrained('lectores_codigos_barras')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traslados', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('lector_codigo_barras_id');
        });

        Schema::table('bajas', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('lector_codigo_barras_id');
        });

        Schema::dropIfExists('lectores_codigos_barras');
    }
};
