<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'nombre',
])]
class BajaSolicitante extends Model
{
    /**
     * @return list<string>
     */
    public static function nombres(): array
    {
        return static::query()
            ->orderBy('nombre')
            ->pluck('nombre')
            ->all();
    }
}
