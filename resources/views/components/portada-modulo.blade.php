@props([
    'title',
    'subtitle' => null,
])

<section {{ $attributes->class('portada-tecnologica mt-6 overflow-hidden rounded-2xl border shadow-sm') }}>
    <div class="flex h-2">
        <div class="w-1/2 bg-brand-green"></div>
        <div class="w-1/2 bg-brand-red"></div>
    </div>
    <div @class([
        'px-6 py-8 sm:px-8',
        'flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between' => isset($actions),
    ])>
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-semibold text-white">{{ $title }}</h1>
                {{ $badge ?? '' }}
            </div>
            @if (filled($subtitle))
                <p class="mt-2 text-sm text-slate-300">{{ $subtitle }}</p>
            @endif
        </div>
        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
