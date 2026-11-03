<?php

namespace App\Http\Controllers;

use App\Http\Requests\BajaActaRequest;
use App\Http\Requests\BajaCorreoRequest;
use App\Http\Requests\FirmaRequest;
use App\Mail\BajasRealizadasMail;
use App\Models\Baja;
use App\Models\BajaSolicitante;
use App\Models\Firma;
use App\Support\BajaActaFields;
use App\Support\BajaMensaje;
use App\Support\CorreoTrasladoFields;
use App\Support\FirmaHtml;
use App\Support\FirmaImagen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class BajaController extends Controller
{
    public function index(): View
    {
        Baja::sincronizarPendientes();

        $listado = Baja::listadoModulo();
        $porNotificar = Baja::porNotificar();

        return view('bajas.index', [
            'listado' => $listado,
            'pendientes' => $listado->filter(fn (Baja $baja): bool => ! $baja->estaCompletada())->values(),
            'completadas' => $listado->filter(fn (Baja $baja): bool => $baja->estaCompletada())->values(),
            'porNotificar' => $porNotificar,
            'firmas' => Firma::query()->latest('id')->get(),
            'correosDestino' => CorreoTrasladoFields::destinatarios(),
            'asuntoCorreo' => BajaMensaje::asunto(),
        ]);
    }

    public function edit(Baja $baja): View|RedirectResponse
    {
        if ($baja->estaEnHistorial()) {
            return redirect()
                ->route('bajas.historial')
                ->with('status', 'Esta baja ya fue enviada al historial.');
        }

        if ($baja->estaCompletada()) {
            return redirect()
                ->route('modules.bajas')
                ->with('status', 'Esta baja ya tiene el acta completada. Usa Enviar bajas para notificarla.');
        }

        $siguiente = Baja::query()
            ->pendientes()
            ->where('id', '!=', $baja->id)
            ->orderBy('id')
            ->first();

        $solicitantes = BajaActaFields::solicitantes();
        $cargos = BajaActaFields::cargos();
        $nombre = old('solicitante_nombre', $baja->solicitante_nombre);
        $cargo = old('solicitante_cargo', $baja->solicitante_cargo);

        if (! in_array($nombre, $solicitantes, true)) {
            $nombre = '';
        }

        if (! in_array($cargo, $cargos, true)) {
            $cargo = '';
        }

        return view('bajas.form', [
            'baja' => $baja,
            'siguiente' => $siguiente,
            'pendientesRestantes' => Baja::query()->pendientes()->count(),
            'actaNumero' => BajaActaFields::siguienteActaNumero(),
            'solicitantes' => $solicitantes,
            'cargos' => $cargos,
            'departamento' => BajaActaFields::departamento(),
            'solicitanteNombre' => $nombre,
            'solicitanteCargo' => $cargo,
            'registrosSolicitantes' => BajaSolicitante::query()->orderBy('nombre')->get(),
        ]);
    }

    public function update(BajaActaRequest $request, Baja $baja): RedirectResponse
    {
        if ($baja->estaEnHistorial()) {
            return redirect()
                ->route('bajas.historial')
                ->with('status', 'Esta baja ya fue enviada al historial.');
        }

        if ($baja->estaCompletada()) {
            return redirect()
                ->route('modules.bajas')
                ->with('status', 'Esta baja ya tiene el acta completada.');
        }

        $datos = $request->safe()->except([
            'adjunto_acta',
            'adjunto_evidencia',
            'adjunto_soporte',
        ]);

        foreach (['adjunto_acta', 'adjunto_soporte'] as $campo) {
            if (! $request->hasFile($campo)) {
                continue;
            }

            if (filled($baja->{$campo})) {
                Storage::disk('public')->delete($baja->{$campo});
            }

            $datos[$campo] = $request->file($campo)->store('bajas', 'public');
        }

        if ($request->hasFile('adjunto_evidencia')) {
            foreach ($baja->rutasEvidencia() as $rutaAnterior) {
                Storage::disk('public')->delete($rutaAnterior);
            }

            $datos['adjunto_evidencia'] = collect($request->file('adjunto_evidencia'))
                ->map(fn ($archivo): string => $archivo->store('bajas', 'public'))
                ->values()
                ->all();
        }

        $baja->update([
            ...$datos,
            'completada_at' => now(),
        ]);

        $siguiente = Baja::query()->pendientes()->orderBy('id')->first();

        if ($siguiente !== null) {
            return redirect()
                ->route('bajas.edit', $siguiente)
                ->with('status', 'Acta guardada. Continúa con el siguiente ítem pendiente.');
        }

        return redirect()
            ->route('modules.bajas')
            ->with('status', 'Acta guardada. Cuando estén listas, usa Enviar bajas para notificarlas.');
    }

    public function storeSolicitante(Request $request): RedirectResponse
    {
        $validado = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'baja_id' => ['nullable', 'integer', 'exists:bajas,id'],
        ], [], [
            'nombre' => 'nombre del solicitante',
        ]);

        $nombre = mb_strtoupper(trim($validado['nombre']));

        if ($nombre === '' || BajaSolicitante::query()->where('nombre', $nombre)->exists()) {
            return back()->withErrors(['nombre' => 'Ese solicitante ya está en el catálogo o el nombre no es válido.']);
        }

        BajaSolicitante::query()->create([
            'nombre' => $nombre,
        ]);

        $destino = filled($validado['baja_id'] ?? null)
            ? route('bajas.edit', $validado['baja_id'])
            : route('modules.bajas');

        return redirect($destino)->with('status', 'Solicitante agregado al catálogo.');
    }

    public function destroySolicitante(BajaSolicitante $solicitante, Request $request): RedirectResponse
    {
        $bajaId = $request->validate([
            'baja_id' => ['nullable', 'integer', 'exists:bajas,id'],
        ])['baja_id'] ?? null;

        $solicitante->delete();

        $destino = filled($bajaId)
            ? route('bajas.edit', $bajaId)
            : route('modules.bajas');

        return redirect($destino)->with('status', 'Solicitante eliminado del catálogo.');
    }

    public function historial(): View
    {
        return view('bajas.historial', [
            'bajas' => Baja::query()->enHistorial()->get(),
        ]);
    }

    public function formato(Baja $baja): View|RedirectResponse
    {
        if (! $baja->estaCompletada()) {
            return redirect()
                ->route('bajas.edit', $baja)
                ->with('status', 'Completa el acta antes de visualizar el formato.');
        }

        return view('bajas.formato', [
            'baja' => $baja,
        ]);
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
            ->route('modules.bajas')
            ->with('status', 'La firma se creó correctamente.')
            ->with('abrir', 'correo');
    }

    public function eliminarFirma(Firma $firma): RedirectResponse
    {
        $this->borrarArchivosFirma($firma);
        $firma->delete();

        return redirect()
            ->route('modules.bajas')
            ->with('status', 'La firma se eliminó correctamente.')
            ->with('abrir', 'correo');
    }

    public function enviarCorreo(BajaCorreoRequest $request): RedirectResponse
    {
        $bajas = Baja::porNotificar();

        if ($bajas->isEmpty()) {
            return redirect()
                ->route('modules.bajas')
                ->with('status', 'Aún no hay bajas completadas para enviar.');
        }

        $firma = Firma::query()->findOrFail($request->validated('firma_id'));

        try {
            Mail::to($request->validated('destinatarios'))->send(new BajasRealizadasMail($bajas, $firma));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return redirect()
                ->route('modules.bajas')
                ->with('abrir', 'correo')
                ->with('status', 'No se pudo conectar al servidor de correo. Revisa la conexión a internet o la configuración SMTP e inténtalo de nuevo.');
        }

        DB::transaction(function () use ($bajas): void {
            $ahora = now();

            Baja::query()
                ->whereIn('id', $bajas->modelKeys())
                ->update(['correo_enviado_at' => $ahora]);

            foreach ($bajas as $baja) {
                $activo = $baja->equipo
                    ?? $baja->celular
                    ?? $baja->radio
                    ?? $baja->impresora
                    ?? $baja->biometrico;

                if ($activo === null) {
                    continue;
                }

                $activo->forceFill(['en_baja' => false])->save();
            }
        });

        return redirect()
            ->route('bajas.historial')
            ->with('status', 'El correo de bajas se envió correctamente.');
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
}
