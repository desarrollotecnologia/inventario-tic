<?php

namespace App\Support;

use App\Models\Baja;

class BajaAdjuntoNombres
{
    public static function descripcion(Baja $baja): string
    {
        $descripcion = trim((string) ($baja->descripcion ?: $baja->tipo ?: $baja->codigoMostrado()));

        if ($descripcion === '' || $descripcion === '—') {
            $descripcion = 'Equipo_'.$baja->id;
        }

        return self::sanear($descripcion);
    }

    public static function acta(Baja $baja): string
    {
        return 'Acta_Baja_'.self::descripcion($baja).'.pdf';
    }

    public static function diagnostico(Baja $baja, string $extension): string
    {
        return 'Diagnostico_Baja_'.self::descripcion($baja).'.'.ltrim($extension, '.');
    }

    public static function evidencia(Baja $baja, string $extension, int $indice = 1): string
    {
        $sufijo = $indice > 1 ? '_evidencia_'.$indice : '_evidencia';

        return self::descripcion($baja).$sufijo.'.'.ltrim($extension, '.');
    }

    public static function sanear(string $nombre): string
    {
        $nombre = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], ' ', $nombre);
        $nombre = preg_replace('/\s+/u', ' ', $nombre) ?? $nombre;
        $nombre = trim($nombre, " .\t\n\r\0\x0B");

        return $nombre !== '' ? $nombre : 'Equipo';
    }
}
