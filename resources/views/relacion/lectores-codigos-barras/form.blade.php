<x-layouts.app :title="$title">
    <a href="{{ route('lectores-codigos-barras.index') }}" class="text-sm font-medium text-brand-green hover:text-brand-green-dark">← Volver a Lectores Códigos de Barras</a>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-1 text-sm text-slate-500">Completa la información del lector de códigos de barras.</p>

        <form method="POST" action="{{ $action }}" class="registro-mayusculas mt-5 space-y-5">
            @csrf
            @method($method)

            @foreach ($groups as $group => $fields)
                <fieldset>
                    <legend class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</legend>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @if ($loop->first)
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Número inventario</label>
                                <input type="text" value="{{ $lector->item }}" readonly class="block w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-sm text-slate-700">
                                <p class="mt-1 text-xs text-slate-500">Se asigna automáticamente al registrar el equipo.</p>
                            </div>
                        @endif

                        @foreach ($fields as $name => $field)
                            <div @class(['sm:col-span-2 lg:col-span-4' => $field['type'] === 'textarea'])>
                                <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
                                    {{ $field['label'] }}
                                </label>

                                @if ($field['type'] === 'textarea')
                                    <textarea
                                        id="{{ $name }}"
                                        name="{{ $name }}"
                                        rows="2"
                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                    >{{ old($name, $lector->{$name}) }}</textarea>
                                @elseif ($field['type'] === 'combobox')
                                    @php
                                        $valor = old($name, $lector->{$name});
                                        $opciones = $field['options'] ?? [];
                                    @endphp
                                    <div class="relative" data-filter-combobox>
                                        <input
                                            id="{{ $name }}"
                                            type="text"
                                            name="{{ $name }}"
                                            value="{{ $valor }}"
                                            autocomplete="off"
                                            placeholder="{{ match ($name) {
                                                'usuario' => 'Escribe o elige un usuario',
                                                'cargo' => 'Escribe o elige un cargo',
                                                'departamento' => 'Escribe o elige un departamento',
                                                'area' => 'Escribe o elige un área',
                                                'marca_referencia' => 'Escribe o elige una marca y referencia',
                                                'modelo' => 'Escribe o elige un modelo',
                                                default => 'Escribe o elige',
                                            } }}"
                                            data-combobox-input
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
                                            @if ($field['allow_custom'] ?? true)
                                                <li
                                                    class="cursor-pointer border-t border-slate-100 px-3 py-2 text-sm font-medium text-brand-green hover:bg-brand-green-soft"
                                                    data-combobox-option
                                                    data-combobox-custom
                                                    data-value="Nuevo"
                                                    role="option"
                                                >Nuevo</li>
                                            @endif
                                            <li class="hidden px-3 py-2 text-sm text-slate-400" data-combobox-empty>
                                                {{ ($field['allow_custom'] ?? true) ? 'Sin coincidencias. Elige Nuevo o escribe el nombre.' : 'Sin coincidencias. Puedes dejar lo que escribiste.' }}
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <input
                                        id="{{ $name }}"
                                        type="text"
                                        name="{{ $name }}"
                                        value="{{ old($name, $lector->{$name}) }}"
                                        class="block w-full rounded-lg border border-slate-300 px-2.5 py-2 text-sm text-slate-900 shadow-sm outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
                                    >
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
                <a href="{{ route('lectores-codigos-barras.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
</x-layouts.app>
