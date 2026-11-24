<?php

namespace App\Support;

use App\Models\Biometrico;
use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Impresora;
use App\Models\LectorCodigoBarras;
use App\Models\Radio;
use Illuminate\Support\Collection;

final class ActaInventarioCatalogo
{
    public const CARGO_ENTREGA = 'Coordinador TIC Colbeef SAS';

    /**
     * @return list<array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    public static function equipos(): array
    {
        return collect()
            ->merge(self::equiposComputo())
            ->merge(self::celulares())
            ->merge(self::radios())
            ->merge(self::impresoras())
            ->merge(self::biometricos())
            ->merge(self::lectores())
            ->filter(fn (array $item): bool => filled($item['serial']) || filled($item['descripcion']) || filled($item['marca']))
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function equiposComputo(): Collection
    {
        return EquipoComputo::query()
            ->orderBy('descripcion')
            ->get(['descripcion', 'marca_referencia', 'service_tag_product_name', 'serial_adicional'])
            ->map(fn (EquipoComputo $equipo): array => self::item(
                (string) ($equipo->descripcion ?: 'EQUIPO DE COMPUTO'),
                (string) ($equipo->marca_referencia ?: ''),
                '',
                $equipo->serialMostrado(),
            ));
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function celulares(): Collection
    {
        return Celular::query()
            ->orderBy('tipo')
            ->get(['tipo', 'marca', 'modelo', 'serial', 'imei_1', 'imei_2'])
            ->map(function (Celular $celular): array {
                $serial = collect([
                    filled($celular->serial) ? 'CELULAR: '.$celular->serial : null,
                    filled($celular->imei_1) ? 'IMEI: '.$celular->imei_1 : null,
                    filled($celular->imei_2) ? 'IMEI 2: '.$celular->imei_2 : null,
                ])->filter()->implode(' | ');

                return self::item(
                    (string) ($celular->tipo ?: 'CELULAR'),
                    (string) ($celular->marca ?: ''),
                    (string) ($celular->modelo ?: ''),
                    $serial !== '' ? $serial : $celular->serialMostrado(),
                );
            });
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function radios(): Collection
    {
        return Radio::query()
            ->orderBy('tipo')
            ->get(['tipo', 'marca_referencia', 'modelo', 'serial'])
            ->map(fn (Radio $radio): array => self::item(
                (string) ($radio->tipo ?: 'RADIO'),
                (string) ($radio->marca_referencia ?: ''),
                (string) ($radio->modelo ?: ''),
                $radio->serialMostrado(),
            ));
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function impresoras(): Collection
    {
        return Impresora::query()
            ->orderBy('tipo')
            ->get(['tipo', 'descripcion', 'modelo', 'serial'])
            ->map(fn (Impresora $impresora): array => self::item(
                (string) ($impresora->tipo ?: 'IMPRESORA'),
                (string) ($impresora->descripcion ?: ''),
                (string) ($impresora->modelo ?: ''),
                $impresora->serialMostrado(),
            ));
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function biometricos(): Collection
    {
        return Biometrico::query()
            ->orderBy('marca_referencia')
            ->get(['marca_referencia', 'modelo', 'serial'])
            ->map(fn (Biometrico $biometrico): array => self::item(
                'BIOMETRICO',
                (string) ($biometrico->marca_referencia ?: ''),
                (string) ($biometrico->modelo ?: ''),
                $biometrico->serialMostrado(),
            ));
    }

    /**
     * @return Collection<int, array{descripcion: string, marca: string, modelo: string, serial: string}>
     */
    private static function lectores(): Collection
    {
        return LectorCodigoBarras::query()
            ->orderBy('marca_referencia')
            ->get(['marca_referencia', 'modelo', 'serial'])
            ->map(fn (LectorCodigoBarras $lector): array => self::item(
                'LECTOR CODIGO BARRAS',
                (string) ($lector->marca_referencia ?: ''),
                (string) ($lector->modelo ?: ''),
                $lector->serialMostrado(),
            ));
    }

    /**
     * Si solo hay marca, el modelo toma el mismo valor; si hay modelo propio, se respeta.
     *
     * @return array{descripcion: string, marca: string, modelo: string, serial: string}
     */
    private static function item(string $descripcion, string $marca, string $modelo, string $serial): array
    {
        $marca = trim($marca);
        $modelo = trim($modelo);

        return [
            'descripcion' => $descripcion,
            'marca' => $marca,
            'modelo' => $modelo !== '' ? $modelo : $marca,
            'serial' => $serial,
        ];
    }
}
