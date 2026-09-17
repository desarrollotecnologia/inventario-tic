<?php

namespace App\Http\Controllers;

use App\Http\Requests\CelularRequest;
use App\Models\Celular;
use App\Support\CelularFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CelularController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'tipo' => trim((string) $request->query('tipo', '')),
            'marca' => trim((string) $request->query('marca', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'imei' => trim((string) $request->query('imei', '')),
            'numero_celular' => trim((string) $request->query('numero_celular', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
        ];

        $totalCelulares = Celular::query()->count();
        $celulares = Celular::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (Celular $celular): int => (int) $celular->item)
            ->values();

        return view('relacion.celulares.index', [
            'celulares' => $celulares,
            'filtros' => $filtros,
            'totalCelulares' => $totalCelulares,
            'celularesFiltrados' => $celulares->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayCelulares' => $totalCelulares > 0,
            'hayTraslados' => Celular::query()->where('en_traslado', true)->exists(),
            'hayBajas' => Celular::query()->where('en_baja', true)->exists(),
            'groups' => CelularFields::groups(),
        ]);
    }

    public function create(): View
    {
        return view('relacion.celulares.form', [
            'celular' => new Celular(['item' => Celular::siguienteItem()]),
            'groups' => CelularFields::groups(),
            'action' => route('celulares.store'),
            'method' => 'POST',
            'title' => 'Registrar celular o tablet',
        ]);
    }

    public function store(CelularRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['item']);
        $datos['item'] = Celular::siguienteItem();

        Celular::query()->create($datos);

        return redirect()
            ->route('celulares.index')
            ->with('status', 'El celular o tablet se registró correctamente.');
    }

    public function edit(Celular $celular): View
    {
        return view('relacion.celulares.form', [
            'celular' => $celular,
            'groups' => CelularFields::groups(),
            'action' => route('celulares.update', $celular),
            'method' => 'PUT',
            'title' => 'Editar celular o tablet',
        ]);
    }

    public function update(CelularRequest $request, Celular $celular): RedirectResponse
    {
        $celular->update($request->safe()->except(['item']));

        return redirect()
            ->route('celulares.index')
            ->with('status', 'El celular o tablet se actualizó correctamente.');
    }

    public function accion(Request $request, Celular $celular): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $celular->update([
            $campo => $request->boolean('valor'),
        ]);

        $mensaje = match ($campo) {
            'en_traslado' => $celular->en_traslado
                ? 'El celular o tablet se marcó para traslado.'
                : 'El celular o tablet se quitó de traslados.',
            'en_disponible' => $celular->en_disponible
                ? 'El celular o tablet quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'El celular o tablet ya no está marcado como disponible.',
            default => $celular->en_baja
                ? 'El celular o tablet se marcó para baja.'
                : 'El celular o tablet se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(Celular $celular): RedirectResponse
    {
        $celular->delete();

        return redirect()
            ->route('celulares.index')
            ->with('status', 'El celular o tablet se eliminó correctamente.');
    }
}
