<?php

namespace Database\Factories;

use App\Models\Celular;
use App\Support\CelularFields;
use App\Support\EquipoComputoFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Celular>
 */
class CelularFactory extends Factory
{
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
            'tipo' => fake()->randomElement(CelularFields::tipos()),
            'marca' => fake()->randomElement(CelularFields::marcas()),
            'modelo' => 'Galaxy A'.fake()->numberBetween(10, 50),
            'serial' => strtoupper(fake()->unique()->bothify('??########')),
            'imei_1' => fake()->unique()->numerify('###############'),
            'numero_celular' => '3'.fake()->numerify('#########'),
        ];
    }
}
