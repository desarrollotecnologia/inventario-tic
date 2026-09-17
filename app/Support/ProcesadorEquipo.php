<?php

namespace App\Support;

class ProcesadorEquipo
{
    /**
     * @return array{modelo: string, version: string, velocidad: string}
     */
    public static function parse(?string $value): array
    {
        $valor = trim((string) $value);

        if ($valor === '') {
            return ['modelo' => '', 'version' => '', 'velocidad' => ''];
        }

        foreach (self::familias() as $familia) {
            if (strncasecmp($valor, $familia, strlen($familia)) !== 0) {
                continue;
            }

            $resto = ltrim(trim(substr($valor, strlen($familia))), "-– \t");

            return [
                'modelo' => $familia,
                ...self::extraerVersionYVelocidad($resto),
            ];
        }

        if (preg_match('/^(.*?)\s+-\s+(.+?)\s+[–-]\s+(.+)$/u', $valor, $partes) === 1) {
            return [
                'modelo' => trim($partes[1]),
                'version' => trim($partes[2]),
                'velocidad' => self::normalizarVelocidad($partes[3]),
            ];
        }

        if (preg_match('/^(.*?)\s+[–-]\s+(.+)$/u', $valor, $partes) === 1) {
            $derecha = trim($partes[2]);

            if (self::pareceVelocidad($derecha)) {
                return [
                    'modelo' => trim($partes[1]),
                    'version' => '',
                    'velocidad' => self::normalizarVelocidad($derecha),
                ];
            }

            return [
                'modelo' => trim($partes[1]),
                'version' => $derecha,
                'velocidad' => '',
            ];
        }

        return ['modelo' => $valor, 'version' => '', 'velocidad' => ''];
    }

    public static function compose(?string $modelo, ?string $version, ?string $velocidad): ?string
    {
        $modelo = trim((string) $modelo);
        $version = trim((string) $version);
        $velocidad = self::normalizarVelocidad($velocidad);

        if ($modelo === '') {
            return null;
        }

        if ($version !== '' && $velocidad !== '') {
            return $modelo.' - '.$version.' – '.$velocidad;
        }

        if ($version !== '') {
            return $modelo.' - '.$version;
        }

        if ($velocidad !== '') {
            return $modelo.' – '.$velocidad;
        }

        return $modelo;
    }

    public static function normalizarVelocidad(?string $valor): string
    {
        $velocidad = trim((string) $valor);

        if ($velocidad === '') {
            return '';
        }

        $velocidad = str_replace(',', '.', $velocidad);
        $velocidad = (string) preg_replace('/\s+/', '', $velocidad);

        if (preg_match('/^(\d+(?:\.\d+)?)(?:ghz)?$/i', $velocidad, $partes) === 1) {
            return $partes[1].'GHz';
        }

        return $velocidad;
    }

    /**
     * @return array{version: string, velocidad: string}
     */
    private static function extraerVersionYVelocidad(string $resto): array
    {
        if ($resto === '') {
            return ['version' => '', 'velocidad' => ''];
        }

        if (preg_match('/^(.+?)\s+[–-]\s+(.+)$/u', $resto, $partes) === 1) {
            $izquierda = trim($partes[1]);
            $derecha = trim($partes[2]);

            if (self::pareceVelocidad($derecha)) {
                return [
                    'version' => $izquierda,
                    'velocidad' => self::normalizarVelocidad($derecha),
                ];
            }

            return [
                'version' => $izquierda,
                'velocidad' => $derecha,
            ];
        }

        if (self::pareceVelocidad($resto)) {
            return ['version' => '', 'velocidad' => self::normalizarVelocidad($resto)];
        }

        return ['version' => $resto, 'velocidad' => ''];
    }

    /**
     * @return list<string>
     */
    private static function familias(): array
    {
        $familias = EquipoComputoFields::procesadores();
        usort($familias, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return $familias;
    }

    private static function pareceVelocidad(string $valor): bool
    {
        return preg_match('/^\d+([.,]\d+)?\s*(ghz)?$/i', trim($valor)) === 1;
    }
}
