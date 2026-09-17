<?php

namespace App\Support;

use App\Models\Traslado;
use Illuminate\Support\Collection;

class TrasladoMensaje
{
    public static function asunto(): string
    {
        return 'Traslado de equipos de computo ('.self::mes().') ('.now()->year.')';
    }

    public static function mes(): string
    {
        return [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ][(int) now()->month];
    }

    /**
     * @param  Collection<int, Traslado>  $traslados
     */
    public static function whatsapp(Collection $traslados): string
    {
        $lineas = [
            "\u{2705} Se enviaron los traslados",
            '',
        ];

        foreach ($traslados as $traslado) {
            $detalle = collect([
                $traslado->tipo,
                $traslado->descripcion_equipo,
                $traslado->serial ?: 's/n',
            ])->filter()->implode(' - ');

            $lineas[] = "\u{1F4BB} ".($detalle !== '' ? $detalle : 'Equipo');
        }

        return implode("\n", $lineas);
    }

    /**
     * @param  Collection<int, Traslado>  $traslados
     */
    public static function urlWhatsapp(Collection $traslados): string
    {
        return 'https://api.whatsapp.com/send?text='.rawurlencode(self::whatsapp($traslados));
    }
}
