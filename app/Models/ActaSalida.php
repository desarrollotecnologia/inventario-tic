<?php

namespace App\Models;

use Database\Factories\ActaSalidaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ciudad',
    'fecha',
    'representante',
    'destino',
    'proposito',
    'entregado_por_nombre',
    'entregado_por_cargo',
    'recibido_por_nombre',
    'recibido_por_documento',
    'equipos',
])]
class ActaSalida extends Model
{
    /** @use HasFactory<ActaSalidaFactory> */
    use HasFactory;

    protected $table = 'actas_salida';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'equipos' => 'array',
        ];
    }

    public function textoIntroduccion(): string
    {
        return 'COLBEEF S.A.S por medio de '.$this->representante
            .', hace entrega de los dispositivos detallados a continuación a la empresa '
            .$this->destino.' '.$this->proposito.'.';
    }

    public function fechaFormateada(): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $fecha = $this->fecha;

        if ($fecha === null) {
            return '';
        }

        return $this->ciudad.' '.$fecha->format('d').' '.$meses[(int) $fecha->format('n')].' '.$fecha->format('Y');
    }
}
