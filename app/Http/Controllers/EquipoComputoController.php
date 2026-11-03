<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipoComputoRequest;
use App\Models\Baja;
use App\Models\EquipoComputo;
use App\Support\EquipoComputoExcel;
use App\Support\EquipoComputoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EquipoComputoController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $this->filtros($request);
        $totalEquipos = EquipoComputo::query()->count();
        $equipos = $this->equiposFiltrados($filtros);

        return view('relacion.equipos-computo.index', [
            'equipos' => $equipos,
            'filtros' => $filtros,
            'totalEquipos' => $totalEquipos,
            'equiposFiltrados' => $equipos->count(),
            'hayFiltros' => collect($filtros)->contains(fn (string $valor): bool => $valor !== ''),
            'hayEquipos' => $totalEquipos > 0,
            'hayTraslados' => EquipoComputo::query()->where('en_traslado', true)->exists(),
            'hayBajas' => EquipoComputo::query()->where('en_baja', true)->exists(),
            'groups' => EquipoComputoFields::groups(),
        ]);
    }

    public function exportar(Request $request): StreamedResponse
    {
        $equipos = $this->equiposFiltrados($this->filtros($request));
        $contenido = EquipoComputoExcel::bytes($equipos);
        $nombre = EquipoComputoExcel::nombreArchivo();

        return response()->streamDownload(
            function () use ($contenido): void {
                echo $contenido;
            },
            $nombre,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function create(): View
    {
        return view('relacion.equipos-computo.form', [
            'equipo' => new EquipoComputo(['item' => EquipoComputo::siguienteItem()]),
            'groups' => EquipoComputoFields::groups(),
            'action' => route('equipos-computo.store'),
            'method' => 'POST',
            'title' => 'Registrar equipo de cómputo',
        ]);
    }

    public function store(EquipoComputoRequest $request): RedirectResponse
    {
        $datos = $this->guardarFotos($request, $request->safe()->except(['item', ...EquipoComputoFields::fotos()]));
        $datos['item'] = EquipoComputo::siguienteItem();

        EquipoComputo::query()->create($datos);

        return redirect()
            ->route('equipos-computo.index')
            ->with('status', 'El equipo de cómputo se registró correctamente.');
    }

    public function edit(EquipoComputo $equipoComputo): View
    {
        return view('relacion.equipos-computo.form', [
            'equipo' => $equipoComputo,
            'groups' => EquipoComputoFields::groups(),
            'action' => route('equipos-computo.update', $equipoComputo),
            'method' => 'PUT',
            'title' => 'Editar equipo de cómputo',
        ]);
    }

    public function update(EquipoComputoRequest $request, EquipoComputo $equipoComputo): RedirectResponse
    {
        $equipoComputo->update($this->guardarFotos(
            $request,
            $request->safe()->except(['item', ...EquipoComputoFields::fotos()]),
            $equipoComputo
        ));

        return redirect()
            ->route('equipos-computo.index')
            ->with('status', 'El equipo de cómputo se actualizó correctamente.');
    }

    public function accion(Request $request, EquipoComputo $equipoComputo): RedirectResponse
    {
        $validado = $request->validate([
            'campo' => ['required', 'in:en_traslado,en_baja,en_disponible'],
        ]);

        $campo = $validado['campo'];
        $valor = $request->boolean('valor');
        $equipoComputo->update([
            $campo => $valor,
        ]);

        if ($campo === 'en_baja') {
            if ($valor) {
                Baja::registrarDesde($equipoComputo);
            } else {
                Baja::eliminarPendienteDe($equipoComputo);
            }
        }

        $mensaje = match ($campo) {
            'en_traslado' => $equipoComputo->en_traslado
                ? 'El equipo se marcó para traslado.'
                : 'El equipo se quitó de traslados.',
            'en_disponible' => $equipoComputo->en_disponible
                ? 'El equipo quedó disponible para asignar. El responsable se tacha y no se borra.'
                : 'El equipo ya no está marcado como disponible.',
            default => $equipoComputo->en_baja
                ? 'El equipo se marcó para baja.'
                : 'El equipo se quitó de bajas.',
        };

        return back()->with('status', $mensaje);
    }

    public function destroy(EquipoComputo $equipoComputo): RedirectResponse
    {
        Storage::disk('public')->delete(array_values(array_filter(
            $equipoComputo->only(EquipoComputoFields::fotos())
        )));

        $equipoComputo->delete();

        return redirect()
            ->route('equipos-computo.index')
            ->with('status', 'El equipo de cómputo se eliminó correctamente.');
    }

    /**
     * @return array<string, string>
     */
    private function filtros(Request $request): array
    {
        return [
            'usuario' => trim((string) $request->query('usuario', '')),
            'departamento' => trim((string) $request->query('departamento', '')),
            'descripcion' => trim((string) $request->query('descripcion', '')),
            'marca_referencia' => trim((string) $request->query('marca_referencia', '')),
            'serial' => trim((string) $request->query('serial', '')),
            'windows' => trim((string) $request->query('windows', '')),
            'office' => trim((string) $request->query('office', '')),
            'disponible' => $request->query('disponible') === '1' ? '1' : '',
            'traslado' => $request->query('traslado') === '1' ? '1' : '',
            'baja' => $request->query('baja') === '1' ? '1' : '',
        ];
    }

    /**
     * @param  array<string, string>  $filtros
     * @return Collection<int, EquipoComputo>
     */
    private function equiposFiltrados(array $filtros): Collection
    {
        return EquipoComputo::query()
            ->filtrar($filtros)
            ->get()
            ->sortBy(fn (EquipoComputo $equipo): int => (int) $equipo->item)
            ->values();
    }

    /**
     * @param  array<string, mixed>  $datos
     * @return array<string, mixed>
     */
    private function guardarFotos(EquipoComputoRequest $request, array $datos, ?EquipoComputo $equipo = null): array
    {
        foreach (EquipoComputoFields::fotos() as $campo) {
            unset($datos[$campo]);

            if ($request->hasFile($campo)) {
                if (filled($equipo?->{$campo})) {
                    Storage::disk('public')->delete($equipo->{$campo});
                }

                $datos[$campo] = $request->file($campo)->store('equipos-computo', 'public');

                continue;
            }

            if ($request->boolean('eliminar_'.$campo) && filled($equipo?->{$campo})) {
                Storage::disk('public')->delete($equipo->{$campo});
                $datos[$campo] = null;
            }
        }

        return $datos;
    }
}
