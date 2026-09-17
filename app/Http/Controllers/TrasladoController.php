<?php

namespace App\Http\Controllers;

use App\Http\Requests\FirmaRequest;
use App\Http\Requests\TrasladoCorreoRequest;
use App\Http\Requests\TrasladoRequest;
use App\Mail\TrasladosRealizadosMail;
use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Firma;
use App\Models\Impresora;
use App\Models\Radio;
use App\Models\Traslado;
use App\Support\CorreoTrasladoFields;
use App\Support\EquipoComputoFields;
use App\Support\FirmaHtml;
use App\Support\FirmaImagen;
use App\Support\TrasladoMensaje;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TrasladoController extends Controller
{
    public function index(): View
    {
        $porNotificar = $this->trasladosPorNotificar();

        return view('traslados.index', [
            'pendientes' => EquipoComputo::query()
                ->where('en_traslado', true)
                ->get()
                ->concat(Celular::query()->where('en_traslado', true)->get())
                ->concat(Radio::query()->where('en_traslado', true)->get())
                ->concat(Impresora::query()->where('en_traslado', true)->get())
                ->sortBy(fn (EquipoComputo|Celular|Radio|Impresora $equipo): int => (int) $equipo->item)
                ->values(),
            'porNotificar' => $porNotificar,
            'firmas' => Firma::query()->latest('id')->get(),
            'correosDestino' => CorreoTrasladoFields::destinatarios(),
            'asuntoCorreo' => TrasladoMensaje::asunto(),
            'urlWhatsapp' => TrasladoMensaje::urlWhatsapp($porNotificar),
            'mensajeWhatsapp' => TrasladoMensaje::whatsapp($porNotificar),
            'usuarios' => $this->valoresUnicos(['usuario'], ['usuario_entrega', 'usuario_recibe']),
            'cargos' => $this->valoresUnicos(['cargo'], ['cargo_entrega', 'cargo_recibe']),
            'areas' => $this->opcionesConNuevas(EquipoComputoFields::areas(), ['area'], ['area_entrega', 'area_recibe']),
            'departamentos' => $this->opcionesConNuevas(EquipoComputoFields::departamentos(), ['departamento'], ['departamento_entrega', 'departamento_recibe']),
            'perfilesUsuarios' => $this->perfilesUsuarios(),
        ]);
    }

    public function historial(): View
    {
        return view('traslados.historial', [
            'realizados' => $this->trasladosEnHistorial(),
        ]);
    }

    public function store(TrasladoRequest $request): RedirectResponse
    {
        $equiposPendientes = EquipoComputo::query()
            ->where('en_traslado', true)
            ->get()
            ->keyBy('id');
        $celularesPendientes = Celular::query()
            ->where('en_traslado', true)
            ->get()
            ->keyBy('id');
        $radiosPendientes = Radio::query()
            ->where('en_traslado', true)
            ->get()
            ->keyBy('id');
        $impresorasPendientes = Impresora::query()
            ->where('en_traslado', true)
            ->get()
            ->keyBy('id');

        $aplicados = 0;

        DB::transaction(function () use ($request, $equiposPendientes, $celularesPendientes, $radiosPendientes, $impresorasPendientes, &$aplicados): void {
            foreach ($request->validated('traslados') as $clave => $datos) {
                $clave = (string) $clave;

                if (str_starts_with($clave, 'c-')) {
                    $celular = $celularesPendientes->get((int) substr($clave, 2));

                    if ($celular === null) {
                        continue;
                    }

                    $this->aplicarTrasladoCelular($celular, $datos);
                    $aplicados++;

                    continue;
                }

                if (str_starts_with($clave, 'r-')) {
                    $radio = $radiosPendientes->get((int) substr($clave, 2));

                    if ($radio === null) {
                        continue;
                    }

                    $this->aplicarTrasladoRadio($radio, $datos);
                    $aplicados++;

                    continue;
                }

                if (str_starts_with($clave, 'i-')) {
                    $impresora = $impresorasPendientes->get((int) substr($clave, 2));

                    if ($impresora === null) {
                        continue;
                    }

                    $this->aplicarTrasladoImpresora($impresora, $datos);
                    $aplicados++;

                    continue;
                }

                $equipo = $equiposPendientes->get((int) $clave);

                if ($equipo === null) {
                    continue;
                }

                $this->aplicarTraslado($equipo, $datos);
                $aplicados++;
            }
        });

        if ($aplicados === 0) {
            return redirect()
                ->route('modules.traslados')
                ->with('status', 'No hay equipos marcados para traslado.');
        }

        $mensaje = $aplicados === 1
            ? 'El traslado se registró y el equipo quedó asignado al nuevo responsable.'
            : "Se aplicaron {$aplicados} traslados y los equipos quedaron asignados a sus nuevos responsables.";

        return redirect()
            ->route('modules.traslados')
            ->with('status', $mensaje);
    }

    public function guardarFirma(FirmaRequest $request): RedirectResponse
    {
        $persistido = FirmaHtml::persistirImagenes(
            FirmaHtml::sanitizar($request->validated('contenido'))
        );

        $imagen = $request->file('imagen')?->store('firmas', 'public')
            ?: $persistido['imagen']
            ?: FirmaImagen::generar($request->validated('nombre'));

        Firma::query()->create([
            'nombre' => $request->validated('nombre'),
            'contenido' => $persistido['html'] !== '' ? $persistido['html'] : e($request->validated('nombre')),
            'imagen' => $imagen,
        ]);

        return redirect()
            ->route('modules.traslados')
            ->with('status', 'La firma se creó correctamente.')
            ->with('abrir', 'correo');
    }

    public function eliminarFirma(Firma $firma): RedirectResponse
    {
        $this->borrarArchivosFirma($firma);
        $firma->delete();

        return redirect()
            ->route('modules.traslados')
            ->with('status', 'La firma se eliminó correctamente.')
            ->with('abrir', 'correo');
    }

    public function enviarCorreo(TrasladoCorreoRequest $request): RedirectResponse
    {
        $traslados = $this->trasladosPorNotificar();

        if ($traslados->isEmpty()) {
            return redirect()
                ->route('modules.traslados')
                ->with('status', 'Aún no hay traslados realizados para enviar.');
        }

        $firma = Firma::query()->findOrFail($request->validated('firma_id'));

        Mail::to($request->validated('destinatarios'))->send(new TrasladosRealizadosMail($traslados, $firma));

        Traslado::query()
            ->whereNull('correo_enviado_at')
            ->update(['correo_enviado_at' => now()]);

        return redirect()
            ->route('traslados.historial')
            ->with('status', 'El correo de traslados realizados se envió correctamente.');
    }

    private function borrarArchivosFirma(Firma $firma): void
    {
        $rutas = collect([$firma->imagen]);

        if (filled($firma->contenido) && preg_match_all('/src="([^"]+)"/', $firma->contenido, $coincidencias) > 0) {
            $rutas = $rutas->merge($coincidencias[1]);
        }

        $rutas
            ->filter(fn (mixed $ruta): bool => is_string($ruta) && $ruta !== '' && ! str_starts_with($ruta, 'http') && ! str_starts_with($ruta, 'cid:'))
            ->unique()
            ->each(function (string $ruta): void {
                $origen = html_entity_decode($ruta, ENT_QUOTES, 'UTF-8');

                if (Storage::disk('public')->exists($origen)) {
                    Storage::disk('public')->delete($origen);
                }
            });
    }

    /**
     * @param  array{fecha: string, usuario_recibe: string, cargo_recibe: string, departamento_recibe: string, area_recibe: string, observaciones?: string|null}  $datos
     */
    private function aplicarTraslado(EquipoComputo $equipo, array $datos): void
    {
        Traslado::query()->create([
            'equipo_computo_id' => $equipo->id,
            'fecha' => $datos['fecha'],
            'usuario_entrega' => $equipo->usuario,
            'cargo_entrega' => $equipo->cargo,
            'departamento_entrega' => $equipo->departamento,
            'area_entrega' => $equipo->area,
            'codigo' => $equipo->codigo,
            'tipo' => $equipo->descripcion,
            'descripcion_equipo' => $equipo->marca_referencia,
            'serial' => $equipo->serialMostrado(),
            'usuario_recibe' => $datos['usuario_recibe'],
            'cargo_recibe' => $datos['cargo_recibe'],
            'departamento_recibe' => $datos['departamento_recibe'],
            'area_recibe' => $datos['area_recibe'],
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        $equipo->update([
            'usuario' => $datos['usuario_recibe'],
            'cargo' => $datos['cargo_recibe'],
            'departamento' => $datos['departamento_recibe'],
            'area' => $datos['area_recibe'],
            'en_traslado' => false,
        ]);
    }

    /**
     * @param  array{fecha: string, usuario_recibe: string, cargo_recibe: string, departamento_recibe: string, area_recibe: string, observaciones?: string|null}  $datos
     */
    private function aplicarTrasladoCelular(Celular $celular, array $datos): void
    {
        Traslado::query()->create([
            'celular_id' => $celular->id,
            'fecha' => $datos['fecha'],
            'usuario_entrega' => $celular->usuario,
            'cargo_entrega' => $celular->cargo,
            'departamento_entrega' => $celular->departamento,
            'area_entrega' => $celular->area,
            'tipo' => $celular->tipo,
            'descripcion_equipo' => $celular->marcaMostrada(),
            'serial' => $celular->serialMostrado(),
            'usuario_recibe' => $datos['usuario_recibe'],
            'cargo_recibe' => $datos['cargo_recibe'],
            'departamento_recibe' => $datos['departamento_recibe'],
            'area_recibe' => $datos['area_recibe'],
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        $celular->update([
            'usuario' => $datos['usuario_recibe'],
            'cargo' => $datos['cargo_recibe'],
            'departamento' => $datos['departamento_recibe'],
            'area' => $datos['area_recibe'],
            'en_traslado' => false,
        ]);
    }

    /**
     * @param  array{fecha: string, usuario_recibe: string, cargo_recibe: string, departamento_recibe: string, area_recibe: string, observaciones?: string|null}  $datos
     */
    private function aplicarTrasladoRadio(Radio $radio, array $datos): void
    {
        Traslado::query()->create([
            'radio_id' => $radio->id,
            'fecha' => $datos['fecha'],
            'usuario_entrega' => $radio->usuario,
            'cargo_entrega' => $radio->cargo,
            'departamento_entrega' => $radio->departamento,
            'area_entrega' => $radio->area,
            'tipo' => $radio->tipo,
            'descripcion_equipo' => $radio->marcaMostrada(),
            'serial' => $radio->serialMostrado(),
            'usuario_recibe' => $datos['usuario_recibe'],
            'cargo_recibe' => $datos['cargo_recibe'],
            'departamento_recibe' => $datos['departamento_recibe'],
            'area_recibe' => $datos['area_recibe'],
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        $radio->update([
            'usuario' => $datos['usuario_recibe'],
            'cargo' => $datos['cargo_recibe'],
            'departamento' => $datos['departamento_recibe'],
            'area' => $datos['area_recibe'],
            'en_traslado' => false,
        ]);
    }

    /**
     * @param  array{fecha: string, usuario_recibe: string, cargo_recibe: string, departamento_recibe: string, area_recibe: string, observaciones?: string|null}  $datos
     */
    private function aplicarTrasladoImpresora(Impresora $impresora, array $datos): void
    {
        Traslado::query()->create([
            'impresora_id' => $impresora->id,
            'fecha' => $datos['fecha'],
            'usuario_entrega' => $impresora->usuario,
            'cargo_entrega' => $impresora->cargo,
            'departamento_entrega' => $impresora->departamento,
            'area_entrega' => $impresora->area,
            'tipo' => $impresora->tipo,
            'descripcion_equipo' => $impresora->marcaMostrada(),
            'serial' => $impresora->serialMostrado(),
            'usuario_recibe' => $datos['usuario_recibe'],
            'cargo_recibe' => $datos['cargo_recibe'],
            'departamento_recibe' => $datos['departamento_recibe'],
            'area_recibe' => $datos['area_recibe'],
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        $impresora->update([
            'usuario' => $datos['usuario_recibe'],
            'cargo' => $datos['cargo_recibe'],
            'departamento' => $datos['departamento_recibe'],
            'area' => $datos['area_recibe'],
            'en_traslado' => false,
        ]);
    }

    /**
     * @return Collection<int, Traslado>
     */
    private function trasladosPorNotificar(): Collection
    {
        return Traslado::query()
            ->with('equipo')
            ->whereNull('correo_enviado_at')
            ->latest('fecha')
            ->latest('id')
            ->get();
    }

    /**
     * @return Collection<int, Traslado>
     */
    private function trasladosEnHistorial(): Collection
    {
        return Traslado::query()
            ->with('equipo')
            ->whereNotNull('correo_enviado_at')
            ->latest('fecha')
            ->latest('id')
            ->get();
    }

    /**
     * @return array<string, array{usuario: string, cargo: string, area: string, departamento: string}>
     */
    private function perfilesUsuarios(): array
    {
        $perfiles = [];

        $registrar = function (?string $usuario, ?string $cargo, ?string $area, ?string $departamento) use (&$perfiles): void {
            $nombre = trim((string) $usuario);
            $clave = mb_strtolower($nombre);

            if ($clave === '' || isset($perfiles[$clave])) {
                return;
            }

            $perfiles[$clave] = [
                'usuario' => $nombre,
                'cargo' => trim((string) $cargo),
                'area' => trim((string) $area),
                'departamento' => trim((string) $departamento),
            ];
        };

        EquipoComputo::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['usuario', 'cargo', 'area', 'departamento'])
            ->each(fn (EquipoComputo $equipo) => $registrar(
                $equipo->usuario,
                $equipo->cargo,
                $equipo->area,
                $equipo->departamento
            ));

        Celular::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['usuario', 'cargo', 'area', 'departamento'])
            ->each(fn (Celular $celular) => $registrar(
                $celular->usuario,
                $celular->cargo,
                $celular->area,
                $celular->departamento
            ));

        Radio::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['usuario', 'cargo', 'area', 'departamento'])
            ->each(fn (Radio $radio) => $registrar(
                $radio->usuario,
                $radio->cargo,
                $radio->area,
                $radio->departamento
            ));

        Impresora::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get(['usuario', 'cargo', 'area', 'departamento'])
            ->each(fn (Impresora $impresora) => $registrar(
                $impresora->usuario,
                $impresora->cargo,
                $impresora->area,
                $impresora->departamento
            ));

        Traslado::query()
            ->latest('id')
            ->get(['usuario_recibe', 'cargo_recibe', 'area_recibe', 'departamento_recibe'])
            ->each(fn (Traslado $traslado) => $registrar(
                $traslado->usuario_recibe,
                $traslado->cargo_recibe,
                $traslado->area_recibe,
                $traslado->departamento_recibe
            ));

        return $perfiles;
    }

    /**
     * @param  list<string>  $camposEquipo
     * @param  list<string>  $camposTraslado
     * @return Collection<int, string>
     */
    private function valoresUnicos(array $camposEquipo, array $camposTraslado): Collection
    {
        return $this->valoresDe(EquipoComputo::class, $camposEquipo)
            ->merge($this->valoresDe(Celular::class, $camposEquipo))
            ->merge($this->valoresDe(Radio::class, $camposEquipo))
            ->merge($this->valoresDe(Impresora::class, $camposEquipo))
            ->merge($this->valoresDe(Traslado::class, $camposTraslado))
            ->unique(fn (string $valor): string => mb_strtolower($valor))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * @param  list<string>  $base
     * @param  list<string>  $camposEquipo
     * @param  list<string>  $camposTraslado
     * @return Collection<int, string>
     */
    private function opcionesConNuevas(array $base, array $camposEquipo, array $camposTraslado): Collection
    {
        $nuevas = $this->valoresUnicos($camposEquipo, $camposTraslado)
            ->reject(fn (string $valor): bool => in_array($valor, $base, true))
            ->values();

        return collect($base)->merge($nuevas)->values();
    }

    /**
     * @param  class-string  $modelo
     * @param  list<string>  $campos
     * @return Collection<int, string>
     */
    private function valoresDe(string $modelo, array $campos): Collection
    {
        $valores = collect();

        foreach ($campos as $campo) {
            $valores = $valores->merge($modelo::query()->pluck($campo));
        }

        return $valores
            ->map(fn (mixed $valor): string => is_string($valor) ? trim($valor) : '')
            ->filter()
            ->values();
    }
}
