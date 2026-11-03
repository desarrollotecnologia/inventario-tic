@props([
    'title' => null,
    'modulesNav' => null,
])

@php
    $showModulesNav = $modulesNav ?? ! request()->routeIs('home');
@endphp

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Colbeef | Inventario TIC' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
        <div class="flex h-1.5 w-full">
            <div class="w-1/2 bg-brand-green"></div>
            <div class="w-1/2 bg-brand-red"></div>
        </div>

        @if ($showModulesNav)
            <x-modules-nav />
        @endif

        <div class="mx-auto w-full max-w-[100rem] px-3 py-6 sm:px-5 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </body>
</html>
