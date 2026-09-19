<?php

namespace Database\Factories;

use App\Models\Biometrico;
use App\Support\BiometricoFields;
use App\Support\EquipoComputoFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Biometrico>
 */
class BiometricoFactory extends Factory
{
    protected $model = Biometrico::class;

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
            'marca_referencia' => fake()->randomElement(BiometricoFields::marcasCatalogo()),
            'modelo' => 'MB'.fake()->numberBetween(100, 900),
            'serial' => strtoupper(fake()->unique()->bothify('BIO########')),
        ];
    }
}
