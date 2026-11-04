<?php

namespace Database\Factories;

use App\Models\LectorCodigoBarras;
use App\Support\EquipoComputoFields;
use App\Support\LectorCodigoBarrasFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LectorCodigoBarras>
 */
class LectorCodigoBarrasFactory extends Factory
{
    protected $model = LectorCodigoBarras::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item' => (string) fake()->unique()->numberBetween(1, 9999),
            'codigo_contable' => strtoupper(fake()->unique()->bothify('CB####')),
            'departamento' => fake()->randomElement(EquipoComputoFields::departamentos()),
            'area' => fake()->randomElement(EquipoComputoFields::areas()),
            'usuario' => fake()->name(),
            'cargo' => 'Analista',
            'marca_referencia' => fake()->randomElement(LectorCodigoBarrasFields::marcasCatalogo()),
            'modelo' => 'DS'.fake()->numberBetween(1000, 9999),
            'serial' => strtoupper(fake()->unique()->bothify('LCB########')),
        ];
    }
}
