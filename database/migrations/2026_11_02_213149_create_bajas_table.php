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
        Schema::create('bajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_computo_id')->nullable()->constrained('equipos_computo')->nullOnDelete();
            $table->foreignId('celular_id')->nullable()->constrained('celulares')->nullOnDelete();
            $table->foreignId('radio_id')->nullable()->constrained('radios_comunicacion')->nullOnDelete();
            $table->foreignId('impresora_id')->nullable()->constrained('impresoras')->nullOnDelete();
            $table->foreignId('biometrico_id')->nullable()->constrained('biometricos')->nullOnDelete();
            $table->date('fecha');
            $table->string('item')->nullable();
            $table->string('codigo')->nullable();
            $table->string('categoria')->nullable();
            $table->string('tipo')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('serial')->nullable();
            $table->string('usuario')->nullable();
            $table->string('cargo')->nullable();
            $table->string('departamento')->nullable();
            $table->string('area')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        $ahora = now();

        foreach (DB::table('equipos_computo')->where('en_baja', true)->get() as $equipo) {
            DB::table('bajas')->insert([
                'equipo_computo_id' => $equipo->id,
                'fecha' => $ahora->toDateString(),
                'item' => $equipo->item,
                'codigo' => $equipo->codigo,
                'categoria' => 'Equipo de cómputo',
                'tipo' => $equipo->descripcion,
                'descripcion' => $equipo->marca_referencia,
                'serial' => trim(collect([$equipo->service_tag_product_name, $equipo->serial_adicional])->filter()->implode(' / ')),
                'usuario' => $equipo->usuario,
                'cargo' => $equipo->cargo,
                'departamento' => $equipo->departamento,
                'area' => $equipo->area,
                'observaciones' => $equipo->observaciones,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        foreach (DB::table('celulares')->where('en_baja', true)->get() as $celular) {
            DB::table('bajas')->insert([
                'celular_id' => $celular->id,
                'fecha' => $ahora->toDateString(),
                'item' => $celular->item,
                'categoria' => 'Celular / Tablet',
                'tipo' => $celular->tipo,
                'descripcion' => trim(collect([$celular->marca, $celular->modelo])->filter()->implode(' ')),
                'serial' => $celular->serial,
                'usuario' => $celular->usuario,
                'cargo' => $celular->cargo,
                'departamento' => $celular->departamento,
                'area' => $celular->area,
                'observaciones' => $celular->observaciones ?? null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        foreach (DB::table('radios_comunicacion')->where('en_baja', true)->get() as $radio) {
            DB::table('bajas')->insert([
                'radio_id' => $radio->id,
                'fecha' => $ahora->toDateString(),
                'item' => $radio->item,
                'categoria' => 'Radio',
                'tipo' => $radio->tipo,
                'descripcion' => $radio->marca_referencia,
                'serial' => $radio->serial,
                'usuario' => $radio->usuario,
                'cargo' => $radio->cargo,
                'departamento' => $radio->departamento,
                'area' => $radio->area,
                'observaciones' => $radio->observaciones ?? null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        foreach (DB::table('impresoras')->where('en_baja', true)->get() as $impresora) {
            DB::table('bajas')->insert([
                'impresora_id' => $impresora->id,
                'fecha' => $ahora->toDateString(),
                'item' => $impresora->item,
                'categoria' => 'Impresora',
                'tipo' => $impresora->tipo,
                'descripcion' => $impresora->descripcion ?: $impresora->modelo,
                'serial' => $impresora->serial,
                'usuario' => $impresora->usuario,
                'cargo' => $impresora->cargo,
                'departamento' => $impresora->departamento,
                'area' => $impresora->area,
                'observaciones' => $impresora->observaciones ?? null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        foreach (DB::table('biometricos')->where('en_baja', true)->get() as $biometrico) {
            DB::table('bajas')->insert([
                'biometrico_id' => $biometrico->id,
                'fecha' => $ahora->toDateString(),
                'item' => $biometrico->item,
                'categoria' => 'Biométrico',
                'tipo' => 'BIOMETRICO',
                'descripcion' => $biometrico->marca_referencia,
                'serial' => $biometrico->serial,
                'usuario' => $biometrico->usuario,
                'cargo' => $biometrico->cargo,
                'departamento' => $biometrico->departamento,
                'area' => $biometrico->area,
                'observaciones' => $biometrico->observaciones ?? null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bajas');
    }
};
