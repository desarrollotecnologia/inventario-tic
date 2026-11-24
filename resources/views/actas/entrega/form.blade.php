<x-layouts.app :title="$title">
    <a href="{{ route('actas.entrega') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Acta de Entrega</a>

    <x-portada-modulo
        :title="$title"
        subtitle="Completa los datos del formato. Al guardar se abre la vista para imprimir con dos copias."
    />

    <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>

            @include('actas.partials.formulario', [
                'acta' => $acta,
                'inventario' => $inventario,
                'action' => $action,
                'method' => $method,
                'cancelUrl' => route('actas.entrega'),
                'destinoLabel' => 'Persona / área que recibe',
                'propositoLabel' => 'Propósito (opcional)',
                'propositoRequerido' => false,
            ])
        </div>
    </section>
</x-layouts.app>
