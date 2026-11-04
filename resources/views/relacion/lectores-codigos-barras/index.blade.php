<x-layouts.app title="Lectores Códigos de Barras">
    <a href="{{ route('modules.relacion') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Relación Inventario Sistemas</a>

    <x-portada-modulo
        title="Lectores Códigos de Barras"
        subtitle="Haz clic en una fila para ver el detalle. Marca D si está disponible: el responsable se tacha y no se borra."
    >
        <x-slot:badge>
            <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-semibold text-white">
                @if ($hayFiltros)
                    {{ $lectoresFiltrados }} de {{ $totalLectores }} {{ $totalLectores === 1 ? 'registro' : 'registros' }}
                @else
                    {{ $totalLectores }} {{ $totalLectores === 1 ? 'registro' : 'registros' }}
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
            <a href="{{ route('lectores-codigos-barras.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Registrar lector
            </a>
        </x-slot:actions>
    </x-portada-modulo>

    <form method="GET" action="{{ route('lectores-codigos-barras.index') }}" class="registro-mayusculas borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>
            <div class="px-3 py-2.5">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-800">Búsqueda rápida</h2>
                    <a href="{{ route('lectores-codigos-barras.index') }}" class="text-xs font-medium text-brand-green hover:text-brand-green-dark">Limpiar</a>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto">
                    <input type="text" name="codigo_contable" value="{{ $filtros['codigo_contable'] }}" placeholder="Código contable" class="min-w-[8rem] max-w-[9.5rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <input type="text" name="usuario" value="{{ $filtros['usuario'] }}" placeholder="Usuario" class="min-w-[8rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <select name="departamento" onchange="this.form.submit()" class="min-w-[9rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                        <option value="">Departamento</option>
                        @foreach (\App\Support\EquipoComputoFields::departamentos() as $opcion)
                            <option value="{{ $opcion }}" @selected($filtros['departamento'] === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="marca_referencia" value="{{ $filtros['marca_referencia'] }}" placeholder="Marca y Referencia" class="min-w-[9rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <input type="text" name="modelo" value="{{ $filtros['modelo'] }}" placeholder="Modelo" class="min-w-[7rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                    <input type="text" name="serial" value="{{ $filtros['serial'] }}" placeholder="Serial" class="min-w-[7rem] max-w-[9.5rem] flex-1 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <button type="submit" class="rounded-lg bg-brand-green px-3 py-1.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                        Buscar
                    </button>
                    @include('relacion.partials.filtros-estado')
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
            @if (! $hayLectores)
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay lectores de códigos de barras registrados.</p>
            @elseif ($lectores->isEmpty())
                <p class="px-6 py-10 text-sm text-slate-500">No hay lectores que coincidan con el filtro.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="listado-mayusculas min-w-[70rem] w-full table-fixed text-left text-xs">
                        <thead class="bg-brand-green-soft text-[11px] uppercase leading-tight tracking-wide text-brand-green-dark">
                            <tr>
                                <th class="w-[7%] px-2 py-2.5 font-medium">N° Inventario</th>
                                <th class="w-[10%] px-2 py-2.5 font-medium">Código Contable</th>
                                <th class="w-[11%] px-2 py-2.5 font-medium">Departamento</th>
                                <th class="w-[9%] px-2 py-2.5 font-medium">Área</th>
                                <th class="w-[11%] px-2 py-2.5 font-medium">Usuario</th>
                                <th class="w-[10%] px-2 py-2.5 font-medium">Cargo</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Marca y Referencia</th>
                                <th class="w-[9%] px-2 py-2.5 font-medium">Modelo</th>
                                <th class="w-[10%] px-2 py-2.5 font-medium">Serial</th>
                                <th class="w-[11%] px-2 py-2.5 font-medium">Acción</th>
                            </tr>
                        </thead>
                        @foreach ($lectores as $lector)
                            <tbody class="border-t border-slate-100">
                                <tr
                                    @class([
                                        'cursor-pointer text-slate-700',
                                        'bg-red-100 hover:bg-red-200' => $lector->en_baja,
                                        'bg-orange-100 hover:bg-orange-200' => $lector->en_traslado && ! $lector->en_baja,
                                        'bg-sky-100 hover:bg-sky-200' => $lector->en_disponible && ! $lector->en_traslado && ! $lector->en_baja,
                                        'hover:bg-slate-50' => ! $lector->en_traslado && ! $lector->en_disponible && ! $lector->en_baja,
                                    ])
                                    data-expand-row
                                    data-expand-target="detalle-lector-{{ $lector->id }}"
                                    aria-expanded="false"
                                >
                                    <td class="break-words px-2 py-2.5">{{ $lector->item ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $lector->codigo_contable ?: '—' }}</td>
                                    <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $lector->en_disponible])>{{ $lector->departamento ?: '—' }}</td>
                                    <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $lector->en_disponible])>{{ $lector->area ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">
                                        <span @class(['line-through text-slate-400' => $lector->en_disponible])>{{ $lector->usuario ?: '—' }}</span>
                                        @if ($lector->en_disponible)
                                            <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-wide text-sky-700">Disponible</span>
                                        @endif
                                    </td>
                                    <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $lector->en_disponible])>{{ $lector->cargo ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $lector->marca_referencia ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $lector->modelo ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $lector->serial ?: '—' }}</td>
                                    <td class="px-2 py-2.5">
                                        <div class="flex items-center gap-1.5">
                                            @include('relacion.equipos-computo.acciones', [
                                                'modelo' => $lector,
                                                'rutaAccion' => route('lectores-codigos-barras.accion', $lector),
                                            ])
                                            <div class="flex items-center gap-1.5" data-expand-ignore>
                                                <a href="{{ route('lectores-codigos-barras.edit', $lector) }}" class="text-brand-green hover:text-brand-green-dark" title="Editar" aria-label="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 1 1 2.97 2.97L8.25 18.04 4 19.5l1.46-4.25 11.402-11.763Z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('lectores-codigos-barras.destroy', $lector) }}" onsubmit="return confirm('¿Eliminar este lector?')">
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
                                    id="detalle-lector-{{ $lector->id }}"
                                    @class([
                                        'hidden',
                                        'bg-red-50' => $lector->en_baja,
                                        'bg-orange-50' => $lector->en_traslado && ! $lector->en_baja,
                                        'bg-sky-50' => $lector->en_disponible && ! $lector->en_traslado && ! $lector->en_baja,
                                        'bg-slate-50' => ! $lector->en_traslado && ! $lector->en_disponible && ! $lector->en_baja,
                                    ])
                                >
                                    <td colspan="10" class="px-4 py-5">
                                        <div class="grid gap-6 md:grid-cols-2">
                                            @foreach ($groups as $group => $fields)
                                                <section @class(['space-y-3', 'md:col-span-2' => $group === 'Observaciones'])>
                                                    <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</h2>
                                                    <dl class="grid gap-3 sm:grid-cols-2">
                                                        @foreach ($fields as $name => $field)
                                                            <div @class(['sm:col-span-2' => $field['type'] === 'textarea'])>
                                                                <dt class="text-xs font-medium text-slate-500">{{ $field['label'] }}</dt>
                                                                <dd @class([
                                                                    'mt-1 text-sm whitespace-pre-wrap',
                                                                    'line-through text-slate-400' => $lector->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true),
                                                                    'text-slate-900' => ! ($lector->en_disponible && in_array($name, ['usuario', 'cargo', 'departamento', 'area'], true)),
                                                                ])>
                                                                    {{ $lector->{$name} ?: '—' }}
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
