<?php

namespace App\Http\Requests;

use App\Support\BiometricoFields;
use App\Support\TextoInventario;
use Illuminate\Foundation\Http\FormRequest;

class BiometricoRequest extends FormRequest
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
            'fecha_adquisicion',
        ]));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return BiometricoFields::rules();
    }
}
