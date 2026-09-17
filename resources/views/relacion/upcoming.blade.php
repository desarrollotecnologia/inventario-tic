<x-layouts.app :title="$title">
    <a href="{{ route('modules.relacion') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Relación Inventario Sistemas</a>

    <x-portada-modulo
        :title="$title"
        subtitle="Este submódulo quedará separado del inventario de equipos de cómputo. El formulario se habilitará cuando se definan sus campos."
    />
</x-layouts.app>
