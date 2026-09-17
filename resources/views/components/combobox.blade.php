@props([
    'name',
    'options' => [],
    'form' => null,
    'value' => '',
    'placeholder' => 'Escribe para filtrar o elige',
    'allowCustom' => true,
])

<div {{ $attributes->class('relative z-20') }} data-filter-combobox>
    <input
        @if ($form)
            form="{{ $form }}"
        @endif
        type="text"
        name="{{ $name }}"
        value="{{ $value }}"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        data-combobox-input
        class="block w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 pr-7 text-xs text-slate-900 outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green-soft"
    >
    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </span>
    <ul
        class="fixed z-[9999] mt-1 hidden max-h-72 min-w-[16rem] overflow-auto rounded-lg border border-slate-300 bg-white py-1 text-sm shadow-xl"
        data-combobox-list
        role="listbox"
    >
        @foreach ($options as $opcion)
            <li
                class="cursor-pointer px-3 py-2 text-sm text-slate-800 hover:bg-brand-green-soft hover:text-brand-green-dark"
                data-combobox-option
                data-value="{{ $opcion }}"
                role="option"
            >{{ $opcion }}</li>
        @endforeach
        @if ($allowCustom)
            <li
                class="cursor-pointer border-t border-slate-200 px-3 py-2 text-sm font-medium text-brand-green hover:bg-brand-green-soft"
                data-combobox-option
                data-combobox-custom
                data-value="Nuevo"
                role="option"
            >Nuevo</li>
        @endif
        <li class="hidden px-3 py-2 text-sm text-slate-400" data-combobox-empty>
            Sin coincidencias. Elige Nuevo o escribe el valor.
        </li>
    </ul>
</div>
