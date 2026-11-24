<?php

namespace Database\Factories;

use App\Models\ActaEntrega;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActaEntrega>
 */
class ActaEntregaFactory extends Factory
{
    protected $model = ActaEntrega::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ciudad' => 'Floridablanca',
            'fecha' => now()->toDateString(),
            'representante' => 'Leonardo Lara',
            'destino' => 'Juan Pérez, área Contabilidad',
            'proposito' => 'para el desempeño de sus funciones',
            'entregado_por_nombre' => 'Felipe Suarez Castaño',
            'entregado_por_cargo' => 'Coordinador TIC Colbeef SAS',
            'recibido_por_nombre' => 'Juan Pérez',
            'recibido_por_documento' => '1098765432',
            'equipos' => [
                [
                    'cantidad' => 1,
                    'descripcion' => 'PORTATIL',
                    'marca' => 'DELL',
                    'modelo' => 'LATITUDE 5420',
                    'serial' => 'ABC123456',
                    'observaciones' => null,
                ],
            ],
        ];
    }
}
