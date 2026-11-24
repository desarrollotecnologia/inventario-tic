<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ActasController extends Controller
{
    public function index(): View
    {
        return view('actas.index', [
            'submodules' => [
                [
                    'title' => 'Acta de Entrega',
                    'description' => 'Genera y consulta actas de entrega de equipos.',
                    'route' => 'actas.entrega',
                    'ready' => true,
                ],
                [
                    'title' => 'Acta de Salida',
                    'description' => 'Genera y consulta actas de salida de equipos.',
                    'route' => 'actas.salida',
                    'ready' => true,
                ],
            ],
        ]);
    }
}
