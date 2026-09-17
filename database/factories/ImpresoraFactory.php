<?php

namespace Database\Factories;

use App\Models\Impresora;
use App\Support\EquipoComputoFields;
use App\Support\ImpresoraFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Impresora>
 */
class ImpresoraFactory extends Factory
{
    protected $model = Impresora::class;

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
            'descripcion' => fake()->randomElement(ImpresoraFields::descripcionesCatalogo()),
            'modelo' => 'M'.fake()->numberBetween(400, 500),
            'serial' => strtoupper(fake()->unique()->bothify('??########')),
            'tipo' => fake()->randomElement(ImpresoraFields::tiposCatalogo()),
        ];
    }
}
