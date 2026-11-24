<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActaSalidaRequest;
use App\Http\Requests\UpdateActaSalidaRequest;
use App\Models\ActaSalida;
use App\Support\ActaInventarioCatalogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActaSalidaController extends Controller
{
    public function index(): View
    {
        return view('actas.salida.index', [
            'actas' => ActaSalida::query()->latest('fecha')->latest('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('actas.salida.form', [
            'acta' => new ActaSalida([
                'ciudad' => 'Floridablanca',
                'fecha' => now()->toDateString(),
                'proposito' => 'para realizar diagnostico y reparación',
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
            'action' => route('actas.salida.store'),
            'method' => 'POST',
            'title' => 'Nueva Acta de Salida',
        ]);
    }

    public function store(StoreActaSalidaRequest $request): RedirectResponse
    {
        $acta = ActaSalida::query()->create($request->validated());

        return redirect()
            ->route('actas.salida.formato', $acta)
            ->with('status', 'Acta de salida creada. Puedes imprimirla ahora.');
    }

    public function edit(ActaSalida $actaSalida): View
    {
        return view('actas.salida.form', [
            'acta' => $actaSalida,
            'inventario' => ActaInventarioCatalogo::equipos(),
            'action' => route('actas.salida.update', $actaSalida),
            'method' => 'PUT',
            'title' => 'Editar Acta de Salida',
        ]);
    }

    public function update(UpdateActaSalidaRequest $request, ActaSalida $actaSalida): RedirectResponse
    {
        $actaSalida->update($request->validated());

        return redirect()
            ->route('actas.salida.formato', $actaSalida)
            ->with('status', 'Acta de salida actualizada.');
    }

    public function formato(ActaSalida $actaSalida): View
    {
        return view('actas.formato', [
            'acta' => $actaSalida,
            'titulo' => 'ACTA DE SALIDA',
            'volver' => route('actas.salida'),
            'editar' => route('actas.salida.edit', $actaSalida),
        ]);
    }
}
