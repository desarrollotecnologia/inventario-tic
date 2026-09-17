<?php

namespace App\Support;

class TextoInventario
{
    /**
     * @param  array<string, mixed>  $datos
     * @param  list<string>  $excepto
     * @return array<string, mixed>
     */
    public static function mayusculas(array $datos, array $excepto = []): array
    {
        foreach ($datos as $clave => $valor) {
            if (! is_string($clave) || in_array($clave, $excepto, true) || ! is_string($valor) || $valor === '') {
                continue;
            }

            $datos[$clave] = self::texto($valor);
        }

        return $datos;
    }

    public static function texto(string $valor): string
    {
        return mb_strtoupper($valor, 'UTF-8');
    }

    public static function like(string $valor): string
    {
        return '%'.self::texto($valor).'%';
    }
}
