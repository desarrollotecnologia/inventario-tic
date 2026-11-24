@php
    use App\Support\ActaInventarioCatalogo;

    $equipos = old('equipos', is_array($acta->equipos) ? $acta->equipos : [[
        'cantidad' => 1,
        'descripcion' => '',
        'marca' => '',
        'modelo' => '',
        'serial' => '',
        'observaciones' => '',
    ]]);
    $cargoFijo = ActaInventarioCatalogo::CARGO_ENTREGA;
    $representante = old('representante', $acta->representante);
    $destino = old('destino', $acta->destino);
@endphp

<script type="application/json" data-acta-inventario-catalogo>@json($inventario ?? [])</script>

<form method="POST" action="{{ $action }}" class="registro-mayusculas space-y-6 px-4 py-5 sm:px-6" data-acta-formulario>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <fieldset>
        <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Datos del acta</legend>
        <div class="grid gap-3 sm:grid-cols-12">
            <div class="sm:col-span-3">
                <label for="ciudad" class="mb-1 block text-sm font-medium text-slate-700">Ciudad</label>
                <input id="ciudad" name="ciudad" type="text" value="{{ old('ciudad', $acta->ciudad) }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required>
                @error('ciudad') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="fecha" class="mb-1 block text-sm font-medium text-slate-700">Fecha</label>
                <input id="fecha" name="fecha" type="date" value="{{ old('fecha', optional($acta->fecha)->toDateString() ?? $acta->fecha) }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required>
                @error('fecha') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-6">
                <label for="representante" class="mb-1 block text-sm font-medium text-slate-700">Representante Colbeef / Persona quien entrega</label>
                <input id="representante" name="representante" type="text" value="{{ $representante }}" data-acta-representante class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required>
                @error('representante') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-6">
                <label for="destino" class="mb-1 block text-sm font-medium text-slate-700">{{ $destinoLabel }} / Persona quien recibe</label>
                <input id="destino" name="destino" type="text" value="{{ $destino }}" data-acta-destino class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required>
                @error('destino') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-6">
                <label for="proposito" class="mb-1 block text-sm font-medium text-slate-700">{{ $propositoLabel }}</label>
                <input id="proposito" name="proposito" type="text" value="{{ old('proposito', $acta->proposito) }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" @if ($propositoRequerido ?? false) required @endif>
                @error('proposito') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Firmas</legend>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="entregado_por_nombre" class="mb-1 block text-sm font-medium text-slate-700">Persona quien entrega</label>
                <input id="entregado_por_nombre" name="entregado_por_nombre" type="text" value="{{ old('entregado_por_nombre', $representante) }}" data-acta-entregado-nombre readonly class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700" required>
                <p class="mt-1 text-xs text-slate-500">Se toma del representante Colbeef.</p>
                @error('entregado_por_nombre') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="entregado_por_cargo" class="mb-1 block text-sm font-medium text-slate-700">Cargo quien entrega</label>
                <input id="entregado_por_cargo" name="entregado_por_cargo" type="text" value="{{ $cargoFijo }}" readonly class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700" required>
                @error('entregado_por_cargo') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="recibido_por_nombre" class="mb-1 block text-sm font-medium text-slate-700">Persona quien recibe</label>
                <input id="recibido_por_nombre" name="recibido_por_nombre" type="text" value="{{ old('recibido_por_nombre', $destino) }}" data-acta-recibido-nombre readonly class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700" required>
                <p class="mt-1 text-xs text-slate-500">Se toma del destino del acta.</p>
                @error('recibido_por_nombre') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="recibido_por_documento" class="mb-1 block text-sm font-medium text-slate-700">CC quien recibe</label>
                <input id="recibido_por_documento" name="recibido_por_documento" type="text" value="{{ old('recibido_por_documento', $acta->recibido_por_documento) }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                @error('recibido_por_documento') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
            </div>
        </div>
    </fieldset>

    <fieldset>
        <div class="mb-3 flex items-center justify-between gap-3">
            <legend class="text-xs font-semibold uppercase tracking-wide text-slate-500">Equipos</legend>
            <button type="button" data-equipos-agregar class="rounded-lg border border-brand-green px-3 py-1.5 text-xs font-semibold text-brand-green hover:bg-brand-green-soft">
                + Agregar equipo
            </button>
        </div>
        @error('equipos') <p class="mb-2 text-xs text-brand-red">{{ $message }}</p> @enderror

        <div class="space-y-3" data-equipos-lista>
            @foreach ($equipos as $index => $equipo)
                <div class="rounded-xl border border-slate-200 p-3" data-equipos-fila>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Equipo <span data-equipos-numero>{{ $index + 1 }}</span></p>
                        <button type="button" data-equipos-quitar class="text-xs font-semibold text-brand-red hover:underline {{ count($equipos) === 1 ? 'hidden' : '' }}">Quitar</button>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-12">
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Cantidad</label>
                            <input name="equipos[{{ $index }}][cantidad]" type="number" min="1" value="{{ $equipo['cantidad'] ?? 1 }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required>
                        </div>
                        <div class="relative sm:col-span-4">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Descripción</label>
                            <input name="equipos[{{ $index }}][descripcion]" type="text" value="{{ $equipo['descripcion'] ?? '' }}" autocomplete="off" data-acta-inventario-input="descripcion" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required placeholder="Escribe para filtrar inventario">
                        </div>
                        <div class="relative sm:col-span-3">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Marca</label>
                            <input name="equipos[{{ $index }}][marca]" type="text" value="{{ $equipo['marca'] ?? '' }}" autocomplete="off" data-acta-inventario-input="marca" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required placeholder="Escribe para filtrar">
                        </div>
                        <div class="relative sm:col-span-3">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Modelo</label>
                            <input name="equipos[{{ $index }}][modelo]" type="text" value="{{ $equipo['modelo'] ?? '' }}" autocomplete="off" data-acta-inventario-input="modelo" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" placeholder="Escribe para filtrar">
                        </div>
                        <div class="relative sm:col-span-4">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Serial</label>
                            <input name="equipos[{{ $index }}][serial]" type="text" value="{{ $equipo['serial'] ?? '' }}" autocomplete="off" data-acta-inventario-input="serial" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" required placeholder="Escribe para filtrar">
                        </div>
                        <div class="sm:col-span-8">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                            <input name="equipos[{{ $index }}][observaciones]" type="text" value="{{ $equipo['observaciones'] ?? '' }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft" placeholder="Novedades u observaciones">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </fieldset>

    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-4">
        <a href="{{ $cancelUrl }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
        <button type="submit" class="rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
            Guardar e imprimir
        </button>
    </div>
</form>
