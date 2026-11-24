<?php

namespace App\Http\Requests;

use App\Support\ActaEquiposRules;
use App\Support\ActaInventarioCatalogo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreActaEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'entregado_por_nombre' => (string) $this->input('representante', ''),
            'entregado_por_cargo' => ActaInventarioCatalogo::CARGO_ENTREGA,
            'recibido_por_nombre' => (string) $this->input('destino', ''),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ActaEquiposRules::base(propositoRequerido: false);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ActaEquiposRules::attributes();
    }
}
