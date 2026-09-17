<?php

namespace App\Http\Controllers;

use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Impresora;
use App\Models\Radio;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * @var array<string, array{title: string, campo: string|null, vacio: string}>
     */
    private array $modules = [
        'baja-de-equipos' => [
            'title' => 'Baja de Equipos',
            'campo' => 'en_baja',
            'vacio' => 'Aún no hay equipos marcados para baja.',
        ],
    ];

    public function show(string $module): View|RedirectResponse
    {
        if (! isset($this->modules[$module])) {
            return redirect()->route('home');
        }

        $config = $this->modules[$module];

        $equipos = $config['campo'] === null
            ? collect()
            : EquipoComputo::query()
                ->where($config['campo'], true)
                ->get()
                ->sortBy(fn (EquipoComputo $equipo): int => (int) $equipo->item)
                ->values();

        $celulares = $config['campo'] === null
            ? collect()
            : Celular::query()
                ->where($config['campo'], true)
                ->get()
                ->sortBy(fn (Celular $celular): int => (int) $celular->item)
                ->values();

        $radios = $config['campo'] === null
            ? collect()
            : Radio::query()
                ->where($config['campo'], true)
                ->get()
                ->sortBy(fn (Radio $radio): int => (int) $radio->item)
                ->values();

        $impresoras = $config['campo'] === null
            ? collect()
            : Impresora::query()
                ->where($config['campo'], true)
                ->get()
                ->sortBy(fn (Impresora $impresora): int => (int) $impresora->item)
                ->values();

        return view('modules.show', [
            'title' => $config['title'],
            'equipos' => $equipos,
            'celulares' => $celulares,
            'radios' => $radios,
            'impresoras' => $impresoras,
            'vacio' => $config['vacio'],
        ]);
    }
}
