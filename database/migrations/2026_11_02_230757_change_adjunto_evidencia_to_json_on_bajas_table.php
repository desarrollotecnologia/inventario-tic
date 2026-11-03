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
        Schema::table('bajas', function (Blueprint $table) {
            $table->json('adjunto_evidencias_tmp')->nullable();
        });

        DB::table('bajas')->orderBy('id')->each(function (object $baja): void {
            $valor = $baja->adjunto_evidencia;

            if ($valor === null || $valor === '') {
                return;
            }

            $decoded = json_decode((string) $valor, true);
            $rutas = is_array($decoded)
                ? array_values(array_filter($decoded, fn (mixed $ruta): bool => is_string($ruta) && $ruta !== ''))
                : [(string) $valor];

            DB::table('bajas')->where('id', $baja->id)->update([
                'adjunto_evidencias_tmp' => json_encode($rutas),
            ]);
        });

        Schema::table('bajas', function (Blueprint $table) {
            $table->dropColumn('adjunto_evidencia');
        });

        Schema::table('bajas', function (Blueprint $table) {
            $table->renameColumn('adjunto_evidencias_tmp', 'adjunto_evidencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bajas', function (Blueprint $table) {
            $table->string('adjunto_evidencia_tmp')->nullable();
        });

        DB::table('bajas')->orderBy('id')->each(function (object $baja): void {
            $valor = $baja->adjunto_evidencia;

            if ($valor === null || $valor === '') {
                return;
            }

            $decoded = is_string($valor) ? json_decode($valor, true) : $valor;
            $primera = is_array($decoded) ? ($decoded[0] ?? null) : $valor;

            DB::table('bajas')->where('id', $baja->id)->update([
                'adjunto_evidencia_tmp' => is_string($primera) ? $primera : null,
            ]);
        });

        Schema::table('bajas', function (Blueprint $table) {
            $table->dropColumn('adjunto_evidencia');
        });

        Schema::table('bajas', function (Blueprint $table) {
            $table->renameColumn('adjunto_evidencia_tmp', 'adjunto_evidencia');
        });
    }
};
