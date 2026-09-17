<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'nombre',
        'semana',
        'fecha_entrega',
        'responsables',
        'estado',
        'observaciones',
    ];
}
