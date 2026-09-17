<?php

namespace App\Http\Requests;

use App\Support\ImpresoraFields;
use App\Support\TextoInventario;
use Illuminate\Foundation\Http\FormRequest;

class ImpresoraRequest extends FormRequest
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
        ]));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return ImpresoraFields::rules();
    }
}
