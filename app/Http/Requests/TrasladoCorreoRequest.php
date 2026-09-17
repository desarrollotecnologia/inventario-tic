<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrasladoCorreoRequest extends FormRequest
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
            'correos' => ['required', 'string'],
            'destinatarios' => ['required', 'array', 'min:1'],
            'destinatarios.*' => ['required', 'email'],
            'firma_id' => ['required', 'exists:firmas,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'correos' => 'correos',
            'destinatarios' => 'correos',
            'destinatarios.*' => 'correo',
            'firma_id' => 'firma',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destinatarios.required' => 'Indica al menos un correo.',
            'destinatarios.min' => 'Indica al menos un correo.',
            'firma_id.required' => 'Selecciona o crea una firma para enviar el correo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $destinatarios = collect(preg_split('/[\s,;]+/', (string) $this->input('correos', ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $correo): string => trim($correo))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->merge([
            'destinatarios' => $destinatarios,
        ]);
    }
}
