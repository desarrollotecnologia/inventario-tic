<?php

namespace App\Support;

use App\Models\Impresora;

class ImpresoraFields
{
    /**
     * @return array<string, array<string, array{label: string, type: string, options?: list<string>, allow_custom?: bool, placeholder?: string}>>
     */
    public static function groups(): array
    {
        return [
            'Ubicación y responsable' => [
                'departamento' => ['label' => 'Departamento', 'type' => 'combobox', 'options' => EquipoComputoFields::departamentos()],
                'area' => ['label' => 'Área', 'type' => 'combobox', 'options' => EquipoComputoFields::areas()],
                'usuario' => ['label' => 'Usuario', 'type' => 'combobox', 'options' => EquipoComputoFields::usuarios(), 'allow_custom' => true],
                'cargo' => ['label' => 'Cargo', 'type' => 'combobox', 'options' => EquipoComputoFields::cargos(), 'allow_custom' => true],
            ],
            'Equipo' => [
                'descripcion' => ['label' => 'Descripción', 'type' => 'combobox', 'options' => self::descripciones(), 'allow_custom' => true],
                'modelo' => ['label' => 'Modelo', 'type' => 'combobox', 'options' => self::modelos(), 'allow_custom' => true],
                'serial' => ['label' => 'Serial', 'type' => 'text'],
                'tipo' => ['label' => 'Tipo', 'type' => 'combobox', 'options' => self::tipos(), 'allow_custom' => true],
            ],
            'Observaciones' => [
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
                $rules[$name] = ['nullable', 'string', 'max:'.($field['type'] === 'textarea' ? '2000' : '255')];
            }
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    public static function tiposCatalogo(): array
    {
        return [
            'Láser',
            'Transferencia térmica',
            'Inyección',
            'Cinta',
        ];
    }

    /**
     * @return list<string>
     */
    public static function tipos(): array
    {
        return self::mezclarOpciones(self::tiposCatalogo(), Impresora::query()->pluck('tipo'));
    }

    /**
     * @return list<string>
     */
    public static function descripcionesCatalogo(): array
    {
        return [
            'HP',
            'Epson',
            'Brother',
            'Canon',
            'Kyocera',
            'Xerox',
            'Ricoh',
            'Lexmark',
            'Samsung',
            'OKI',
        ];
    }

    /**
     * @return list<string>
     */
    public static function descripciones(): array
    {
        return self::mezclarOpciones(self::descripcionesCatalogo(), Impresora::query()->pluck('descripcion'));
    }

    /**
     * @return list<string>
     */
    public static function modelos(): array
    {
        return self::mezclarOpciones([], Impresora::query()->pluck('modelo'));
    }

    /**
     * @param  list<string>  $catalogo
     * @param  iterable<int, mixed>  $registrados
     * @return list<string>
     */
    private static function mezclarOpciones(array $catalogo, iterable $registrados): array
    {
        return collect($catalogo)
            ->merge($registrados)
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }
}
