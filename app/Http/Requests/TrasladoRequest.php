<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrasladoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'traslados' => ['required', 'array', 'min:1'],
            'traslados.*.fecha' => ['required', 'date'],
            'traslados.*.usuario_recibe' => ['required', 'string', 'max:255'],
            'traslados.*.cargo_recibe' => ['required', 'string', 'max:255'],
            'traslados.*.departamento_recibe' => ['required', 'string', 'max:255'],
            'traslados.*.area_recibe' => ['required', 'string', 'max:255'],
            'traslados.*.observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'traslados' => 'traslados',
            'traslados.*.fecha' => 'fecha',
            'traslados.*.usuario_recibe' => 'usuario recibe',
            'traslados.*.cargo_recibe' => 'cargo recibe',
            'traslados.*.departamento_recibe' => 'departamento recibe',
            'traslados.*.area_recibe' => 'área recibe',
            'traslados.*.observaciones' => 'observaciones',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'traslados.required' => 'Completa al menos un equipo para aplicar el traslado.',
            'traslados.min' => 'Completa al menos un equipo para aplicar el traslado.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $traslados = collect($this->input('traslados', []))
            ->filter(fn (mixed $datos): bool => is_array($datos) && $this->filaConDatos($datos))
            ->all();

        $this->merge([
            'traslados' => $traslados,
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    private function filaConDatos(array $datos): bool
    {
        foreach (['usuario_recibe', 'cargo_recibe', 'area_recibe', 'departamento_recibe', 'observaciones'] as $campo) {
            if (filled($datos[$campo] ?? null)) {
                return true;
            }
        }

        return false;
    }
}
