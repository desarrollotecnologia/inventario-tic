<?php

namespace App\Http\Controllers;

use App\Http\Requests\LectorCodigoBarrasRequest;
use App\Models\Baja;
use App\Models\LectorCodigoBarras;
use App\Support\LectorCodigoBarrasFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LectorCodigoBarrasController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'codigo_contable' => trim((string) $request->query('codigo_contable', '')),
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'area' => trim((string) $request->query('area', '')),
            'marca_referencia' => trim((string) $request->query('marca_referencia', '')),
            'modelo' => trim((string) $request->query('modelo', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
            'traslado' => $request->query('traslado') === '1' ? '1' : '',
            'baja' => $request->query('baja') === '1' ? '1' : '',
        ];

        $totalLectores = LectorCodigoBarras::query()->count();
        $lectores = LectorCodigoBarras::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (LectorCodigoBarras $lector): int => (int) $lector->item)
            ->values();

        return view('relacion.lectores-codigos-barras.index', [
            'lectores' => $lectores,
            'filtros' => $filtros,
            'totalLectores' => $totalLectores,
            'lectoresFiltrados' => $lectores->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayLectores' => $totalLectores > 0,
            'hayTraslados' => LectorCodigoBarras::query()->where('en_traslado', true)->exists(),
            'hayBajas' => LectorCodigoBarras::query()->where('en_baja', true)->exists(),
            'groups' => LectorCodigoBarrasFields::groups(),
        ]);
    }

    public function create(): View
    {
        return view('relacion.lectores-codigos-barras.form', [
            'lector' => new LectorCodigoBarras(['item' => LectorCodigoBarras::siguienteItem()]),
            'groups' => LectorCodigoBarrasFields::groups(),
            'action' => route('lectores-codigos-barras.store'),
            'method' => 'POST',
            'title' => 'Registrar lector de códigos de barras',
        ]);
    }

    public function store(LectorCodigoBarrasRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['item']);
        $datos['item'] = LectorCodigoBarras::siguienteItem();

        LectorCodigoBarras::query()->create($datos);

        return redirect()
            ->route('lectores-codigos-barras.index')
            ->with('status', 'El lector de códigos de barras se registró correctamente.');
    }

    public function edit(LectorCodigoBarras $lectorCodigoBarras): View
    {
        return view('relacion.lectores-codigos-barras.form', [
            'lector' => $lectorCodigoBarras,
            'groups' => LectorCodigoBarrasFields::groups(),
            'action' => route('lectores-codigos-barras.update', $lectorCodigoBarras),
            'method' => 'PUT',
            'title' => 'Editar lector de códigos de barras',
        ]);
    }

    public function update(LectorCodigoBarrasRequest $request, LectorCodigoBarras $lectorCodigoBarras): RedirectResponse
    {
        $lectorCodigoBarras->update($request->safe()->except(['item']));

        return redirect()
            ->route('lectores-codigos-barras.index')
            ->with('status', 'El lector de códigos de barras se actualizó correctamente.');
    }

    public function accion(Request $request, LectorCodigoBarras $lectorCodigoBarras): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $valor = $request->boolean('valor');
        $lectorCodigoBarras->update([
            $campo => $valor,
        ]);

        if ($campo === 'en_baja') {
            if ($valor) {
                Baja::registrarDesde($lectorCodigoBarras);
            } else {
                Baja::eliminarPendienteDe($lectorCodigoBarras);
            }
        }

        $mensaje = match ($campo) {
            'en_traslado' => $lectorCodigoBarras->en_traslado
                ? 'El lector se marcó para traslado.'
                : 'El lector se quitó de traslados.',
            'en_disponible' => $lectorCodigoBarras->en_disponible
                ? 'El lector quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'El lector ya no está marcado como disponible.',
            default => $lectorCodigoBarras->en_baja
                ? 'El lector se marcó para baja.'
                : 'El lector se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(LectorCodigoBarras $lectorCodigoBarras): RedirectResponse
    {
        $lectorCodigoBarras->delete();

        return redirect()
            ->route('lectores-codigos-barras.index')
            ->with('status', 'El lector de códigos de barras se eliminó correctamente.');
    }
}
