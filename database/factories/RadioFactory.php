<?php

namespace Database\Factories;

use App\Models\Radio;
use App\Support\EquipoComputoFields;
use App\Support\RadioFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Radio>
 */
class RadioFactory extends Factory
{
    protected $model = Radio::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item' => (string) fake()->unique()->numberBetween(1, 9999),
            'departamento' => fake()->randomElement(EquipoComputoFields::departamentos()),
            'area' => fake()->randomElement(EquipoComputoFields::areas()),
            'usuario' => fake()->name(),
            'cargo' => 'Analista',
            'tipo' => fake()->randomElement(RadioFields::tipos()),
            'marca_referencia' => fake()->randomElement(RadioFields::marcasCatalogo()),
            'modelo' => 'DP'.fake()->numberBetween(4000, 4800),
            'serial' => strtoupper(fake()->unique()->bothify('??########')),
            'fecha_adquisicion' => fake()->optional()->date(),
        ];
    }
}
