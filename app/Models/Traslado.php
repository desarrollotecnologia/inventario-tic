<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipo_computo_id',
    'celular_id',
    'radio_id',
    'impresora_id',
    'fecha',
    'usuario_entrega',
    'cargo_entrega',
    'departamento_entrega',
    'area_entrega',
    'codigo',
    'tipo',
    'descripcion_equipo',
    'serial',
    'usuario_recibe',
    'cargo_recibe',
    'departamento_recibe',
    'area_recibe',
    'observaciones',
])]
class Traslado extends Model
{
    /**
     * @return BelongsTo<EquipoComputo, $this>
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(EquipoComputo::class, 'equipo_computo_id');
    }

    /**
     * @return BelongsTo<Celular, $this>
     */
    public function celular(): BelongsTo
    {
        return $this->belongsTo(Celular::class);
    }

    /**
     * @return BelongsTo<Radio, $this>
     */
    public function radio(): BelongsTo
    {
        return $this->belongsTo(Radio::class);
    }

    /**
     * @return BelongsTo<Impresora, $this>
     */
    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'correo_enviado_at' => 'datetime',
        ];
    }

    public function codigoMostrado(): string
    {
        if ($this->celular_id || $this->radio_id || $this->impresora_id) {
            return '—';
        }

        return (string) ($this->codigo ?: '—');
    }
}
