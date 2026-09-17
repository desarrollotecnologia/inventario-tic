<?php

namespace App\Http\Requests;

use App\Support\EquipoComputoFields;
use App\Support\ProcesadorEquipo;
use App\Support\TextoInventario;
use Illuminate\Foundation\Http\FormRequest;

class EquipoComputoRequest extends FormRequest
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
            'fecha_garantia',
            'cuenta_office365',
            'contrasena_deslock_eset',
        ]));

        if ($this->input('codigo') === '') {
            $this->merge(['codigo' => null]);
        }

        if ($this->input('serial_adicional') === '') {
            $this->merge(['serial_adicional' => null]);
        }

        if ($this->exists('procesador_modelo')) {
            $procesador = ProcesadorEquipo::compose(
                $this->input('procesador_modelo'),
                $this->input('procesador_version'),
                $this->input('procesador_velocidad'),
            );

            $this->merge([
                'procesador' => $procesador === null ? null : TextoInventario::texto($procesador),
            ]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $equipoId = $this->route('equipoComputo')?->id;

        return EquipoComputoFields::rules($equipoId);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe un equipo con este código contable.',
            'foto_equipo.image' => 'La foto del equipo debe ser una imagen.',
            'foto_equipo.mimes' => 'La foto del equipo debe ser JPG, PNG o WEBP.',
            'foto_equipo.max' => 'La foto del equipo no puede superar 5 MB.',
            'foto_etiqueta.image' => 'La foto de la etiqueta debe ser una imagen.',
            'foto_etiqueta.mimes' => 'La foto de la etiqueta debe ser JPG, PNG o WEBP.',
            'foto_etiqueta.max' => 'La foto de la etiqueta no puede superar 5 MB.',
            'foto_etiqueta_procesador.image' => 'La foto de la etiqueta del procesador debe ser una imagen.',
            'foto_etiqueta_procesador.mimes' => 'La foto de la etiqueta del procesador debe ser JPG, PNG o WEBP.',
            'foto_etiqueta_procesador.max' => 'La foto de la etiqueta del procesador no puede superar 5 MB.',
        ];
    }
}
