<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmaImagen
{
    public static function generar(string $texto): string
    {
        $firma = e($texto);
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="520" height="150" viewBox="0 0 520 150">
  <rect width="520" height="150" fill="#ffffff"/>
  <text x="24" y="95" font-family="Segoe Script, Brush Script MT, Lucida Handwriting, cursive" font-size="46" fill="#17823a">{$firma}</text>
</svg>
SVG;

        $ruta = 'firmas/'.Str::uuid().'.svg';
        Storage::disk('public')->put($ruta, $svg);

        return $ruta;
    }
}
