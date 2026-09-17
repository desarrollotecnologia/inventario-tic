<?php

namespace App\Support;

use Carbon\CarbonInterface;

class Antiguedad
{
    public static function desde(?CarbonInterface $fecha): string
    {
        if ($fecha === null) {
            return '—';
        }

        $inicio = $fecha->copy()->startOfDay();
        $hoy = now()->startOfDay();

        if ($inicio->greaterThan($hoy)) {
            return 'Fecha futura';
        }

        $diff = $inicio->diff($hoy);
        $partes = [];

        if ($diff->y > 0) {
            $partes[] = $diff->y.' '.($diff->y === 1 ? 'año' : 'años');
        }

        if ($diff->m > 0) {
            $partes[] = $diff->m.' '.($diff->m === 1 ? 'mes' : 'meses');
        }

        if ($diff->d > 0 || $partes === []) {
            $partes[] = $diff->d.' '.($diff->d === 1 ? 'día' : 'días');
        }

        return implode(', ', $partes);
    }
}
