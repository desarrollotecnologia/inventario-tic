<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InventarioExcelImport
{
    /**
     * @return array{0: list<string>, 1: list<array<string, mixed>>, 2: list<array<string, mixed>>}
     */
    public static function parsear(string $ruta): array
    {
        $hoja = IOFactory::load($ruta)->getSheetByName('INVENTARIO');

        if ($hoja === null) {
            throw new \RuntimeException('No se encontró la hoja INVENTARIO.');
        }

        $filas = $hoja->toArray(null, true, true, false);
        $equipos = [];
        $celulares = [];
        $avisos = [];
        $siguienteEquipo = 1;
        $siguienteCelular = 1;

        foreach (array_slice($filas, 3) as $fila) {
            $itemExcel = self::texto($fila[0] ?? null);

            if ($itemExcel === null) {
                continue;
            }

            $descripcion = self::texto($fila[6] ?? null);
            $tipoMovil = self::tipoMovil($descripcion);

            if ($tipoMovil !== null) {
                $item = self::itemNumerico($itemExcel) ?? (string) $siguienteCelular;
                $siguienteCelular = max($siguienteCelular, ((int) $item) + 1);
                $celulares[] = self::mapearCelular($fila, $item, $tipoMovil);

                continue;
            }

            $item = self::itemNumerico($itemExcel) ?? (string) $siguienteEquipo;
            $siguienteEquipo = max($siguienteEquipo, ((int) $item) + 1);
            $equipos[] = self::mapearEquipo($fila, $item, $descripcion);
        }

        return [$avisos, $equipos, $celulares];
    }

    private static function itemNumerico(?string $item): ?string
    {
        if ($item === null || preg_match('/^\d+$/', $item) !== 1) {
            return null;
        }

        return (string) ((int) $item);
    }

    /**
     * @param  list<mixed>  $fila
     * @return array<string, mixed>
     */
    private static function mapearEquipo(array $fila, string $item, ?string $descripcion): array
    {
        $fechaAdquisicion = self::parseFecha($fila[25] ?? null, $fila[24] ?? null);
        $fechaGarantia = self::parseGarantia($fila[26] ?? null, $fechaAdquisicion);
        $observaciones = self::textoLargo($fila[24] ?? null);

        return [
            'item' => $item,
            'codigo' => self::texto($fila[1] ?? null),
            'departamento' => self::texto($fila[2] ?? null),
            'area' => self::texto($fila[3] ?? null),
            'usuario' => self::texto($fila[4] ?? null),
            'cargo' => self::texto($fila[5] ?? null),
            'descripcion' => $descripcion,
            'marca_referencia' => self::texto($fila[7] ?? null),
            'service_tag_product_name' => self::texto($fila[8] ?? null),
            'serial_adicional' => self::texto($fila[9] ?? null),
            'procesador' => self::texto($fila[10] ?? null),
            'memoria' => self::texto($fila[11] ?? null),
            'disco' => self::texto($fila[12] ?? null),
            'windows' => self::texto($fila[13] ?? null),
            'serial_windows' => self::texto($fila[14] ?? null),
            'office' => self::texto($fila[15] ?? null),
            'serial_office' => self::texto($fila[16] ?? null),
            'cuenta_office365' => self::email($fila[17] ?? null),
            'antivirus' => self::texto($fila[18] ?? null),
            'usuario_deslock_eset' => self::texto($fila[19] ?? null),
            'contrasena_deslock_eset' => self::texto($fila[20] ?? null),
            'nombre_equipo' => self::texto($fila[21] ?? null),
            'mac_cableada' => self::mac($fila[22] ?? null),
            'mac_inalambrica' => self::mac($fila[23] ?? null),
            'observaciones' => $observaciones,
            'fecha_adquisicion' => $fechaAdquisicion,
            'fecha_garantia' => $fechaGarantia,
            'en_baja' => self::esBaja($observaciones),
            'en_traslado' => false,
            'en_disponible' => false,
        ];
    }

    /**
     * @param  list<mixed>  $fila
     * @return array<string, mixed>
     */
    private static function mapearCelular(array $fila, string $item, string $tipo): array
    {
        $marcaCompleta = self::texto($fila[7] ?? null);
        [$marca, $modelo] = self::partirMarcaModelo($marcaCompleta);
        $fechaEntrega = self::parseFecha($fila[25] ?? null, $fila[24] ?? null);
        $fechaGarantia = self::parseGarantia($fila[26] ?? null, $fechaEntrega);
        $observaciones = self::textoLargo($fila[24] ?? null);
        $imeis = self::extraerImeis($observaciones);
        $serial = self::texto($fila[9] ?? null) ?: self::texto($fila[8] ?? null);

        return [
            'item' => $item,
            'departamento' => self::texto($fila[2] ?? null),
            'area' => self::texto($fila[3] ?? null),
            'usuario' => self::texto($fila[4] ?? null),
            'cargo' => self::texto($fila[5] ?? null),
            'tipo' => $tipo,
            'marca' => $marca,
            'modelo' => $modelo,
            'almacenamiento' => self::texto($fila[12] ?? null),
            'ram' => self::texto($fila[11] ?? null),
            'serial' => $serial,
            'imei_1' => $imeis[0] ?? null,
            'imei_2' => $imeis[1] ?? null,
            'direccion_mac' => self::mac($fila[22] ?? null) ?: self::mac($fila[23] ?? null),
            'fecha_entrega' => $fechaEntrega,
            'fecha_garantia' => $fechaGarantia,
            'observaciones' => $observaciones,
            'en_baja' => self::esBaja($observaciones),
            'en_traslado' => false,
            'en_disponible' => false,
        ];
    }

    private static function tipoMovil(?string $descripcion): ?string
    {
        $valor = mb_strtoupper(trim((string) $descripcion));

        return match (true) {
            $valor === 'CELULAR' => 'CELULAR',
            in_array($valor, ['TABLETA', 'TABLET', 'TABLET - CELULAR'], true) => 'TABLET',
            default => null,
        };
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private static function partirMarcaModelo(?string $valor): array
    {
        if ($valor === null || trim($valor) === '') {
            return [null, null];
        }

        $partes = preg_split('/\s+/', trim($valor), 2) ?: [];

        return [
            $partes[0] ?? null,
            isset($partes[1]) && $partes[1] !== '' ? $partes[1] : null,
        ];
    }

    private static function parseFecha(mixed $valor, mixed $observaciones = null): ?string
    {
        $texto = trim((string) ($valor ?? ''));

        if ($texto !== '') {
            if (preg_match('/^\d{4}$/', $texto) === 1) {
                return $texto.'-01-01';
            }

            try {
                return Carbon::parse($texto)->toDateString();
            } catch (\Throwable) {
                // Intentar desde observaciones.
            }
        }

        $obs = (string) ($observaciones ?? '');

        if (preg_match('/ADQUIRIDO\s+(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/iu', $obs, $m) === 1) {
            $anio = strlen($m[3]) === 2 ? '20'.$m[3] : $m[3];

            return sprintf('%04d-%02d-%02d', (int) $anio, (int) $m[2], (int) $m[1]);
        }

        if (preg_match('/ADQUIRIDO\s+(\d{1,2})\s+(?:DE\s+)?([A-ZÁÉÍÓÚ]+)\s+(\d{4})/iu', $obs, $m) === 1) {
            try {
                return Carbon::parse($m[1].' '.$m[2].' '.$m[3])->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private static function parseGarantia(mixed $valor, ?string $fechaAdquisicion): ?string
    {
        $texto = trim((string) ($valor ?? ''));

        if ($texto === '') {
            return null;
        }

        $normalizado = mb_strtoupper($texto);

        if (in_array($normalizado, ['CADUCADO', 'N/A', 'NA', 'NO APLICA'], true)) {
            return $fechaAdquisicion
                ? Carbon::parse($fechaAdquisicion)->addYears(3)->toDateString()
                : null;
        }

        try {
            return Carbon::parse($texto)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private static function esBaja(?string $observaciones): bool
    {
        $texto = mb_strtolower((string) $observaciones);

        return Str::contains($texto, ['dado de baja', 'dada de baja', 'da de baja', 'dados de baja']);
    }

    /**
     * @return list<string>
     */
    private static function extraerImeis(?string $observaciones): array
    {
        if ($observaciones === null) {
            return [];
        }

        preg_match_all('/\b(\d{15})\b/', $observaciones, $coincidencias);

        return array_values(array_unique($coincidencias[1] ?? []));
    }

    private static function texto(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim(preg_replace("/[ \t]+/u", ' ', str_replace(["\r\n", "\r"], "\n", (string) $valor)) ?? '');

        if ($texto === '') {
            return null;
        }

        return TextoInventario::texto($texto);
    }

    private static function textoLargo(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    private static function email(mixed $valor): ?string
    {
        $texto = trim((string) ($valor ?? ''));

        return $texto === '' ? null : mb_strtolower($texto);
    }

    private static function mac(mixed $valor): ?string
    {
        $texto = trim(preg_replace('/\s+/u', '', (string) ($valor ?? '')) ?? '');

        if ($texto === '') {
            return null;
        }

        return MacAddress::format($texto);
    }
}
