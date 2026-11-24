<x-layouts.app title="Acta de Entrega">
    <a href="{{ route('modules.actas') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Actas</a>

    <x-portada-modulo
        title="Acta de Entrega"
        subtitle="Registra la entrega de equipos a usuarios o áreas e imprime el formato con dos copias."
    >
        <x-slot:actions>
            <a href="{{ route('actas.entrega.create') }}" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Nueva acta de entrega
            </a>
        </x-slot:actions>
    </x-portada-modulo>

    @if (session('status'))
        <p class="mt-4 rounded-lg bg-brand-green-soft px-4 py-3 text-sm text-brand-green-dark">{{ session('status') }}</p>
    @endif

    <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>

            @if ($actas->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-sm font-medium text-slate-700">Aún no hay actas de entrega.</p>
                    <p class="mt-2 text-sm text-slate-500">Crea la primera para generar el formato imprimible.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Fecha</th>
                                <th class="px-4 py-3 font-medium">Destino</th>
                                <th class="px-4 py-3 font-medium">Entrega</th>
                                <th class="px-4 py-3 font-medium">Equipos</th>
                                <th class="px-4 py-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($actas as $acta)
                                <tr class="text-slate-700">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $acta->fecha?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ $acta->destino }}</td>
                                    <td class="px-4 py-3">{{ $acta->entregado_por_nombre }}</td>
                                    <td class="px-4 py-3">{{ count($acta->equipos ?? []) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('actas.entrega.formato', $acta) }}" class="font-semibold text-brand-green hover:text-brand-green-dark">Imprimir</a>
                                        <span class="mx-1 text-slate-300">|</span>
                                        <a href="{{ route('actas.entrega.edit', $acta) }}" class="font-semibold text-slate-600 hover:text-slate-900">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $actas->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
