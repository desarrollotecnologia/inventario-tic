<label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-1.5 text-sm font-semibold text-sky-800">
    <input
        type="checkbox"
        name="disponible"
        value="1"
        @checked(($filtros['disponible'] ?? '') === '1')
        onchange="this.form.submit()"
        class="h-3.5 w-3.5 rounded border-sky-300 text-sky-700 focus:ring-sky-500"
    >
    Solo disponibles
</label>
<label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-1.5 text-sm font-semibold text-orange-800">
    <input
        type="checkbox"
        name="traslado"
        value="1"
        @checked(($filtros['traslado'] ?? '') === '1')
        onchange="this.form.submit()"
        class="h-3.5 w-3.5 rounded border-orange-300 text-orange-700 focus:ring-orange-500"
    >
    Solo traslados
</label>
<label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-800">
    <input
        type="checkbox"
        name="baja"
        value="1"
        @checked(($filtros['baja'] ?? '') === '1')
        onchange="this.form.submit()"
        class="h-3.5 w-3.5 rounded border-red-300 text-red-700 focus:ring-red-500"
    >
    Solo bajas
</label>
