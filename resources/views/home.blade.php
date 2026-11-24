<x-layouts.app title="Colbeef | Inventario TIC">
    <section class="portada-tecnologica overflow-hidden rounded-2xl border shadow-sm">
        <div class="flex h-2">
            <div class="w-1/2 bg-brand-green"></div>
            <div class="w-1/2 bg-brand-red"></div>
        </div>
        <div class="flex items-center justify-between gap-5 px-6 py-8 sm:gap-7 sm:px-10 sm:py-10">
            <div class="min-w-0">
                <h1 class="text-3xl font-bold tracking-tight sm:text-5xl">
                    <span class="text-brand-green">Col</span><span class="text-brand-red">beef</span>
                </h1>
                <p class="mt-2 text-lg font-semibold text-white">Inventario TIC</p>
                <p class="mt-1 max-w-2xl text-sm text-slate-300 sm:text-base">Selecciona un módulo para ingresar.</p>
            </div>
            <div class="portada-avatar shrink-0">
                <img
                    src="{{ asset('images/kall-su.png') }}"
                    alt="Inventario TIC"
                    class="h-20 w-20 rounded-full object-cover sm:h-24 sm:w-24 md:h-28 md:w-28"
                >
            </div>
        </div>
    </section>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($modules as $module)
            <a
                href="{{ route($module['route']) }}"
                class="group rounded-2xl border-2 border-brand-green bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-green-dark hover:shadow-md"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-green-soft text-brand-green">
                    @if ($module['icon'] === 'list')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                    @elseif ($module['icon'] === 'move')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8l-4 4 4 4M17 8l4 4-4 4M3 12h18" />
                        </svg>
                    @elseif ($module['icon'] === 'document')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75h4.5L18 8.25v12a.75.75 0 0 1-.75.75H6.75A.75.75 0 0 1 6 20.25V4.5a.75.75 0 0 1 .75-.75Zm4.5 0v4.5H18M8.25 12h7.5M8.25 15.75h7.5M8.25 19.5h4.5" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Zm2-3h12l2 3H4l2-3Z" />
                        </svg>
                    @endif
                </div>

                <h2 class="mt-5 text-lg font-semibold text-slate-900">{{ $module['title'] }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $module['description'] }}</p>
                <span class="mt-6 inline-flex items-center text-sm font-semibold text-brand-green group-hover:text-brand-green-dark">
                    Entrar
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </a>
        @endforeach
    </div>
</x-layouts.app>
