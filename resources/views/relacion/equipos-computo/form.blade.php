<x-layouts.app :title="$title">
    <a href="{{ route('equipos-computo.index') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Equipos de Cómputo</a>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-1 text-sm text-slate-500">Completa la información del equipo de cómputo.</p>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="registro-mayusculas mt-5 space-y-5">
            @csrf
            @method($method)

            @foreach ($groups as $group => $fields)
                <fieldset>
                    <legend class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</legend>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($fields as $name => $field)
                            <div @class([
                                'sm:col-span-2 lg:col-span-4' => $field['type'] === 'textarea',
                                'sm:col-span-2' => in_array($field['type'], ['image'], true) || $name === 'procesador' || isset($field['optional']),
                            ])>
                                <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
                                    {{ $field['label'] }}
                                </label>

                                @if ($field['type'] === 'readonly' || $field['type'] === 'computed')
                                    <input
                                        id="{{ $name }}"
                                        type="text"
                                        value="{{ $field['type'] === 'computed' ? $equipo->antiguedad() : $equipo->{$name} }}"
                                        readonly
                                        @if ($name === 'antiguedad') data-antiguedad-output @endif
                                        class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700"
                                    >
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $field['type'] === 'computed' ? 'Se calcula automáticamente con la fecha de adquisición.' : 'Se asigna automáticamente al registrar el equipo.' }}
                                    </p>
                                @elseif ($field['type'] === 'textarea')
                                    <textarea
                                        id="{{ $name }}"
                                        name="{{ $name }}"
                                        rows="2"
                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                    >{{ old($name, $equipo->{$name}) }}</textarea>
                                @elseif ($field['type'] === 'image')
                                    <div @if (isset($field['optional'])) data-optional-field @endif>
                                        <input
                                            id="{{ $name }}"
                                            type="file"
                                            name="{{ $name }}"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-green-dark"
                                        >
                                        <p class="mt-1 text-xs text-slate-500">JPG, PNG o WEBP. Máximo 5 MB.</p>
                                        @if ($equipo->urlFoto($name))
                                            <img src="{{ $equipo->urlFoto($name) }}" alt="{{ $field['label'] }}" class="mt-2 h-28 w-full rounded-lg border border-slate-200 object-cover">
                                            <p class="mt-1 text-xs text-slate-500">Foto actual. Sube otra para reemplazarla.</p>
                                        @endif

                                        @if (isset($field['optional']))
                                            @php
                                                $extra = $field['optional'];
                                                $mostrarExtra = filled($equipo->{$extra['name']}) || $errors->has($extra['name']);
                                            @endphp

                                            <button
                                                type="button"
                                                data-optional-add
                                                @class(['mt-2 text-sm font-medium text-brand-green hover:text-brand-green-dark', 'hidden' => $mostrarExtra])
                                            >
                                                + Agregar foto del procesador
                                            </button>

                                            <div data-optional-extra @class(['mt-3', 'hidden' => ! $mostrarExtra])>
                                                <label for="{{ $extra['name'] }}" class="mb-1 block text-sm font-medium text-slate-700">
                                                    {{ $extra['label'] }}
                                                </label>
                                                <input
                                                    type="hidden"
                                                    name="eliminar_{{ $extra['name'] }}"
                                                    value="0"
                                                    data-optional-remove-flag
                                                >
                                                <div class="flex items-start gap-2">
                                                    <input
                                                        id="{{ $extra['name'] }}"
                                                        type="file"
                                                        name="{{ $extra['name'] }}"
                                                        accept="image/jpeg,image/png,image/webp"
                                                        class="block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 px-2.5 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-green-dark"
                                                    >
                                                    <button
                                                        type="button"
                                                        data-optional-remove
                                                        class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                                                    >
                                                        Quitar
                                                    </button>
                                                </div>
                                                <p class="mt-1 text-xs text-slate-500">JPG, PNG o WEBP. Máximo 5 MB.</p>
                                                @if ($equipo->urlFoto($extra['name']))
                                                    <img src="{{ $equipo->urlFoto($extra['name']) }}" alt="{{ $extra['label'] }}" class="mt-2 h-28 w-full rounded-lg border border-slate-200 object-cover">
                                                    <p class="mt-1 text-xs text-slate-500">Foto actual. Sube otra para reemplazarla.</p>
                                                @endif
                                                @error($extra['name'])
                                                    <p class="mt-1 text-sm text-brand-red">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                @elseif ($field['type'] === 'combobox')
                                    @php
                                        $partesProcesador = $name === 'procesador'
                                            ? \App\Support\ProcesadorEquipo::parse(old('procesador_modelo', old('procesador', $equipo->procesador)))
                                            : null;
                                        $valor = $name === 'procesador'
                                            ? old('procesador_modelo', $partesProcesador['modelo'])
                                            : old($name, $equipo->{$name});
                                        $opciones = $field['options'] ?? [];
                                    @endphp
                                    <div @if ($name === 'procesador') data-procesador-campo @endif>
                                        <div class="relative" data-filter-combobox>
                                            <input
                                                id="{{ $name }}"
                                                type="text"
                                                name="{{ $name === 'procesador' ? 'procesador_modelo' : $name }}"
                                                value="{{ $valor }}"
                                                autocomplete="off"
                                                placeholder="Escribe para filtrar o elige {{ match ($field['label']) { 'Área' => 'un área', 'Descripción' => 'una descripción', 'Windows' => 'un Windows', 'Office' => 'un Office', 'Memoria' => 'una memoria', 'Disco' => 'un disco', 'Procesador' => 'un procesador', 'Usuario' => 'un usuario', 'Cargo' => 'un cargo', default => 'un departamento' } }}"
                                                data-combobox-input
                                                @if ($name === 'procesador') data-procesador-modelo @endif
                                                class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 pr-9 text-sm text-slate-900 shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                            >
                                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                            <ul
                                                class="absolute z-20 mt-1 hidden max-h-56 w-full overflow-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg"
                                                data-combobox-list
                                                role="listbox"
                                            >
                                                @foreach ($opciones as $opcion)
                                                    <li
                                                        class="cursor-pointer px-3 py-2 text-sm text-slate-700 hover:bg-brand-green-soft hover:text-brand-green-dark"
                                                        data-combobox-option
                                                        data-value="{{ $opcion }}"
                                                        role="option"
                                                    >{{ $opcion }}</li>
                                                @endforeach
                                                @if ($field['allow_custom'] ?? false)
                                                    <li
                                                        class="cursor-pointer border-t border-slate-100 px-3 py-2 text-sm font-medium text-brand-green hover:bg-brand-green-soft"
                                                        data-combobox-option
                                                        data-combobox-custom
                                                        data-value="Nuevo"
                                                        role="option"
                                                    >Nuevo</li>
                                                @endif
                                                <li class="hidden px-3 py-2 text-sm text-slate-400" data-combobox-empty>
                                                    {{ ($field['allow_custom'] ?? false) ? 'Sin coincidencias. Elige Nuevo o escribe el nombre.' : 'Sin coincidencias. Puedes dejar lo que escribiste.' }}
                                                </li>
                                            </ul>
                                        </div>

                                        @if ($name === 'procesador')
                                            @php
                                                $versionProcesador = old('procesador_version', $partesProcesador['version'] ?? '');
                                                $velocidadProcesador = old('procesador_velocidad', $partesProcesador['velocidad'] ?? '');
                                                $mostrarDetalleProcesador = filled($valor) || filled($versionProcesador) || filled($velocidadProcesador);
                                            @endphp
                                            <div
                                                data-procesador-detalle
                                                @class(['mt-2 grid gap-3 sm:grid-cols-2', 'hidden' => ! $mostrarDetalleProcesador])
                                            >
                                                <div>
                                                    <label for="procesador_version" class="mb-1 block text-sm font-medium text-slate-700">Versión</label>
                                                    <input
                                                        id="procesador_version"
                                                        type="text"
                                                        name="procesador_version"
                                                        value="{{ $versionProcesador }}"
                                                        placeholder="3330s"
                                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                                    >
                                                </div>
                                                <div>
                                                    <label for="procesador_velocidad" class="mb-1 block text-sm font-medium text-slate-700">Velocidad</label>
                                                    <input
                                                        id="procesador_velocidad"
                                                        type="text"
                                                        name="procesador_velocidad"
                                                        value="{{ $velocidadProcesador }}"
                                                        placeholder="2.7GHz"
                                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                                    >
                                                </div>
                                                <p class="text-xs text-slate-500 sm:col-span-2">Ejemplo: Intel Core i5 - 3330s – 2.7GHz</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div @if (isset($field['optional'])) data-optional-field @endif>
                                        <input
                                            id="{{ $name }}"
                                            type="{{ $field['type'] }}"
                                            name="{{ $name }}"
                                            value="{{ old($name, $field['type'] === 'date' ? $equipo->{$name}?->format('Y-m-d') : $equipo->{$name}) }}"
                                            @if ($name === 'fecha_adquisicion') data-antiguedad-source @endif
                                            class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                        >

                                        @if (isset($field['optional']))
                                            @php
                                                $extra = $field['optional'];
                                                $extraValor = old($extra['name'], $equipo->{$extra['name']});
                                                $mostrarExtra = filled($extraValor);
                                            @endphp

                                            <button
                                                type="button"
                                                data-optional-add
                                                @class(['mt-2 text-sm font-medium text-brand-green hover:text-brand-green-dark', 'hidden' => $mostrarExtra])
                                            >
                                                + Agregar otro serial
                                            </button>

                                            <div data-optional-extra @class(['mt-3', 'hidden' => ! $mostrarExtra])>
                                                <label for="{{ $extra['name'] }}" class="mb-1 block text-sm font-medium text-slate-700">
                                                    {{ $extra['label'] }}
                                                </label>
                                                <div class="flex gap-2">
                                                    <input
                                                        id="{{ $extra['name'] }}"
                                                        type="text"
                                                        name="{{ $extra['name'] }}"
                                                        value="{{ $extraValor }}"
                                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                                    >
                                                    <button
                                                        type="button"
                                                        data-optional-remove
                                                        class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                                                    >
                                                        Quitar
                                                    </button>
                                                </div>
                                                @error($extra['name'])
                                                    <p class="mt-1 text-sm text-brand-red">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @error($name)
                                    <p class="mt-1 text-sm text-brand-red">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-brand-green px-4 py-2 text-sm font-semibold text-white hover:bg-brand-green-dark">
                    Guardar
                </button>
                <a href="{{ route('equipos-computo.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
</x-layouts.app>
