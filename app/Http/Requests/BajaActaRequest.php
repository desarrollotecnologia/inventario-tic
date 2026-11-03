<?php

namespace App\Http\Requests;

use App\Support\BajaActaFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BajaActaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fecha' => now()->toDateString(),
            'acta_numero' => BajaActaFields::siguienteActaNumero(),
            'solicitante_departamento' => BajaActaFields::departamento(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'acta_numero' => ['required', 'string', 'max:50'],
            'fecha' => ['required', 'date'],
            'solicitante_nombre' => ['required', 'string', 'max:120'],
            'solicitante_cargo' => ['required', 'string', Rule::in(BajaActaFields::cargos())],
            'solicitante_departamento' => ['required', 'string', Rule::in([BajaActaFields::departamento()])],
            'informacion' => ['required', 'string', 'max:2000'],
            'motivo_baja' => ['required', 'string', 'max:5000'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'adjunto_acta' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'adjunto_evidencia' => ['nullable', 'array', 'max:20'],
            'adjunto_evidencia.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'adjunto_soporte' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'acta_numero' => 'número de acta',
            'fecha' => 'fecha',
            'solicitante_nombre' => 'nombre completo',
            'solicitante_cargo' => 'cargo',
            'solicitante_departamento' => 'departamento',
            'informacion' => 'información del activo',
            'motivo_baja' => 'motivo de la baja',
            'observaciones' => 'observaciones',
            'adjunto_acta' => 'firma del solicitante',
            'adjunto_evidencia' => 'evidencia fotográfica',
            'adjunto_evidencia.*' => 'evidencia fotográfica',
            'adjunto_soporte' => 'soporte del diagnóstico',
        ];
    }
}
