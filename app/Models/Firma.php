<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'nombre',
    'contenido',
    'imagen',
])]
class Firma extends Model
{
    public function urlImagen(): ?string
    {
        return filled($this->imagen) ? Storage::disk('public')->url($this->imagen) : null;
    }

    public function rutaImagen(): ?string
    {
        return filled($this->imagen) ? Storage::disk('public')->path($this->imagen) : null;
    }
}
