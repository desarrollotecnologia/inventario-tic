@props([
    'title',
    'subtitle' => null,
])

<section {{ $attributes->class('portada-tecnologica mt-6 overflow-hidden rounded-2xl border shadow-sm') }}>
    <div class="flex h-2">
        <div class="w-1/2 bg-brand-green"></div>
        <div class="w-1/2 bg-brand-red"></div>
    </div>
    <div class="flex items-center justify-between gap-4 px-6 py-8 sm:gap-6 sm:px-8">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-white">{{ $title }}</h1>
                {{ $badge ?? '' }}
            </div>
            @if (filled($subtitle))
                <p class="mt-2 text-sm text-slate-300">{{ $subtitle }}</p>
            @endif
            @isset($actions)
                <div class="mt-4 flex flex-wrap items-center gap-2 sm:mt-5">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        <div class="portada-avatar shrink-0">
            <img
                src="{{ asset('images/kall-su.png') }}"
                alt="Inventario TIC"
                class="h-16 w-16 rounded-full object-cover sm:h-20 sm:w-20"
            >
        </div>
    </div>
</section>
