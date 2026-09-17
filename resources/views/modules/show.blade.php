<x-layouts.app :title="$title">
    <a href="{{ route('home') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver al inicio</a>

    <x-portada-modulo
        :title="$title"
        subtitle="Equipos, celulares, tablets, radios e impresoras marcados desde el inventario."
    />

    @if ($equipos->isEmpty() && $celulares->isEmpty() && $radios->isEmpty() && $impresoras->isEmpty())
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <p class="px-6 py-10 text-sm text-slate-500">{{ $vacio }}</p>
        </section>
    @endif

    @if ($equipos->isNotEmpty())
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div>
                <table class="w-full table-fixed text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
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
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($equipos as $equipo)
                            <tr @class([
                                'text-slate-700',
                                'bg-sky-100' => $equipo->en_disponible && ! $equipo->en_traslado,
                            ])>
                                <td class="break-words px-2 py-2.5">{{ $equipo->item ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $equipo->codigo ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $equipo->en_disponible])>{{ $equipo->usuario ?: '—' }}</td>
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
                                    @include('relacion.equipos-computo.acciones', ['equipo' => $equipo])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($celulares->isNotEmpty())
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div>
                <table class="w-full table-fixed text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                        <tr>
                            <th class="w-[6%] px-2 py-2.5 font-medium">Ítem</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Tipo</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Usuario</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Departamento</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Marca</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Modelo</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Serial</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Número</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($celulares as $celular)
                            <tr @class([
                                'text-slate-700',
                                'bg-sky-100' => $celular->en_disponible && ! $celular->en_traslado,
                            ])>
                                <td class="break-words px-2 py-2.5">{{ $celular->item ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $celular->tipo ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $celular->en_disponible])>{{ $celular->usuario ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $celular->en_disponible])>{{ $celular->departamento ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $celular->marca ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $celular->modelo ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $celular->serial ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $celular->numero_celular ?: '—' }}</td>
                                <td class="px-2 py-2.5">
                                    @include('relacion.equipos-computo.acciones', [
                                        'modelo' => $celular,
                                        'rutaAccion' => route('celulares.accion', $celular),
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($radios->isNotEmpty())
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div>
                <table class="w-full table-fixed text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                        <tr>
                            <th class="w-[6%] px-2 py-2.5 font-medium">Ítem</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Tipo</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Usuario</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Departamento</th>
                            <th class="w-[16%] px-2 py-2.5 font-medium">Marca y Referencia</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Modelo</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Serial</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($radios as $radio)
                            <tr @class([
                                'text-slate-700',
                                'bg-sky-100' => $radio->en_disponible && ! $radio->en_traslado,
                            ])>
                                <td class="break-words px-2 py-2.5">{{ $radio->item ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $radio->tipo ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $radio->en_disponible])>{{ $radio->usuario ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $radio->en_disponible])>{{ $radio->departamento ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $radio->marca_referencia ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $radio->modelo ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $radio->serial ?: '—' }}</td>
                                <td class="px-2 py-2.5">
                                    @include('relacion.equipos-computo.acciones', [
                                        'modelo' => $radio,
                                        'rutaAccion' => route('radios.accion', $radio),
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if ($impresoras->isNotEmpty())
        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div>
                <table class="w-full table-fixed text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                        <tr>
                            <th class="w-[6%] px-2 py-2.5 font-medium">Ítem</th>
                            <th class="w-[12%] px-2 py-2.5 font-medium">Tipo</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Usuario</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Departamento</th>
                            <th class="w-[16%] px-2 py-2.5 font-medium">Descripción</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Modelo</th>
                            <th class="w-[14%] px-2 py-2.5 font-medium">Serial</th>
                            <th class="w-[10%] px-2 py-2.5 font-medium">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($impresoras as $impresora)
                            <tr @class([
                                'text-slate-700',
                                'bg-sky-100' => $impresora->en_disponible && ! $impresora->en_traslado,
                            ])>
                                <td class="break-words px-2 py-2.5">{{ $impresora->item ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $impresora->tipo ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $impresora->en_disponible])>{{ $impresora->usuario ?: '—' }}</td>
                                <td @class(['break-words px-2 py-2.5', 'line-through text-slate-400' => $impresora->en_disponible])>{{ $impresora->departamento ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $impresora->descripcion ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5">{{ $impresora->modelo ?: '—' }}</td>
                                <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $impresora->serial ?: '—' }}</td>
                                <td class="px-2 py-2.5">
                                    @include('relacion.equipos-computo.acciones', [
                                        'modelo' => $impresora,
                                        'rutaAccion' => route('impresoras.accion', $impresora),
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-layouts.app>
