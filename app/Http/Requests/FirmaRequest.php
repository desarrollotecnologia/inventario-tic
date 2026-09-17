<?php

namespace App\Http\Requests;

use App\Support\FirmaHtml;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FirmaRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string', 'max:50000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'contenido' => 'firma',
            'imagen' => 'imagen',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contenido.required' => 'Escribe o pega la firma, incluyendo la imagen si la tienes.',
            'nombre.required' => 'Indica el nombre de quien es la firma.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $contenido = (string) $this->input('contenido', '');

            if (! FirmaHtml::tieneContenido($contenido) && ! $this->hasFile('imagen')) {
                $validator->errors()->add('contenido', 'Escribe o pega la firma, incluyendo la imagen si la tienes.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->input('nombre', '')),
            'contenido' => (string) $this->input('contenido', ''),
        ]);
    }
}
