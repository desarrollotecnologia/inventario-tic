<?php

namespace App\Support;

use App\Models\Celular;

class CelularFields
{
    /**
     * @return array<string, array<string, array{label: string, type: string, options?: list<string>, allow_custom?: bool, options_by_marca?: bool}>>
     */
    public static function groups(): array
    {
        return [
            'Ubicación y responsable' => [
                'departamento' => ['label' => 'Departamento', 'type' => 'combobox', 'options' => EquipoComputoFields::departamentos(), 'allow_custom' => true],
                'area' => ['label' => 'Área', 'type' => 'combobox', 'options' => EquipoComputoFields::areas(), 'allow_custom' => true],
                'usuario' => ['label' => 'Usuario', 'type' => 'combobox', 'options' => EquipoComputoFields::usuarios(), 'allow_custom' => true],
                'cargo' => ['label' => 'Cargo', 'type' => 'combobox', 'options' => EquipoComputoFields::cargos(), 'allow_custom' => true],
            ],
            'Equipo' => [
                'tipo' => ['label' => 'Tipo', 'type' => 'combobox', 'options' => self::tipos(), 'allow_custom' => true],
                'marca' => ['label' => 'Marca', 'type' => 'combobox', 'options' => self::marcas(), 'allow_custom' => true],
                'modelo' => ['label' => 'Modelo', 'type' => 'combobox', 'options_by_marca' => true, 'allow_custom' => true],
                'almacenamiento' => ['label' => 'Almacenamiento', 'type' => 'combobox', 'options' => self::almacenamientos(), 'allow_custom' => true],
                'ram' => ['label' => 'RAM', 'type' => 'combobox', 'options' => self::rams(), 'allow_custom' => true],
                'ram_expansiva' => ['label' => 'RAM expansiva', 'type' => 'combobox', 'options' => self::ramsExpansivas(), 'allow_custom' => true],
                'serial' => ['label' => 'Serial', 'type' => 'text'],
                'imei_1' => ['label' => 'IMEI 1', 'type' => 'text'],
                'imei_2' => ['label' => 'IMEI 2', 'type' => 'text'],
                'numero_celular' => ['label' => 'Número celular', 'type' => 'text'],
                'direccion_mac' => ['label' => 'Dirección MAC', 'type' => 'text'],
            ],
            'Fechas y observaciones' => [
                'fecha_entrega' => ['label' => 'Fecha de entrega', 'type' => 'date'],
                'antiguedad' => ['label' => 'Antigüedad', 'type' => 'computed'],
                'fecha_garantia' => ['label' => 'Fecha garantía', 'type' => 'date'],
                'observaciones' => ['label' => 'Observaciones', 'type' => 'textarea'],
            ],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        $rules = [];

        foreach (self::groups() as $fields) {
            foreach ($fields as $name => $field) {
                if (in_array($field['type'], ['readonly', 'computed'], true)) {
                    continue;
                }

                $rules[$name] = match ($field['type']) {
                    'date' => ['nullable', 'date'],
                    default => ['nullable', 'string', 'max:'.($field['type'] === 'textarea' ? '2000' : '255')],
                };
            }
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    public static function tipos(): array
    {
        return collect(['Celular', 'Tablet', 'CELULAR', 'TABLET'])
            ->merge(Celular::query()->pluck('tipo'))
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * @return array<string, list<string>>
     */
    public static function modelosPorMarca(): array
    {
        $catalogo = self::modelosCatalogo();

        Celular::query()
            ->whereNotNull('marca')
            ->whereNotNull('modelo')
            ->get(['marca', 'modelo'])
            ->each(function (Celular $celular) use (&$catalogo): void {
                $marca = trim((string) $celular->marca);
                $modelo = trim((string) $celular->modelo);

                if ($marca === '' || $modelo === '') {
                    return;
                }

                $catalogo[$marca] ??= [];

                foreach ($catalogo[$marca] as $existente) {
                    if (strcasecmp($existente, $modelo) === 0) {
                        return;
                    }
                }

                $catalogo[$marca][] = $modelo;
            });

        foreach ($catalogo as $marca => $modelos) {
            natcasesort($modelos);
            $catalogo[$marca] = array_values($modelos);
        }

        ksort($catalogo, SORT_NATURAL | SORT_FLAG_CASE);

        return $catalogo;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function modelosCatalogo(): array
    {
        return [
            'Samsung' => ['Galaxy A06', 'Galaxy A16', 'Galaxy A26', 'Galaxy A35', 'Galaxy A36', 'Galaxy A54', 'Galaxy A55', 'Galaxy A56', 'Galaxy S23', 'Galaxy S24', 'Galaxy S24 Ultra', 'Galaxy S25', 'Galaxy S25 Ultra', 'Galaxy Tab A9', 'Galaxy Tab S9', 'Galaxy Tab S10', 'Galaxy Z Flip 6', 'Galaxy Z Fold 6'],
            'Apple' => ['iPhone 13', 'iPhone 13 Pro', 'iPhone 14', 'iPhone 14 Pro', 'iPhone 15', 'iPhone 15 Pro', 'iPhone 15 Pro Max', 'iPhone 16', 'iPhone 16 Pro', 'iPhone 16 Pro Max', 'iPhone SE', 'iPad', 'iPad Air', 'iPad mini', 'iPad Pro'],
            'Xiaomi' => ['Redmi 13C', 'Redmi 14C', 'Redmi Note 13', 'Redmi Note 13 Pro', 'Redmi Note 14', 'Poco X6', 'Xiaomi 14'],
            'Motorola' => ['Moto G24', 'Moto G54', 'Moto G84', 'Moto G85', 'Edge 40', 'Edge 50'],
            'Huawei' => ['nova 12', 'nova 13', 'Pura 70', 'Y9a'],
            'Honor' => ['Honor 200', 'Magic 6', 'X8b', 'X9b'],
            'Oppo' => ['A3', 'A78', 'Reno 11', 'Reno 12'],
            'Realme' => ['12 Pro', 'C67', 'C75', 'Note 50'],
            'Nokia' => ['C32', 'G42', '2780 Flip'],
            'Google' => ['Pixel 8', 'Pixel 8a', 'Pixel 9', 'Pixel 9 Pro'],
            'Sony' => ['Xperia 1 VI', 'Xperia 10 VI'],
            'TCL' => ['40 SE', '50', '50 XL'],
            'ZTE' => ['Blade A35', 'Blade V50'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function marcas(): array
    {
        return [
            'Samsung',
            'Apple',
            'Xiaomi',
            'Motorola',
            'Huawei',
            'Honor',
            'Oppo',
            'Realme',
            'Nokia',
            'Google',
            'Sony',
            'TCL',
            'ZTE',
        ];
    }

    /**
     * @return list<string>
     */
    public static function almacenamientos(): array
    {
        return [
            '32 GB',
            '64 GB',
            '128 GB',
            '256 GB',
            '512 GB',
            '1 TB',
        ];
    }

    /**
     * @return list<string>
     */
    public static function rams(): array
    {
        return [
            '2 GB',
            '3 GB',
            '4 GB',
            '6 GB',
            '8 GB',
            '12 GB',
            '16 GB',
        ];
    }

    /**
     * @return list<string>
     */
    public static function ramsExpansivas(): array
    {
        return [
            'No',
            'Sí',
            '2 GB',
            '4 GB',
            '6 GB',
            '8 GB',
            '12 GB',
        ];
    }
}
