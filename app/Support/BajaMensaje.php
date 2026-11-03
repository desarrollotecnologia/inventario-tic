<?php

namespace App\Support;

class BajaMensaje
{
    public static function asunto(): string
    {
        return 'Baja de equipos de computo '.self::mes().' '.now()->year;
    }

    public static function tituloTabla(): string
    {
        return 'BAJAR DEL INVENTARIO EQUIPOS DE CÓMPUTO '.mb_strtoupper(self::mes()).' '.now()->year;
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
}
