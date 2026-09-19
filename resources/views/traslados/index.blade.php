<x-layouts.app title="Traslados">
    <a href="{{ route('home') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver al inicio</a>

    <x-portada-modulo
        title="Traslados"
        subtitle="Completa usuario, cargo, departamento y área de quien recibe. Al aplicar, el equipo queda asignado a esa persona."
    >
        <x-slot:actions>
            <div class="flex shrink-0 flex-col items-stretch gap-2 sm:items-end">
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('traslados.historial') }}"
                        class="inline-flex items-center justify-center rounded-lg border-2 border-white/40 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10"
                    >
                        Historial de Traslados
                    </a>
                    @if ($pendientes->isNotEmpty())
                        <form
                            id="traslados-pendientes"
                            method="POST"
                            action="{{ route('traslados.store') }}"
                            onsubmit="return confirm('¿Aplicar los traslados y actualizar los responsables de los equipos?')"
                        >
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                                Aplicar
                            </button>
                        </form>
                    @endif
                </div>
                @error('traslados')
                    <p class="text-xs text-red-200">{{ $message }}</p>
                @enderror
            </div>
        </x-slot:actions>
    </x-portada-modulo>

    @if ($pendientes->isNotEmpty() || $porNotificar->isEmpty())
    <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>

            @if ($pendientes->isNotEmpty())
                <script type="application/json" data-perfiles-usuarios>@json($perfilesUsuarios)</script>
                <div class="overflow-x-auto overflow-y-visible">
                    <table class="min-w-[88rem] w-full table-fixed text-left text-xs">
                        <thead class="bg-orange-50 text-[11px] uppercase leading-tight tracking-wide text-orange-900">
                            <tr>
                                <th class="w-[5%] px-2 py-2.5 font-medium">Ítem</th>
                                <th class="w-[8%] px-2 py-2.5 font-medium">Fecha</th>
                                <th class="w-[9%] px-2 py-2.5 font-medium">Usuario Entrega</th>
                                <th class="w-[8%] px-2 py-2.5 font-medium">Código Contable</th>
                                <th class="w-[7%] px-2 py-2.5 font-medium">Área</th>
                                <th class="w-[7%] px-2 py-2.5 font-medium">Tipo</th>
                                <th class="w-[10%] px-2 py-2.5 font-medium">Descripción del equipo</th>
                                <th class="w-[8%] px-2 py-2.5 font-medium">Serial</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Usuario Recibe</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Área Recibe</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-orange-100">
                            @foreach ($pendientes as $equipo)
                                @php
                                    $clave = match (true) {
                                        $equipo instanceof \App\Models\Celular => 'c-'.$equipo->id,
                                        $equipo instanceof \App\Models\Radio => 'r-'.$equipo->id,
                                        $equipo instanceof \App\Models\Impresora => 'i-'.$equipo->id,
                                        $equipo instanceof \App\Models\Biometrico => 'b-'.$equipo->id,
                                        default => (string) $equipo->id,
                                    };
                                    $prefijo = 'traslados.'.$clave;
                                    $nombre = fn (string $campo): string => 'traslados['.$clave.']['.$campo.']';
                                @endphp
                                <tr class="bg-orange-50 align-top text-slate-700">
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $loop->iteration }}</td>
                                    <td class="px-2 py-2.5">
                                        <input
                                            form="traslados-pendientes"
                                            type="date"
                                            name="{{ $nombre('fecha') }}"
                                            value="{{ old($prefijo.'.fecha', now()->toDateString()) }}"
                                            class="block w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                        >
                                        @error($prefijo.'.fecha')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="break-words px-2 py-2.5">{{ $equipo->usuario ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $equipo instanceof \App\Models\EquipoComputo ? ($equipo->codigo ?: '—') : '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $equipo->area ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ match (true) {
                                        $equipo instanceof \App\Models\EquipoComputo => $equipo->descripcion ?: '—',
                                        $equipo instanceof \App\Models\Biometrico => 'BIOMÉTRICO',
                                        default => $equipo->tipo ?: '—',
                                    } }}</td>
                                    <td class="break-words px-2 py-2.5" title="{{ $equipo instanceof \App\Models\EquipoComputo ? $equipo->marca_referencia : $equipo->marcaMostrada() }}">{{ $equipo instanceof \App\Models\EquipoComputo ? ($equipo->marca_referencia ?: '—') : ($equipo->marcaMostrada() ?: '—') }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $equipo->serialMostrado() ?: '—' }}</td>
                                    <td class="relative z-20 overflow-visible px-2 py-2.5">
                                        <x-combobox
                                            data-autofill-usuario
                                            name="{{ $nombre('usuario_recibe') }}"
                                            form="traslados-pendientes"
                                            :value="old($prefijo.'.usuario_recibe', '')"
                                            :options="$usuarios"
                                            placeholder="Escribe o elige un usuario"
                                        />
                                        @error($prefijo.'.usuario_recibe')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                        <label class="mt-1.5 block text-[10px] font-medium uppercase tracking-wide text-slate-500">Cargo</label>
                                        <x-combobox
                                            class="mt-0.5"
                                            name="{{ $nombre('cargo_recibe') }}"
                                            form="traslados-pendientes"
                                            :value="old($prefijo.'.cargo_recibe', '')"
                                            :options="$cargos"
                                            placeholder="Escribe o elige un cargo"
                                        />
                                        @error($prefijo.'.cargo_recibe')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="relative z-20 overflow-visible px-2 py-2.5">
                                        <x-combobox
                                            name="{{ $nombre('area_recibe') }}"
                                            form="traslados-pendientes"
                                            :value="old($prefijo.'.area_recibe', '')"
                                            :options="$areas"
                                            placeholder="Escribe o elige un área"
                                        />
                                        @error($prefijo.'.area_recibe')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                        <label class="mt-1.5 block text-[10px] font-medium uppercase tracking-wide text-slate-500">Departamento</label>
                                        <x-combobox
                                            class="mt-0.5"
                                            name="{{ $nombre('departamento_recibe') }}"
                                            form="traslados-pendientes"
                                            :value="old($prefijo.'.departamento_recibe', '')"
                                            :options="$departamentos"
                                            placeholder="Escribe o elige un departamento"
                                        />
                                        @error($prefijo.'.departamento_recibe')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <textarea
                                            form="traslados-pendientes"
                                            name="{{ $nombre('observaciones') }}"
                                            rows="3"
                                            placeholder="Opcional"
                                            class="block w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                        >{{ old($prefijo.'.observaciones') }}</textarea>
                                        @error($prefijo.'.observaciones')
                                            <p class="mt-1 text-[10px] text-brand-red">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($porNotificar->isEmpty())
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay equipos marcados para traslado.</p>
            @endif
        </div>
    </section>
    @endif

    @if ($porNotificar->isNotEmpty())
        @include('traslados.partials.tabla-realizados', [
            'traslados' => $porNotificar,
            'titulo' => 'Traslados realizados',
            'descripcion' => 'Al enviar el correo, estos movimientos pasan al historial.',
            'conAcciones' => true,
            'mensajeWhatsapp' => $mensajeWhatsapp,
            'urlWhatsapp' => $urlWhatsapp,
        ])
        @include('traslados.partials.modales-correo')
    @endif
</x-layouts.app>
