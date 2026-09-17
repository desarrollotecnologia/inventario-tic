<?php

namespace App\Http\Requests;

use App\Support\CelularFields;
use App\Support\TextoInventario;
use Illuminate\Foundation\Http\FormRequest;

class CelularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(TextoInventario::mayusculas($this->all(), [
            '_token',
            '_method',
            'fecha_entrega',
            'fecha_garantia',
            'tipo',
        ]));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return CelularFields::rules();
    }
}
