<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'modules' => [
                [
                    'title' => 'Relación Inventario Sistemas',
                    'description' => 'Consulta y gestiona la relación de activos de sistemas.',
                    'route' => 'modules.relacion',
                    'icon' => 'list',
                ],
                [
                    'title' => 'Traslados',
                    'description' => 'Registra y sigue los movimientos de equipos entre áreas.',
                    'route' => 'modules.traslados',
                    'icon' => 'move',
                ],
                [
                    'title' => 'Baja de Equipos',
                    'description' => 'Da de baja equipos que ya no forman parte del inventario.',
                    'route' => 'modules.bajas',
                    'icon' => 'archive',
                ],
            ],
        ]);
    }
}
