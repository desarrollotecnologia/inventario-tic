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
            $table->string('acta_numero')->nullable()->after('observaciones');
            $table->string('solicitante_nombre')->nullable()->after('acta_numero');
            $table->string('solicitante_cargo')->nullable()->after('solicitante_nombre');
            $table->string('solicitante_departamento')->nullable()->after('solicitante_cargo');
            $table->text('informacion')->nullable()->after('solicitante_departamento');
            $table->text('motivo_baja')->nullable()->after('informacion');
            $table->string('adjunto_acta')->nullable()->after('motivo_baja');
            $table->string('adjunto_evidencia')->nullable()->after('adjunto_acta');
            $table->string('adjunto_soporte')->nullable()->after('adjunto_evidencia');
            $table->timestamp('completada_at')->nullable()->after('adjunto_soporte');
        });

        foreach (DB::table('bajas')->whereNull('completada_at')->get() as $baja) {
            $partes = collect([
                $baja->descripcion,
                $baja->codigo,
                filled($baja->serial) ? 'Serial '.$baja->serial : null,
            ])->filter()->implode(' ');

            DB::table('bajas')->where('id', $baja->id)->update([
                'completada_at' => $baja->created_at,
                'informacion' => 'Se solicita dar de baja al siguiente activo: '.$partes,
                'motivo_baja' => 'Registro histórico previo al acta digital.',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bajas', function (Blueprint $table) {
            $table->dropColumn([
                'acta_numero',
                'solicitante_nombre',
                'solicitante_cargo',
                'solicitante_departamento',
                'informacion',
                'motivo_baja',
                'adjunto_acta',
                'adjunto_evidencia',
                'adjunto_soporte',
                'completada_at',
            ]);
        });
    }
};
