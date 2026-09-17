<div class="flex shrink-0 items-center gap-1" data-expand-ignore>
    @php
        $modelo = $modelo ?? $equipo;
        $rutaAccion = $rutaAccion ?? route('equipos-computo.accion', $modelo);
    @endphp
    @foreach ([['en_disponible', 'Disponible', 'D'], ['en_traslado', 'Traslado', 'T'], ['en_baja', 'Baja', 'B']] as [$campo, $etiqueta, $inicial])
        <form method="POST" action="{{ $rutaAccion }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="campo" value="{{ $campo }}">
            <input type="hidden" name="valor" value="0">
            <label class="inline-flex cursor-pointer items-center gap-1 text-[11px] font-semibold text-slate-600" title="{{ $etiqueta }}">
                <input
                    type="checkbox"
                    name="valor"
                    value="1"
                    @checked($modelo->{$campo})
                    onchange="this.form.submit()"
                    class="h-3.5 w-3.5 rounded border-slate-300 text-brand-green focus:ring-brand-green"
                >
                {{ $inicial }}
            </label>
        </form>
    @endforeach
</div>
