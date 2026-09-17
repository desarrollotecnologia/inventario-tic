<x-layouts.app title="Historial de Traslados">
    <a href="{{ route('modules.traslados') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Traslados</a>

    <x-portada-modulo
        title="Historial de Traslados"
        subtitle="Consulta los movimientos que ya se notificaron por correo."
    />

    @if ($realizados->isEmpty())
        <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
            <div class="overflow-hidden rounded-[14px] bg-white">
                <div class="flex h-1.5">
                    <div class="w-1/2 bg-brand-green"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay traslados en el historial. Aparecen aquí después de enviar el correo.</p>
            </div>
        </section>
    @else
        @include('traslados.partials.tabla-realizados', [
            'traslados' => $realizados,
            'titulo' => 'Traslados realizados',
            'conAcciones' => false,
        ])
    @endif
</x-layouts.app>
