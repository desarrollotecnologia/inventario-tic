<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiometricoRequest;
use App\Models\Biometrico;
use App\Support\BiometricoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BiometricoController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = [
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'marca_referencia' => trim((string) $request->query('marca_referencia', '')),
            'modelo' => trim((string) $request->query('modelo', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
            'traslado' => $request->query('traslado') === '1' ? '1' : '',
            'baja' => $request->query('baja') === '1' ? '1' : '',
        ];

        $totalBiometricos = Biometrico::query()->count();
        $biometricos = Biometrico::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (Biometrico $biometrico): int => (int) $biometrico->item)
            ->values();

        return view('relacion.biometricos.index', [
            'biometricos' => $biometricos,
            'filtros' => $filtros,
            'totalBiometricos' => $totalBiometricos,
            'biometricosFiltrados' => $biometricos->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayBiometricos' => $totalBiometricos > 0,
            'hayTraslados' => Biometrico::query()->where('en_traslado', true)->exists(),
            'hayBajas' => Biometrico::query()->where('en_baja', true)->exists(),
            'groups' => BiometricoFields::groups(),
        ]);
    }

    public function create(): View
    {
        return view('relacion.biometricos.form', [
            'biometrico' => new Biometrico(['item' => Biometrico::siguienteItem()]),
            'groups' => BiometricoFields::groups(),
            'action' => route('biometricos.store'),
            'method' => 'POST',
            'title' => 'Registrar biométrico',
        ]);
    }

    public function store(BiometricoRequest $request): RedirectResponse
    {
        $datos = $request->safe()->except(['item']);
        $datos['item'] = Biometrico::siguienteItem();

        Biometrico::query()->create($datos);

        return redirect()
            ->route('biometricos.index')
            ->with('status', 'El biométrico se registró correctamente.');
    }

    public function edit(Biometrico $biometrico): View
    {
        return view('relacion.biometricos.form', [
            'biometrico' => $biometrico,
            'groups' => BiometricoFields::groups(),
            'action' => route('biometricos.update', $biometrico),
            'method' => 'PUT',
            'title' => 'Editar biométrico',
        ]);
    }

    public function update(BiometricoRequest $request, Biometrico $biometrico): RedirectResponse
    {
        $biometrico->update($request->safe()->except(['item']));

        return redirect()
            ->route('biometricos.index')
            ->with('status', 'El biométrico se actualizó correctamente.');
    }

    public function accion(Request $request, Biometrico $biometrico): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $biometrico->update([
            $campo => $request->boolean('valor'),
        ]);

        $mensaje = match ($campo) {
            'en_traslado' => $biometrico->en_traslado
                ? 'El biométrico se marcó para traslado.'
                : 'El biométrico se quitó de traslados.',
            'en_disponible' => $biometrico->en_disponible
                ? 'El biométrico quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'El biométrico ya no está marcado como disponible.',
            default => $biometrico->en_baja
                ? 'El biométrico se marcó para baja.'
                : 'El biométrico se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(Biometrico $biometrico): RedirectResponse
    {
        $biometrico->delete();

        return redirect()
            ->route('biometricos.index')
            ->with('status', 'El biométrico se eliminó correctamente.');
    }
}
