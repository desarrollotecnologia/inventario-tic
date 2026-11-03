<?php

namespace App\Support;

use App\Models\Baja;
use App\Models\BajaSolicitante;

class BajaActaFields
{
    /**
     * @return list<string>
     */
    public static function cargos(): array
    {
        return [
            'AUXILIAR TIC',
            'ANALISTA TIC',
            'GESTOR TIC',
        ];
    }

    public static function departamento(): string
    {
        return 'ADMINISTRATIVO Y FINANCIERO';
    }

    /**
     * @return list<string>
     */
    public static function solicitantes(): array
    {
        return BajaSolicitante::nombres();
    }

    public static function siguienteActaNumero(): string
    {
        $maximo = Baja::query()
            ->whereNotNull('acta_numero')
            ->where('acta_numero', '!=', '')
            ->get()
            ->map(fn (Baja $baja): int => (int) preg_replace('/\D+/', '', (string) $baja->acta_numero))
            ->max();

        return (string) (((int) $maximo) + 1);
    }
}
