<?php

namespace App\Support;

use App\Models\LectorCodigoBarras;

class LectorCodigoBarrasFields
{
    /**
     * @return array<string, array<string, array{label: string, type: string, options?: list<string>, allow_custom?: bool}>>
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
                'codigo_contable' => ['label' => 'Código contable', 'type' => 'text'],
                'marca_referencia' => ['label' => 'Marca y Referencia', 'type' => 'combobox', 'options' => self::marcas(), 'allow_custom' => true],
                'modelo' => ['label' => 'Modelo', 'type' => 'combobox', 'options' => self::modelos(), 'allow_custom' => true],
                'serial' => ['label' => 'Serial', 'type' => 'text'],
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
                $rules[$name] = match ($field['type']) {
                    'textarea' => ['nullable', 'string', 'max:2000'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    public static function marcasCatalogo(): array
    {
        return [
            'Zebra',
            'Honeywell',
            'Datalogic',
            'Symbol',
            'Motorola',
            'Socket Mobile',
            'Newland',
            'CipherLab',
        ];
    }

    /**
     * @return list<string>
     */
    public static function marcas(): array
    {
        return self::mezclarOpciones(self::marcasCatalogo(), LectorCodigoBarras::query()->pluck('marca_referencia'));
    }

    /**
     * @return list<string>
     */
    public static function modelos(): array
    {
        return self::mezclarOpciones([], LectorCodigoBarras::query()->pluck('modelo'));
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
