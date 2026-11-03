<x-layouts.app title="Baja de Equipos">
    <x-portada-modulo
        title="Baja de Equipos"
        subtitle="Completa el acta ítem por ítem. En verde quedan listas para enviar; al pulsar Enviar bajas pasan al historial."
    >
        <x-slot:actions>
            <a
                href="{{ route('bajas.historial') }}"
                class="inline-flex items-center justify-center rounded-lg border-2 border-white/40 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10"
            >
                Historial de Bajas
            </a>
            @if ($pendientes->isNotEmpty())
                <a
                    href="{{ route('bajas.edit', $pendientes->first()) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-red px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-red-dark"
                >
                    Completar siguiente acta
                </a>
            @endif
            @if ($porNotificar->isNotEmpty())
                <button
                    type="button"
                    data-open-dialog="modal-correo"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark"
                >
                    Enviar bajas
                </button>
            @endif
        </x-slot:actions>
    </x-portada-modulo>

    @if ($listado->isEmpty())
        <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
            <div class="overflow-hidden rounded-[14px] bg-white">
                <div class="flex h-1.5">
                    <div class="w-1/2 bg-brand-green"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay equipos marcados para baja. Márcalos con B en el inventario para iniciar.</p>
            </div>
        </section>
    @else
        <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
            <div class="overflow-hidden rounded-[14px] bg-white">
                <div class="flex h-1.5">
                    <div class="w-1/2 bg-brand-green"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>
                <div class="border-b border-slate-100 px-4 py-3">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Bajas marcadas ({{ $listado->count() }})
                        <span class="ml-2 font-normal text-slate-500">
                            · {{ $pendientes->count() }} pendientes
                            · {{ $completadas->count() }} listas para enviar
                        </span>
                    </h2>
                    <p class="mt-0.5 text-xs text-slate-500">Rojo: falta diligenciar el acta. Verde: información completa (aún en el módulo hasta enviar el correo).</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[60rem] w-full table-fixed text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                            <tr>
                                <th class="w-[6%] px-2 py-2.5 font-medium">#</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Categoría</th>
                                <th class="w-[10%] px-2 py-2.5 font-medium">Código</th>
                                <th class="w-[14%] px-2 py-2.5 font-medium">Usuario</th>
                                <th class="w-[14%] px-2 py-2.5 font-medium">Descripción</th>
                                <th class="w-[12%] px-2 py-2.5 font-medium">Serial</th>
                                <th class="w-[14%] px-2 py-2.5 font-medium">Departamento</th>
                                <th class="w-[18%] px-2 py-2.5 font-medium">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($listado as $baja)
                                <tr @class([
                                    'text-slate-700',
                                    'bg-brand-green-soft/70' => $baja->estaCompletada(),
                                    'bg-red-50/50' => ! $baja->estaCompletada(),
                                ])>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $loop->iteration }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $baja->categoria ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $baja->codigoMostrado() }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $baja->usuario ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $baja->descripcion ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $baja->serial ?: '—' }}</td>
                                    <td class="break-words px-2 py-2.5">{{ $baja->departamento ?: '—' }}</td>
                                    <td class="px-2 py-2.5">
                                        @if ($baja->estaCompletada())
                                            <span class="inline-flex items-center rounded-lg bg-brand-green px-3 py-1.5 text-xs font-semibold text-white">
                                                Lista para enviar
                                            </span>
                                        @else
                                            <a
                                                href="{{ route('bajas.edit', $baja) }}"
                                                class="inline-flex items-center rounded-lg bg-brand-red px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-red-dark"
                                            >
                                                Completar acta
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    @if ($porNotificar->isNotEmpty())
        @include('bajas.partials.modales-correo')
    @endif
</x-layouts.app>
