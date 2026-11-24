<?php

namespace App\Support;

final class ActaEquiposRules
{
    /**
     * @return array<string, mixed>
     */
    public static function base(bool $propositoRequerido = true): array
    {
        return [
            'ciudad' => ['required', 'string', 'max:120'],
            'fecha' => ['required', 'date'],
            'representante' => ['required', 'string', 'max:160'],
            'destino' => ['required', 'string', 'max:200'],
            'proposito' => [$propositoRequerido ? 'required' : 'nullable', 'string', 'max:255'],
            'entregado_por_nombre' => ['required', 'string', 'max:160'],
            'entregado_por_cargo' => ['required', 'string', 'max:160'],
            'recibido_por_nombre' => ['required', 'string', 'max:160'],
            'recibido_por_documento' => ['nullable', 'string', 'max:40'],
            'equipos' => ['required', 'array', 'min:1'],
            'equipos.*.cantidad' => ['required', 'integer', 'min:1', 'max:9999'],
            'equipos.*.descripcion' => ['required', 'string', 'max:160'],
            'equipos.*.marca' => ['required', 'string', 'max:120'],
            'equipos.*.modelo' => ['nullable', 'string', 'max:120'],
            'equipos.*.serial' => ['required', 'string', 'max:255'],
            'equipos.*.observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'ciudad' => 'ciudad',
            'fecha' => 'fecha',
            'representante' => 'representante',
            'destino' => 'destino',
            'proposito' => 'propósito',
            'entregado_por_nombre' => 'persona quien entrega',
            'entregado_por_cargo' => 'cargo quien entrega',
            'recibido_por_nombre' => 'persona quien recibe',
            'recibido_por_documento' => 'documento quien recibe',
            'equipos' => 'equipos',
            'equipos.*.cantidad' => 'cantidad',
            'equipos.*.descripcion' => 'descripción',
            'equipos.*.marca' => 'marca',
            'equipos.*.modelo' => 'modelo',
            'equipos.*.serial' => 'serial',
            'equipos.*.observaciones' => 'observaciones',
        ];
    }
}
