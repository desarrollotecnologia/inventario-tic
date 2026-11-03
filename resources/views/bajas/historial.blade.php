<x-layouts.app title="Historial de Bajas">
    <a href="{{ route('modules.bajas') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Baja de Equipos</a>

    <x-portada-modulo
        title="Historial de Bajas"
        subtitle="Bajas ya notificadas por correo. Aquí quedan los bloques enviados."
    />

    @if ($bajas->isEmpty())
        <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
            <div class="overflow-hidden rounded-[14px] bg-white">
                <div class="flex h-1.5">
                    <div class="w-1/2 bg-brand-green"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>
                <p class="px-6 py-10 text-sm text-slate-500">Aún no hay bajas enviadas en el historial.</p>
            </div>
        </section>
    @else
        @include('bajas.partials.tabla', [
            'bajas' => $bajas,
            'titulo' => 'Bajas notificadas',
        ])
    @endif
</x-layouts.app>
