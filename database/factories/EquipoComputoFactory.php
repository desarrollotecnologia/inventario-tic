<?php

namespace Database\Factories;

use App\Models\EquipoComputo;
use App\Support\EquipoComputoFields;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EquipoComputo>
 */
class EquipoComputoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item' => (string) fake()->unique()->numberBetween(1, 9999),
            'codigo' => 'EQ-'.fake()->unique()->numerify('####'),
            'departamento' => fake()->randomElement(EquipoComputoFields::departamentos()),
            'area' => fake()->randomElement(EquipoComputoFields::areas()),
            'usuario' => fake()->name(),
            'cargo' => 'Analista',
            'descripcion' => fake()->randomElement(EquipoComputoFields::descripciones()),
            'marca_referencia' => 'Dell OptiPlex',
            'nombre_equipo' => 'PC-'.fake()->numerify('##'),
        ];
    }
}
