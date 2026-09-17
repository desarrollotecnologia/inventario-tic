@php
    $abrirCorreo = session('abrir') === 'correo'
        || $errors->has('correos')
        || $errors->has('destinatarios')
        || $errors->has('firma_id')
        || collect($errors->keys())->contains(fn (string $key): bool => str_starts_with($key, 'destinatarios.'));
    $abrirFirma = $errors->has('nombre') || $errors->has('contenido') || $errors->has('imagen');
@endphp
<script type="application/json" data-abrir-dialogos>@json(['correo' => $abrirCorreo, 'firma' => $abrirFirma])</script>
<dialog id="modal-correo" class="w-[min(36rem,calc(100%-2rem))] overflow-visible rounded-2xl border border-slate-200 bg-white p-0 shadow-xl backdrop:bg-slate-900/40">
    <form method="POST" action="{{ route('traslados.correo') }}" class="p-5">
        @csrf
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Enviar por correo</h3>
                <p class="mt-1 text-xs text-slate-500">Se envía desde tecnologia@colbeef.com. Escribe o elige los correos a los que llega.</p>
            </div>
            <button type="button" data-close-dialog class="text-sm font-medium text-slate-500 hover:text-slate-800">Cerrar</button>
        </div>

        <div class="mt-4" data-email-picker>
            <span class="mb-1.5 block text-sm font-medium text-slate-700">Correos</span>
            <input type="hidden" name="correos" value="{{ old('correos') }}" data-email-hidden>
            <div class="mb-2 flex min-h-8 flex-wrap gap-1.5" data-email-chips></div>
            <div class="relative z-20" data-filter-combobox>
                <input
                    type="text"
                    value=""
                    autocomplete="off"
                    placeholder="Escribe o elige un correo"
                    data-combobox-input
                    data-email-input
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pr-10 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                >
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
                <ul
                    class="fixed z-[9999] mt-1 hidden max-h-72 min-w-[16rem] overflow-auto rounded-lg border border-slate-300 bg-white py-1 text-sm shadow-xl"
                    data-combobox-list
                    role="listbox"
                >
                    @foreach ($correosDestino as $correo)
                        <li
                            class="cursor-pointer px-3 py-2 text-sm text-slate-800 hover:bg-brand-green-soft hover:text-brand-green-dark"
                            data-combobox-option
                            data-value="{{ $correo }}"
                            role="option"
                        >{{ $correo }}</li>
                    @endforeach
                    <li
                        class="cursor-pointer border-t border-slate-200 px-3 py-2 text-sm font-medium text-brand-green hover:bg-brand-green-soft"
                        data-combobox-option
                        data-combobox-custom
                        data-value="Nuevo"
                        role="option"
                    >Nuevo</li>
                    <li class="hidden px-3 py-2 text-sm text-slate-400" data-combobox-empty>
                        Sin coincidencias. Elige Nuevo o escribe el correo.
                    </li>
                </ul>
            </div>
        </div>
        @error('correos')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror
        @error('destinatarios')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror
        @error('destinatarios.0')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror

        <p class="mt-4 text-sm text-slate-700">
            <span class="font-medium">Asunto:</span> {{ $asuntoCorreo }}
        </p>

        <div class="mt-4">
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <span class="text-sm font-medium text-slate-700">Firma</span>
                <button type="button" data-open-dialog="modal-firma" class="text-xs font-semibold text-brand-green hover:text-brand-green-dark">
                    Crear firma
                </button>
            </div>
            <select
                name="firma_id"
                data-firma-select
                class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
            >
                <option value="">Selecciona una firma</option>
                @foreach ($firmas as $firma)
                    <option value="{{ $firma->id }}" @selected((string) old('firma_id') === (string) $firma->id)>{{ $firma->nombre }}</option>
                @endforeach
            </select>
            @error('firma_id')
                <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
            @enderror
            @if ($firmas->isEmpty())
                <p class="mt-1.5 text-xs text-slate-500">Aún no hay firmas. Crea una para poder enviar el correo.</p>
            @else
                <button
                    type="submit"
                    form="form-eliminar-firma"
                    class="mt-2 text-xs font-semibold text-brand-red hover:text-brand-red-dark disabled:cursor-not-allowed disabled:text-slate-400"
                    data-firma-eliminar-boton
                    @disabled(! old('firma_id'))
                >
                    Eliminar firma
                </button>
            @endif
        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Enviar correo
            </button>
        </div>
    </form>
    @if ($firmas->isNotEmpty())
        <form
            id="form-eliminar-firma"
            method="POST"
            action="{{ route('traslados.firmas.destroy', $firmas->first()) }}"
            data-firma-eliminar
            data-firma-eliminar-base="{{ url('/traslados/firmas') }}"
            onsubmit="return confirm('¿Eliminar esta firma?')"
        >
            @csrf
            @method('DELETE')
        </form>
    @endif
</dialog>

<dialog id="modal-firma" class="w-[min(40rem,calc(100%-2rem))] rounded-2xl border border-slate-200 bg-white p-0 shadow-xl backdrop:bg-slate-900/40">
    <form method="POST" action="{{ route('traslados.firmas.store') }}" enctype="multipart/form-data" class="p-5" data-firma-form>
        @csrf
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Crear firma</h3>
                <p class="mt-1 text-xs text-slate-500">Escribe o pega aquí la firma de Gmail, con texto e imagen.</p>
            </div>
            <button type="button" data-close-dialog class="text-sm font-medium text-slate-500 hover:text-slate-800">Cerrar</button>
        </div>

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-medium text-slate-700">Nombre</span>
            <input
                type="text"
                name="nombre"
                value="{{ old('nombre') }}"
                placeholder="Ej. Juan Pérez"
                data-firma-nombre
                class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
            >
        </label>
        @error('nombre')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror

        <div class="mt-4">
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <span class="text-sm font-medium text-slate-700">Firma</span>
                <button type="button" data-firma-insertar class="text-xs font-semibold text-brand-green hover:text-brand-green-dark">
                    Insertar imagen
                </button>
            </div>
            <div
                data-firma-editor
                data-placeholder="Escribe o pega aquí tu firma de Gmail, con texto e imagen."
                contenteditable="true"
                class="min-h-48 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
            >{!! old('contenido') !!}</div>
            <input type="hidden" name="contenido" value="{{ old('contenido') }}" data-firma-contenido>
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only" data-firma-archivo>
        </div>
        @error('contenido')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror
        @error('imagen')
            <p class="mt-1.5 text-sm text-brand-red">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-xs text-slate-500">Copia la firma en Gmail y pégala aquí con Ctrl+V, o inserta la imagen.</p>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="rounded-lg bg-brand-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-green-dark">
                Guardar firma
            </button>
        </div>
    </form>
</dialog>
