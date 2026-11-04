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
        foreach ([
            'equipos_computo',
            'celulares',
            'radios_comunicacion',
            'impresoras',
            'biometricos',
        ] as $tabla) {
            Schema::table($tabla, function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        $ahora = now();

        $mapeo = [
            'equipo_computo_id' => 'equipos_computo',
            'celular_id' => 'celulares',
            'radio_id' => 'radios_comunicacion',
            'impresora_id' => 'impresoras',
            'biometrico_id' => 'biometricos',
        ];

        foreach ($mapeo as $columna => $tabla) {
            $ids = DB::table('bajas')
                ->whereNotNull('correo_enviado_at')
                ->whereNotNull($columna)
                ->pluck($columna)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($ids === []) {
                continue;
            }

            DB::table($tabla)
                ->whereIn('id', $ids)
                ->whereNull('deleted_at')
                ->update([
                    'en_baja' => false,
                    'deleted_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            'equipos_computo',
            'celulares',
            'radios_comunicacion',
            'impresoras',
            'biometricos',
        ] as $tabla) {
            Schema::table($tabla, function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};
