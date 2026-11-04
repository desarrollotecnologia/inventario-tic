<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RelacionInventarioController extends Controller
{
    public function index(): View
    {
        return view('relacion.index', [
            'submodules' => [
                [
                    'title' => 'Equipos de Cómputo',
                    'description' => 'Registra y consulta PCs, laptops y servidores.',
                    'route' => 'equipos-computo.index',
                    'ready' => true,
                ],
                [
                    'title' => 'Impresoras',
                    'description' => 'Inventario de impresoras y multifuncionales.',
                    'route' => 'impresoras.index',
                    'ready' => true,
                ],
                [
                    'title' => 'Radios de Comunicación',
                    'description' => 'Inventario de radios, antenas, amplificadores y filtros.',
                    'route' => 'radios.index',
                    'ready' => true,
                ],
                [
                    'title' => 'Celulares y Tablets',
                    'description' => 'Inventario de celulares, tablets y líneas móviles.',
                    'route' => 'celulares.index',
                    'ready' => true,
                ],
                [
                    'title' => 'Biométricos',
                    'description' => 'Inventario de lectores biométricos y control de acceso.',
                    'route' => 'biometricos.index',
                    'ready' => true,
                ],
                [
                    'title' => 'Lectores Códigos de Barras',
                    'description' => 'Inventario de lectores de códigos de barras.',
                    'route' => 'lectores-codigos-barras.index',
                    'ready' => true,
                ],
            ],
        ]);
    }
}
