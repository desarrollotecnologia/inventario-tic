<x-layouts.app title="Actas">
    <x-portada-modulo
        title="Actas"
        subtitle="Selecciona el tipo de acta que vas a gestionar."
    />

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        @foreach ($submodules as $submodule)
            <a
                href="{{ route($submodule['route']) }}"
                class="group rounded-2xl border-2 border-brand-green bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-green-dark hover:shadow-md"
            >
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $submodule['title'] }}</h2>
                    @if (! $submodule['ready'])
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Próximamente</span>
                    @endif
                </div>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $submodule['description'] }}</p>
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
