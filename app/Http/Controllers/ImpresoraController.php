<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImpresoraRequest;
use App\Models\Impresora;
use App\Support\ImpresoraFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpresoraController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'tipo' => trim((string) $request->query('tipo', '')),
            'descripcion' => trim((string) $request->query('descripcion', '')),
            'modelo' => trim((string) $request->query('modelo', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
        ];

        $totalImpresoras = Impresora::query()->count();
        $impresoras = Impresora::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (Impresora $impresora): int => (int) $impresora->item)
            ->values();

        return view('relacion.impresoras.index', [
            'impresoras' => $impresoras,
            'filtros' => $filtros,
            'totalImpresoras' => $totalImpresoras,
            'impresorasFiltradas' => $impresoras->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayImpresoras' => $totalImpresoras > 0,
            'hayTraslados' => Impresora::query()->where('en_traslado', true)->exists(),
            'hayBajas' => Impresora::query()->where('en_baja', true)->exists(),
            'groups' => ImpresoraFields::groups(),
        ]);
    }

    public function create(): View
    {
        return view('relacion.impresoras.form', [
            'impresora' => new Impresora(['item' => Impresora::siguienteItem()]),
            'groups' => ImpresoraFields::groups(),
            'action' => route('impresoras.store'),
            'method' => 'POST',
            'title' => 'Registrar impresora',
        ]);
    }

    public function store(ImpresoraRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['item']);
        $datos['item'] = Impresora::siguienteItem();

        Impresora::query()->create($datos);

        return redirect()
            ->route('impresoras.index')
            ->with('status', 'La impresora se registró correctamente.');
    }

    public function edit(Impresora $impresora): View
    {
        return view('relacion.impresoras.form', [
            'impresora' => $impresora,
            'groups' => ImpresoraFields::groups(),
            'action' => route('impresoras.update', $impresora),
            'method' => 'PUT',
            'title' => 'Editar impresora',
        ]);
    }

    public function update(ImpresoraRequest $request, Impresora $impresora): RedirectResponse
    {
        $impresora->update($request->safe()->except(['item']));

        return redirect()
            ->route('impresoras.index')
            ->with('status', 'La impresora se actualizó correctamente.');
    }

    public function accion(Request $request, Impresora $impresora): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $impresora->update([
            $campo => $request->boolean('valor'),
        ]);

        $mensaje = match ($campo) {
            'en_traslado' => $impresora->en_traslado
                ? 'La impresora se marcó para traslado.'
                : 'La impresora se quitó de traslados.',
            'en_disponible' => $impresora->en_disponible
                ? 'La impresora quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'La impresora ya no está marcada como disponible.',
            default => $impresora->en_baja
                ? 'La impresora se marcó para baja.'
                : 'La impresora se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(Impresora $impresora): RedirectResponse
    {
        $impresora->delete();

        return redirect()
            ->route('impresoras.index')
            ->with('status', 'La impresora se eliminó correctamente.');
    }
}
