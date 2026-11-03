<x-layouts.app title="Acta de Baja de Activos">
    <a href="{{ route('modules.bajas') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Baja de Equipos</a>

    <x-portada-modulo
        title="Acta de Baja de Activos Fijos e Inventarios"
        subtitle="Formato RFR-AF2. Completa los datos del solicitante, el motivo y adjunta los soportes del ítem."
    >
        <x-slot:badge>
            <span class="rounded-full bg-white/15 px-2.5 py-1 text-xs font-semibold text-white">
                {{ $pendientesRestantes }} {{ $pendientesRestantes === 1 ? 'pendiente' : 'pendientes' }}
            </span>
        </x-slot:badge>
    </x-portada-modulo>

    <section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
        <div class="overflow-hidden rounded-[14px] bg-white">
            <div class="flex h-1.5">
                <div class="w-1/2 bg-brand-green"></div>
                <div class="w-1/2 bg-brand-red"></div>
            </div>

            <div class="border-b border-slate-100 px-4 py-3 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Activo a dar de baja</p>
                <div class="mt-2 grid gap-2 text-sm text-slate-700 sm:grid-cols-2 lg:grid-cols-4">
                    <p><span class="font-medium text-slate-500">Categoría:</span> {{ $baja->categoria ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Código:</span> {{ $baja->codigoMostrado() }}</p>
                    <p><span class="font-medium text-slate-500">Tipo:</span> {{ $baja->tipo ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Serial:</span> {{ $baja->serial ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Descripción:</span> {{ $baja->descripcion ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Usuario:</span> {{ $baja->usuario ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Departamento:</span> {{ $baja->departamento ?: '—' }}</p>
                    <p><span class="font-medium text-slate-500">Área:</span> {{ $baja->area ?: '—' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('bajas.update', $baja) }}" enctype="multipart/form-data" class="registro-mayusculas space-y-6 px-4 py-5 sm:px-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="acta_numero" value="{{ $actaNumero }}">
                <input type="hidden" name="fecha" value="{{ now()->toDateString() }}">
                <input type="hidden" name="solicitante_departamento" value="{{ $departamento }}">

                <fieldset>
                    <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Datos del acta</legend>
                    <div class="grid gap-3 sm:grid-cols-12">
                        <div class="sm:col-span-2">
                            <label for="acta_numero" class="mb-1 block text-sm font-medium text-slate-700">Acta No.</label>
                            <input id="acta_numero" type="text" value="{{ $actaNumero }}" readonly class="block w-full max-w-[7rem] cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="fecha" class="mb-1 block text-sm font-medium text-slate-700">Fecha</label>
                            <input id="fecha" type="date" value="{{ now()->toDateString() }}" readonly class="block w-full max-w-[11rem] cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700">
                        </div>
                        <div class="sm:col-span-4">
                            <label for="solicitante_nombre" class="mb-1 block text-sm font-medium text-slate-700">Nombre completo</label>
                            <x-combobox
                                name="solicitante_nombre"
                                :options="$solicitantes"
                                :value="$solicitanteNombre"
                                :allow-custom="false"
                                manage-dialog="modal-solicitantes"
                                manage-label="+ Agregar / gestionar"
                                placeholder="Selecciona el solicitante"
                                class="relative z-20"
                            />
                            @error('solicitante_nombre') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="solicitante_cargo" class="mb-1 block text-sm font-medium text-slate-700">Cargo</label>
                            <x-combobox
                                name="solicitante_cargo"
                                :options="$cargos"
                                :value="$solicitanteCargo"
                                :allow-custom="false"
                                placeholder="Cargo TIC"
                            />
                            @error('solicitante_cargo') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="solicitante_departamento" class="mb-1 block text-sm font-medium text-slate-700">Departamento</label>
                            <input id="solicitante_departamento" type="text" value="{{ $departamento }}" readonly title="{{ $departamento }}" class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Información</legend>
                    <div class="space-y-3">
                        <div>
                            <label for="informacion" class="mb-1 block text-sm font-medium text-slate-700">Se solicita dar de baja al siguiente activo</label>
                            <textarea id="informacion" name="informacion" rows="3" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">{{ old('informacion', $baja->informacion) }}</textarea>
                            @error('informacion') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="motivo_baja" class="mb-1 block text-sm font-medium text-slate-700">Motivo de la baja</label>
                            <textarea id="motivo_baja" name="motivo_baja" rows="4" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">{{ old('motivo_baja', $baja->motivo_baja) }}</textarea>
                            @error('motivo_baja') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="observaciones" class="mb-1 block text-sm font-medium text-slate-700">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" rows="4" placeholder="Ej. historial de mantenimientos, fechas y detalles que deben ir en el correo" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">{{ old('observaciones', $baja->observaciones) }}</textarea>
                            <p class="mt-1 text-xs text-slate-500">Este texto es el que aparece en la columna Observaciones del correo de bajas.</p>
                            @error('observaciones') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Adjuntos</legend>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="adjunto_acta" class="mb-1 block text-sm font-medium text-slate-700">Firma del solicitante</label>
                            <input id="adjunto_acta" name="adjunto_acta" type="file" accept="image/jpeg,image/png,image/webp,.pdf" class="block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-green-dark">
                            <p class="mt-1 text-xs text-slate-500">Imagen de la firma (JPG, PNG, WEBP o PDF). Máximo 10 MB.</p>
                            @if ($baja->urlAdjunto('adjunto_acta'))
                                <a href="{{ $baja->urlAdjunto('adjunto_acta') }}" target="_blank" class="mt-1 inline-block text-xs font-medium text-brand-green hover:text-brand-green-dark">Ver firma actual</a>
                            @endif
                            @error('adjunto_acta') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="adjunto_evidencia" class="mb-1 block text-sm font-medium text-slate-700">Evidencia fotográfica</label>
                            <input id="adjunto_evidencia" name="adjunto_evidencia[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-green-dark">
                            <p class="mt-1 text-xs text-slate-500">Puedes seleccionar varias imágenes (JPG, PNG o WEBP). Máximo 10 MB c/u.</p>
                            @if ($baja->urlsEvidencia() !== [])
                                <div class="mt-1 flex flex-col gap-0.5">
                                    @foreach ($baja->urlsEvidencia() as $indice => $url)
                                        <a href="{{ $url }}" target="_blank" class="inline-block text-xs font-medium text-brand-green hover:text-brand-green-dark">Ver imagen {{ $indice + 1 }}</a>
                                    @endforeach
                                </div>
                            @endif
                            @error('adjunto_evidencia') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                            @error('adjunto_evidencia.*') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="adjunto_soporte" class="mb-1 block text-sm font-medium text-slate-700">Soporte del diagnóstico de la baja</label>
                            <input id="adjunto_soporte" name="adjunto_soporte" type="file" accept=".pdf,image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-green-dark">
                            <p class="mt-1 text-xs text-slate-500">Opcional. PDF, JPG, PNG o WEBP. Máximo 10 MB.</p>
                            @if ($baja->urlAdjunto('adjunto_soporte'))
                                <a href="{{ $baja->urlAdjunto('adjunto_soporte') }}" target="_blank" class="mt-1 inline-block text-xs font-medium text-brand-green hover:text-brand-green-dark">Ver archivo actual</a>
                            @endif
                            @error('adjunto_soporte') <p class="mt-1 text-xs text-brand-red">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">El formato del acta RFR-AF2 se genera automáticamente con estos datos. Aquí solo cargas la firma del solicitante y los soportes.</p>
                </fieldset>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('modules.bajas') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">Cancelar</a>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-red px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-red-dark">
                            {{ $siguiente ? 'Guardar y continuar al siguiente' : 'Guardar acta de baja' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <dialog id="modal-solicitantes" class="w-[min(28rem,calc(100%-2rem))] rounded-2xl border border-slate-200 bg-white p-0 shadow-xl backdrop:bg-slate-900/40">
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Solicitantes del acta</h3>
                    <p class="mt-1 text-xs text-slate-500">Agrega o elimina nombres del listado.</p>
                </div>
                <button type="button" data-close-dialog class="text-sm font-medium text-slate-500 hover:text-slate-800">Cerrar</button>
            </div>

            <form method="POST" action="{{ route('bajas.solicitantes.store') }}" class="registro-mayusculas mt-4 flex flex-wrap items-end gap-2">
                @csrf
                <input type="hidden" name="baja_id" value="{{ $baja->id }}">
                <div class="min-w-[12rem] flex-1">
                    <label for="nuevo_solicitante" class="mb-1 block text-sm font-medium text-slate-700">Nuevo solicitante</label>
                    <input id="nuevo_solicitante" name="nombre" type="text" required placeholder="Nombre completo" class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft">
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-green px-3 py-2 text-sm font-semibold text-white hover:bg-brand-green-dark">
                    + Agregar
                </button>
            </form>

            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($registrosSolicitantes as $solicitante)
                    <form method="POST" action="{{ route('bajas.solicitantes.destroy', $solicitante) }}" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700" onsubmit="return confirm('¿Eliminar a {{ $solicitante->nombre }} del catálogo?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="baja_id" value="{{ $baja->id }}">
                        <span>{{ $solicitante->nombre }}</span>
                        <button type="submit" class="rounded-full px-1 text-brand-red hover:bg-brand-red-soft" title="Eliminar">×</button>
                    </form>
                @empty
                    <p class="text-xs text-slate-500">No hay solicitantes en el catálogo.</p>
                @endforelse
            </div>
        </div>
    </dialog>
</x-layouts.app>
