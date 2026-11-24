<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActaEntregaRequest;
use App\Http\Requests\UpdateActaEntregaRequest;
use App\Models\ActaEntrega;
use App\Support\ActaInventarioCatalogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActaEntregaController extends Controller
{
    public function index(): View
    {
        return view('actas.entrega.index', [
            'actas' => ActaEntrega::query()->latest('fecha')->latest('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('actas.entrega.form', [
            'acta' => new ActaEntrega([
                'ciudad' => 'Floridablanca',
                'fecha' => now()->toDateString(),
                'proposito' => 'para el desempeño de sus funciones',
                'entregado_por_cargo' => ActaInventarioCatalogo::CARGO_ENTREGA,
                'equipos' => [[
                    'cantidad' => 1,
                    'descripcion' => '',
                    'marca' => '',
                    'modelo' => '',
                    'serial' => '',
                    'observaciones' => '',
                ]],
            ]),
            'inventario' => ActaInventarioCatalogo::equipos(),
            'action' => route('actas.entrega.store'),
            'method' => 'POST',
            'title' => 'Nueva Acta de Entrega',
        ]);
    }

    public function store(StoreActaEntregaRequest $request): RedirectResponse
    {
        $acta = ActaEntrega::query()->create($request->validated());

        return redirect()
            ->route('actas.entrega.formato', $acta)
            ->with('status', 'Acta de entrega creada. Puedes imprimirla ahora.');
    }

    public function edit(ActaEntrega $actaEntrega): View
    {
        return view('actas.entrega.form', [
            'acta' => $actaEntrega,
            'inventario' => ActaInventarioCatalogo::equipos(),
            'action' => route('actas.entrega.update', $actaEntrega),
            'method' => 'PUT',
            'title' => 'Editar Acta de Entrega',
        ]);
    }

    public function update(UpdateActaEntregaRequest $request, ActaEntrega $actaEntrega): RedirectResponse
    {
        $actaEntrega->update($request->validated());

        return redirect()
            ->route('actas.entrega.formato', $actaEntrega)
            ->with('status', 'Acta de entrega actualizada.');
    }

    public function formato(ActaEntrega $actaEntrega): View
    {
        return view('actas.formato', [
            'acta' => $actaEntrega,
            'titulo' => 'ACTA DE ENTREGA',
            'volver' => route('actas.entrega'),
            'editar' => route('actas.entrega.edit', $actaEntrega),
        ]);
    }
}
