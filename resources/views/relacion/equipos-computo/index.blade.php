<x-layouts.app title="Equipos de Cómputo">
    <a href="{{ route('modules.relacion') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Relación Inventario Sistemas</a>

    <x-portada-modulo
        title="Equipos de Cómputo"
        subtitle="Haz clic en una fila para ver el detalle. Marca D si el equipo está disponible: el responsable se tacha y no se borra."
    >
        <x-slot:badge>
            <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-semibold text-white">
                @if ($hayFiltros)
                    {{ $equiposFiltrados }} de {{ $totalEquipos }} {{ $totalEquipos === 1 ? 'equipo registrado' : 'equipos registrados' }}
                @else
                    {{ $totalEquipos }} {{ $totalEquipos === 1 ? 'equipo registrado' : 'equipos registrados' }}
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
            <a href="{{ route('equipos-computo.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Registrar equipo
            </a>
        </x-slot:actions>
    </x-portada-modulo>

    <form method="GET" action="{{ route('equipos-computo.index') }}" class="registro-mayusculas borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>
            <div class="p-4">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-slate-800">Búsqueda rápida</h2>
            <a href="{{ route('equipos-computo.index') }}" class="text-xs font-medium text-brand-green hover:text-brand-green-dark">Limpiar</a>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Usuario</span>
                <input type="text" name="usuario" value="{{ $filtros['usuario'] }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Departamento</span>
                <select name="departamento" onchange="this.form.submit()" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <option value="">Todos</option>
                    @foreach (\App\Support\EquipoComputoFields::departamentos() as $opcion)
                        <option value="{{ $opcion }}" @selected($filtros['departamento'] === $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Descripción</span>
                <select name="descripcion" onchange="this.form.submit()" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <option value="">Todas</option>
                    @foreach (\App\Support\EquipoComputoFields::descripciones() as $opcion)
                        <option value="{{ $opcion }}" @selected($filtros['descripcion'] === $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Marca y Referencia</span>
                <input type="text" name="marca_referencia" value="{{ $filtros['marca_referencia'] }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Serial</span>
                <input type="text" name="serial" value="{{ $filtros['serial'] }}" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Windows</span>
                <select name="windows" onchange="this.form.submit()" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <option value="">Todos</option>
                    @foreach (\App\Support\EquipoComputoFields::windows() as $opcion)
                        <option value="{{ $opcion }}" @selected($filtros['windows'] === $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] font-medium uppercase tracking-wide text-slate-500">Office</span>
                <select name="office" onchange="this.form.submit()" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <option value="">Todos</option>
                    @foreach (\App\Support\EquipoComputoFields::offices() as $opcion)
                        <option value="{{ $opcion }}" @selected($filtros['office'] === $opcion)>{{ $opcion }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-lg bg-brand-green px-3 py-2 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Buscar
            </button>
            @include('relacion.partials.filtros-estado')
            <a
                href="{{ route('equipos-computo.exportar', request()->query()) }}"
                class="ml-auto inline-flex items-center gap-1.5 rounded-lg border border-brand-green bg-white px-3 py-2 text-sm font-semibold text-brand-green hover:bg-brand-green-soft"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5 5-5M12 15V3" />
                </svg>
                Descargar Excel
            </a>
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
        @if (! $hayEquipos)
            <p class="px-6 py-10 text-sm text-slate-500">Aún no hay equipos de cómputo registrados.</p>
        @elseif ($equipos->isEmpty())
            <p class="px-6 py-10 text-sm text-slate-500">No hay equipos que coincidan con el filtro.</p>
        @else
            <div>
                <table class="listado-mayusculas w-full table-fixed text-left text-xs">
                    <thead class="bg-brand-green-soft text-[11px] uppercase leading-tight tracking-wide text-brand-green-dark">
                        <tr>
                            <th class="w-[4%] px-2 py-2.5 font-medium">Ítem</th>
                            <th class="w-[7%] px-2 py-2.5 font-medium">Código</th>
                            <th class="w-[11%] px-2 py-2.5 font-medium">Usuario</th>
                            <th class="w-[11%] px-2 py-2.5 font-medium">Departamento</th>
                            <th class="w-[8%] px-2 py-2.5 font-medium">Descripción</th>
                            <th class="w-[11%] px-2 py-2.5 font-medium">Marca y Referencia</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Serial</th>
                            <th class="w-[8%] px-2 py-2.5 font-medium">Windows</th>
                            <th class="w-[8%] px-2 py-2.5 font-medium">Office</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Cargo</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Acción</th>
                        </tr>
                    </thead>
                    @foreach ($equipos as $equipo)
                        <tbody class="border-t border-slate-100">
                            <tr
                                @class([
                                    'cursor-pointer text-slate-700',
                                    'bg-red-100 hover:bg-red-200' => $equipo->en_baja,
                                    'bg-orange-100 hover:bg-orange-200' => $equipo->en_traslado && ! $equipo->en_baja,
                                    'bg-sky-100 hover:bg-sky-200' => $equipo->en_disponible && ! $equipo->en_traslado && ! $equipo->en_baja,
                                    'hover:bg-slate-50' => ! $equipo->en_traslado && ! $equipo->en_disponible && ! $equipo->en_baja,
                                ])
                                data-expand-row
                                data-expand-target="detalle-equipo-{{ $equipo->id }}"
                                aria-expanded="false"
                            >
                                <td class="break-words px-2 py-2.5">{{ $equipo->item ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $equipo->codigo ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">
                                    <span @class(['line-through text-slate-400' => $equipo->en_disponible])>{{ $equipo->usuario ?: '—' }}</span>
                                    @if ($equipo->en_disponible)
                                        <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-wide text-sky-700">Disponible</span>
                                    @endif
                                </td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $equipo->en_disponible])>{{ $equipo->departamento ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $equipo->descripcion ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5" title="{{ $equipo->marca_referencia }}">{{ $equipo->marca_referencia ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900" title="{{ collect([$equipo->service_tag_product_name, $equipo->serial_adicional])->filter()->implode(' / ') }}">
                                    {{ $equipo->service_tag_product_name ?: '—' }}
                                    @if (filled($equipo->serial_adicional))
                                        <span class="mt-0.5 block text-[10px] font-normal text-slate-500">{{ $equipo->serial_adicional }}</span>
                                    @endif
                                </td>
                                <td class="break-words px-2 py-2.5">{{ $equipo->windows ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5" title="{{ $equipo->office }}">{{ $equipo->office ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $equipo->en_disponible])>{{ $equipo->cargo ?: '—' }}</td>
                                <td class="px-2 py-2.5">
                                    <div class="flex items-center gap-2">
                                        @include('relacion.equipos-computo.acciones', ['equipo' => $equipo])
                                        <div class="flex items-center gap-1.5" data-expand-ignore>
                                            <a href="{{ route('equipos-computo.edit', $equipo) }}" class="text-brand-green hover:text-brand-green-dark" title="Editar" aria-label="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 1 1 2.97 2.97L8.25 18.04 4 19.5l1.46-4.25 11.402-11.763Z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('equipos-computo.destroy', $equipo) }}" onsubmit="return confirm('¿Eliminar este equipo?')">
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
                                id="detalle-equipo-{{ $equipo->id }}"
                                @class([
                                    'hidden',
                                    'bg-red-50' => $equipo->en_baja,
                                    'bg-orange-50' => $equipo->en_traslado && ! $equipo->en_baja,
                                    'bg-sky-50' => $equipo->en_disponible && ! $equipo->en_traslado && ! $equipo->en_baja,
                                    'bg-slate-50' => ! $equipo->en_traslado && ! $equipo->en_disponible && ! $equipo->en_baja,
                                ])
                            >
                                <td colspan="11" class="px-4 py-5">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        @foreach ($groups as $group => $fields)
                                            <section @class(['space-y-3', 'md:col-span-2' => $group === 'Adjuntos' || $group === 'Fechas y observaciones'])>
                                                <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</h2>
                                                <dl @class([
                                                    'grid gap-3',
                                                    'sm:grid-cols-3' => in_array($group, ['Adjuntos', 'Fechas y observaciones'], true),
                                                    'sm:grid-cols-2' => ! in_array($group, ['Adjuntos', 'Fechas y observaciones'], true),
                                                ])>
                                                    @foreach ($fields as $name => $field)
                                                        <div @class(['sm:col-span-3' => $field['type'] === 'textarea' && $group === 'Fechas y observaciones', 'sm:col-span-2' => $field['type'] === 'textarea' && $group !== 'Fechas y observaciones'])>
                                                            <dt class="text-xs font-medium text-slate-500">{{ $field['label'] }}</dt>
                                                            <dd @class([
                                                                'mt-1 text-sm',
                                                                'line-through text-slate-400' => $equipo->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true),
                                                                'text-slate-900' => ! ($equipo->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true)),
                                                            ])>
                                                                @if ($field['type'] === 'image')
                                                                    @if ($equipo->urlFoto($name))
                                                                        <a href="{{ $equipo->urlFoto($name) }}" target="_blank" rel="noopener" class="block">
                                                                            <img src="{{ $equipo->urlFoto($name) }}" alt="{{ $field['label'] }}" class="h-36 w-full rounded-lg border border-slate-200 object-cover">
                                                                        </a>
                                                                    @else
                                                                        —
                                                                    @endif
                                                                @elseif ($field['type'] === 'date')
                                                                    <span class="inline-flex flex-wrap items-center gap-2">
                                                                        <span>{{ $equipo->{$name}?->format('d/m/Y') ?: '—' }}</span>
                                                                        @if ($name === 'fecha_garantia' && $equipo->garantiaCaducada())
                                                                            <span class="rounded-full bg-brand-red/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-brand-red">Caducado</span>
                                                                        @endif
                                                                    </span>
                                                                @elseif ($field['type'] === 'computed')
                                                                    {{ $equipo->antiguedad() }}
                                                                @else
                                                                    {{ $equipo->{$name} ?: '—' }}
                                                                @endif
                                                            </dd>
                                                        </div>

                                                        @if (isset($field['optional']))
                                                            @php $extra = $field['optional']; @endphp
                                                            <div>
                                                                <dt class="text-xs font-medium text-slate-500">{{ $extra['label'] }}</dt>
                                                                <dd class="mt-1 text-sm text-slate-900">
                                                                    @if ($field['type'] === 'image')
                                                                        @if ($equipo->urlFoto($extra['name']))
                                                                            <a href="{{ $equipo->urlFoto($extra['name']) }}" target="_blank" rel="noopener" class="block">
                                                                                <img src="{{ $equipo->urlFoto($extra['name']) }}" alt="{{ $extra['label'] }}" class="h-36 w-full rounded-lg border border-slate-200 object-cover">
                                                                            </a>
                                                                        @else
                                                                            —
                                                                        @endif
                                                                    @else
                                                                        {{ $equipo->{$extra['name']} ?: '—' }}
                                                                    @endif
                                                                </dd>
                                                            </div>
                                                        @endif
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
