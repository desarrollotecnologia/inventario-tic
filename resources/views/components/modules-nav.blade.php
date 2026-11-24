@php
    $items = [
        [
            'label' => 'Inicio',
            'short' => 'Inicio',
            'route' => 'home',
            'active' => request()->routeIs('home'),
            'icon' => 'home',
        ],
        [
            'label' => 'Relación Inventario Sistemas',
            'short' => 'Inventario',
            'route' => 'modules.relacion',
            'active' => request()->is('relacion-inventario-sistemas', 'relacion-inventario-sistemas/*'),
            'icon' => 'list',
        ],
        [
            'label' => 'Traslados',
            'short' => 'Traslados',
            'route' => 'modules.traslados',
            'active' => request()->is('traslados', 'traslados/*'),
            'icon' => 'move',
        ],
        [
            'label' => 'Baja de Equipos',
            'short' => 'Bajas',
            'route' => 'modules.bajas',
            'active' => request()->is('baja-de-equipos', 'baja-de-equipos/*'),
            'icon' => 'archive',
        ],
        [
            'label' => 'Actas',
            'short' => 'Actas',
            'route' => 'modules.actas',
            'active' => request()->is('actas', 'actas/*'),
            'icon' => 'document',
        ],
    ];
@endphp

{{-- Navegación móvil --}}
<nav class="border-b border-slate-200 bg-white lg:hidden" aria-label="Módulos">
    <div class="flex gap-2 overflow-x-auto px-3 py-3">
        @foreach ($items as $item)
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'inline-flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm transition duration-200',
                    'bg-brand-green-soft font-semibold text-brand-green-dark hover:bg-brand-red-soft hover:text-brand-red-dark hover:shadow-md hover:shadow-brand-red/20' => $item['active'],
                    'font-medium text-slate-600 hover:bg-brand-red-soft hover:text-brand-red-dark hover:shadow-md hover:shadow-brand-red/20' => ! $item['active'],
                ])
            >
                {{ $item['short'] }}
            </a>
        @endforeach
    </div>
</nav>

{{-- Barra lateral escritorio: oculta, se abre al pasar el mouse por el borde izquierdo --}}
<div class="pointer-events-none fixed inset-y-0 left-0 z-40 hidden lg:block">
    <aside
        class="modules-nav-flyout group/nav pointer-events-auto relative flex h-full w-64 -translate-x-[calc(100%-0.75rem)] flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300 ease-out hover:translate-x-0 focus-within:translate-x-0"
        aria-label="Módulos"
    >
        <div class="pointer-events-none absolute inset-y-0 right-0 flex w-3 overflow-hidden rounded-r-md shadow-sm transition-opacity duration-300 group-hover/nav:opacity-0 group-focus-within/nav:opacity-0">
            <div class="flex h-full w-full flex-col">
                <div class="h-1/2 bg-brand-green"></div>
                <div class="h-1/2 bg-brand-red"></div>
            </div>
        </div>

        <div class="border-b border-slate-100 px-4 py-5">
            <a href="{{ route('home') }}" class="block">
                <p class="text-lg font-bold tracking-tight">
                    <span class="text-brand-green">Col</span><span class="text-brand-red">beef</span>
                </p>
                <p class="mt-0.5 text-xs font-semibold text-slate-500">Inventario TIC</p>
            </a>
        </div>

        <div class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'group/item flex items-start gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                        'bg-brand-green-soft font-semibold text-brand-green-dark hover:bg-brand-red-soft hover:text-brand-red-dark hover:shadow-md hover:shadow-brand-red/25' => $item['active'],
                        'font-medium text-slate-600 hover:bg-brand-red-soft hover:text-brand-red-dark hover:shadow-md hover:shadow-brand-red/25' => ! $item['active'],
                    ])
                >
                    <span @class([
                        'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition duration-200 group-hover/item:bg-white group-hover/item:text-brand-red',
                        'bg-white text-brand-green' => $item['active'],
                        'bg-brand-green-soft text-brand-green' => ! $item['active'],
                    ])>
                        @if ($item['icon'] === 'home')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z" />
                            </svg>
                        @elseif ($item['icon'] === 'list')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                            </svg>
                        @elseif ($item['icon'] === 'move')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8l-4 4 4 4M17 8l4 4-4 4M3 12h18" />
                            </svg>
                        @elseif ($item['icon'] === 'document')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75h4.5L18 8.25v12a.75.75 0 0 1-.75.75H6.75A.75.75 0 0 1 6 20.25V4.5a.75.75 0 0 1 .75-.75Zm4.5 0v4.5H18M8.25 12h7.5M8.25 15.75h7.5M8.25 19.5h4.5" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Zm2-3h12l2 3H4l2-3Z" />
                            </svg>
                        @endif
                    </span>
                    <span class="leading-snug">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </aside>
</div>
