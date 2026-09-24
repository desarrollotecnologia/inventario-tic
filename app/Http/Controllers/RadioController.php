<?php

namespace App\Http\Controllers;

use App\Http\Requests\RadioRequest;
use App\Models\Radio;
use App\Support\RadioFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RadioController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'tipo' => trim((string) $request->query('tipo', '')),
            'marca_referencia' => trim((string) $request->query('marca_referencia', '')),
            'modelo' => trim((string) $request->query('modelo', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
            'traslado' => $request->query('traslado') === '1' ? '1' : '',
            'baja' => $request->query('baja') === '1' ? '1' : '',
        ];

        $totalRadios = Radio::query()->count();
        $radios = Radio::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (Radio $radio): int => (int) $radio->item)
            ->values();

        return view('relacion.radios.index', [
            'radios' => $radios,
            'filtros' => $filtros,
            'totalRadios' => $totalRadios,
            'radiosFiltrados' => $radios->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayRadios' => $totalRadios > 0,
            'hayTraslados' => Radio::query()->where('en_traslado', true)->exists(),
            'hayBajas' => Radio::query()->where('en_baja', true)->exists(),
            'groups' => RadioFields::groups(),
        ]);
    }

    public function create(): View
    {
        return view('relacion.radios.form', [
            'radio' => new Radio(['item' => Radio::siguienteItem()]),
            'groups' => RadioFields::groups(),
            'action' => route('radios.store'),
            'method' => 'POST',
            'title' => 'Registrar radio de comunicación',
        ]);
    }

    public function store(RadioRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['item']);
        $datos['item'] = Radio::siguienteItem();

        Radio::query()->create($datos);

        return redirect()
            ->route('radios.index')
            ->with('status', 'El equipo de comunicación se registró correctamente.');
    }

    public function edit(Radio $radio): View
    {
        return view('relacion.radios.form', [
            'radio' => $radio,
            'groups' => RadioFields::groups(),
            'action' => route('radios.update', $radio),
            'method' => 'PUT',
            'title' => 'Editar radio de comunicación',
        ]);
    }

    public function update(RadioRequest $request, Radio $radio): RedirectResponse
    {
        $radio->update($request->safe()->except(['item']));

        return redirect()
            ->route('radios.index')
            ->with('status', 'El equipo de comunicación se actualizó correctamente.');
    }

    public function accion(Request $request, Radio $radio): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $radio->update([
            $campo => $request->boolean('valor'),
        ]);

        $mensaje = match ($campo) {
            'en_traslado' => $radio->en_traslado
                ? 'El equipo de comunicación se marcó para traslado.'
                : 'El equipo de comunicación se quitó de traslados.',
            'en_disponible' => $radio->en_disponible
                ? 'El equipo de comunicación quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'El equipo de comunicación ya no está marcado como disponible.',
            default => $radio->en_baja
                ? 'El equipo de comunicación se marcó para baja.'
                : 'El equipo de comunicación se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(Radio $radio): RedirectResponse
    {
        $radio->delete();

        return redirect()
            ->route('radios.index')
            ->with('status', 'El equipo de comunicación se eliminó correctamente.');
    }
}
