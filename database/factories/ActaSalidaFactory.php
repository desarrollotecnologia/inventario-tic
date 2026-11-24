<?php

namespace Database\Factories;

use App\Models\ActaSalida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActaSalida>
 */
class ActaSalidaFactory extends Factory
{
    protected $model = ActaSalida::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ciudad' => 'Floridablanca',
            'fecha' => now()->toDateString(),
            'representante' => 'Leonardo Lara',
            'destino' => 'LINEA DATASCAN',
            'proposito' => 'para realizar diagnostico y reparación',
            'entregado_por_nombre' => 'Felipe Suarez Castaño',
            'entregado_por_cargo' => 'Coordinador TIC Colbeef SAS',
            'recibido_por_nombre' => 'Responsable LINEA DATASCAN',
            'recibido_por_documento' => null,
            'equipos' => [
                [
                    'cantidad' => 1,
                    'descripcion' => 'CELULAR MOVIL',
                    'marca' => 'MOTO G41',
                    'modelo' => 'XT2167-1',
                    'serial' => "CELULAR: ZY22GBLXMJ\nIMEI: 350077382282873",
                    'observaciones' => "Novedades:\nMODULO DE PANTALLA DEFECTUOSO, NO DA IMAGEN",
                ],
            ],
        ];
    }
}
