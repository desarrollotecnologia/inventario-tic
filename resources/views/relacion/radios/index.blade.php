<x-layouts.app title="Radios de Comunicación">
    <a href="{{ route('modules.relacion') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Relación Inventario Sistemas</a>

    <x-portada-modulo
        title="Radios de Comunicación"
        subtitle="Haz clic en una fila para ver el detalle. Marca D si está disponible: el responsable se tacha y no se borra."
    >
        <x-slot:badge>
            <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-semibold text-white">
                @if ($hayFiltros)
                    {{ $radiosFiltrados }} de {{ $totalRadios }} {{ $totalRadios === 1 ? 'registro' : 'registros' }}
                @else
                    {{ $totalRadios }} {{ $totalRadios === 1 ? 'registro' : 'registros' }}
                @endif
            </span>
        </x-slot:badge>
        <x-slot:actions>
            @if ($hayTraslados)
                <a href="{{ route('modules.traslados') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                    Alistar Traslados
                </a>
            @endif
            @if ($hayBajas)
                <a href="{{ route('modules.bajas') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-red px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-red-dark">
                    Alistar Bajas
                </a>
            @endif
            <a href="{{ route('radios.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Registrar radio
            </a>
        </x-slot:actions>
    </x-portada-modulo>

    <form method="GET" action="{{ route('radios.index') }}" class="registro-mayusculas borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>
            <div class="px-3 py-2.5">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-800">Búsqueda rápida</h2>
                    <a href="{{ route('radios.index') }}" class="text-xs font-medium text-brand-green hover:text-brand-green-dark">Limpiar</a>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto">
                    <label class="sr-only" for="filtro-usuario">Usuario</label>
                    <input
                        id="filtro-usuario"
                        type="text"
                        name="usuario"
                        value="{{ $filtros['usuario'] }}"
                        placeholder="Usuario"
                        class="min-w-[8rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                    <label class="sr-only" for="filtro-tipo">Tipo</label>
                    <select
                        id="filtro-tipo"
                        name="tipo"
                        onchange="this.form.submit()"
                        class="min-w-[7.5rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                        <option value="">Tipo</option>
                        @foreach (\App\Support\RadioFields::tipos() as $opcion)
                            <option value="{{ $opcion }}" @selected($filtros['tipo'] === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="filtro-departamento">Departamento</label>
                    <select
                        id="filtro-departamento"
                        name="departamento"
                        onchange="this.form.submit()"
                        class="min-w-[9rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                        <option value="">Departamento</option>
                        @foreach (\App\Support\EquipoComputoFields::departamentos() as $opcion)
                            <option value="{{ $opcion }}" @selected($filtros['departamento'] === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="filtro-marca">Marca y Referencia</label>
                    <input
                        id="filtro-marca"
                        type="text"
                        name="marca_referencia"
                        value="{{ $filtros['marca_referencia'] }}"
                        placeholder="Marca y Referencia"
                        class="min-w-[9rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                    <label class="sr-only" for="filtro-modelo">Modelo</label>
                    <input
                        id="filtro-modelo"
                        type="text"
                        name="modelo"
                        value="{{ $filtros['modelo'] }}"
                        placeholder="Modelo"
                        class="min-w-[7rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                    <label class="sr-only" for="filtro-serial">Serial</label>
                    <input
                        id="filtro-serial"
                        type="text"
                        name="serial"
                        value="{{ $filtros['serial'] }}"
                        placeholder="Serial"
                        class="min-w-[7rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                    >
                </div>
                <div class="mt-2">
                    <button type="submit" class="rounded-lg bg-brand-green px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                        Buscar
                    </button>
                    <label class="ml-3 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-sm font-semibold text-sky-800">
                        <input
                            type="checkbox"
                            name="disponible"
                            value="1"
                            @checked($filtros['disponible'] === '1')
                            onchange="this.form.submit()"
                            class="h-3.5 w-3.5 rounded border-sky-300 text-sky-700 focus:ring-sky-500"
                        >
                        Solo disponibles
                    </label>
                </div>
            </div>
        </div>
    </form>

    <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>
            @if (! $hayRadios)
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay radios de comunicación registrados.</p>
            @elseif ($radios->isEmpty())
                <p class="px-6 py-10 text-sm text-slate-500">No hay equipos de comunicación que coincidan con el filtro.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="listado-mayusculas w-full text-left text-xs">
                        <thead class="bg-brand-green-soft text-[11px] uppercase leading-tight tracking-wide text-brand-green-dark">
                            <tr>
                                <th class="px-2 py-2.5 font-medium">Ítem</th>
                                <th class="px-2 py-2.5 font-medium">Tipo</th>
                                <th class="px-2 py-2.5 font-medium">Usuario</th>
                                <th class="px-2 py-2.5 font-medium">Departamento</th>
                                <th class="px-2 py-2.5 font-medium">Área</th>
                                <th class="px-2 py-2.5 font-medium">Marca y Referencia</th>
                                <th class="px-2 py-2.5 font-medium">Modelo</th>
                                <th class="px-2 py-2.5 font-medium">Serial</th>
                                <th class="w-px whitespace-nowrap px-2 py-2.5 font-medium">Acción</th>
                            </tr>
                        </thead>
                        @foreach ($radios as $radio)
                            <tbody class="border-t border-slate-100">
                                <tr
                                    @class([
                                        'cursor-pointer text-slate-700',
                                        'bg-orange-100 hover:bg-orange-200' => $radio->en_traslado,
                                        'bg-sky-100 hover:bg-sky-200' => $radio->en_disponible && ! $radio->en_traslado,
                                        'hover:bg-slate-50' => ! $radio->en_traslado && ! $radio->en_disponible,
                                    ])
                                    data-expand-row
                                    data-expand-target="detalle-radio-{{ $radio->id }}"
                                    aria-expanded="false"
                                >
                                    <td class="break-words px-2 py-2.5">{{ $radio->item ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $radio->tipo ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">
                                        <span @class(['line-through text-slate-400' => $radio->en_disponible])>{{ $radio->usuario ?: '—' }}</span>
                                        @if ($radio->en_disponible)
                                            <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-wide text-sky-700">Disponible</span>
                                        @endif
                                    </td>
                                    <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $radio->en_disponible])>{{ $radio->departamento ?: '—' }}</td>
                                    <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $radio->en_disponible])>{{ $radio->area ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $radio->marca_referencia ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $radio->modelo ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $radio->serial ?: '—' }}</td>
                                    <td class="w-px whitespace-nowrap px-2 py-2.5">
                                        <div class="flex items-center gap-1.5">
                                            @include('relacion.equipos-computo.acciones', [
                                                'modelo' => $radio,
                                                'rutaAccion' => route('radios.accion', $radio),
                                            ])
                                            <div class="flex items-center gap-1.5" data-expand-ignore>
                                                <a href="{{ route('radios.edit', $radio) }}" class="text-brand-green hover:text-brand-green-dark" title="Editar" aria-label="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 1 1 2.97 2.97L8.25 18.04 4 19.5l1.46-4.25 11.402-11.763Z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('radios.destroy', $radio) }}" onsubmit="return confirm('¿Eliminar este equipo de comunicación?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-brand-red hover:text-brand-red-dark" title="Eliminar" aria-label="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <span class="text-slate-400" data-expand-icon>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    id="detalle-radio-{{ $radio->id }}"
                                    @class([
                                        'hidden',
                                        'bg-orange-50' => $radio->en_traslado,
                                        'bg-sky-50' => $radio->en_disponible && ! $radio->en_traslado,
                                        'bg-slate-50' => ! $radio->en_traslado && ! $radio->en_disponible,
                                    ])
                                >
                                    <td colspan="9" class="px-4 py-5">
                                        <div class="grid gap-6 md:grid-cols-2">
                                            @foreach ($groups as $group => $fields)
                                                <section @class(['space-y-3', 'md:col-span-2' => $group === 'Fechas y observaciones'])>
                                                    <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</h2>
                                                    <dl @class([
                                                        'grid gap-3',
                                                        'sm:grid-cols-3' => $group === 'Fechas y observaciones',
                                                        'sm:grid-cols-2' => $group !== 'Fechas y observaciones',
                                                    ])>
                                                        @foreach ($fields as $name => $field)
                                                            <div @class(['sm:col-span-3' => $field['type'] === 'textarea' && $group === 'Fechas y observaciones', 'sm:col-span-2' => $field['type'] === 'textarea' && $group !== 'Fechas y observaciones'])>
                                                                <dt class="text-xs font-medium text-slate-500">{{ $field['label'] }}</dt>
                                                                <dd @class([
                                                                    'mt-1 text-sm',
                                                                    'line-through text-slate-400' => $radio->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true),
                                                                    'text-slate-900' => ! ($radio->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true)),
                                                                ])>
                                                                    @if ($field['type'] === 'date')
                                                                        {{ $radio->{$name}?->format('d/m/Y') ?: '—' }}
                                                                    @elseif ($field['type'] === 'computed')
                                                                        {{ $radio->antiguedad() }}
                                                                    @else
                                                                        {{ $radio->{$name} ?: '—' }}
                                                                    @endif
                                                                </dd>
                                                            </div>
                                                        @endforeach
                                                    </dl>
                                                </section>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
