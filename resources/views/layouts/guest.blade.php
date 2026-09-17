<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>PLAN SEMANAL TIC</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                background: url('{{ asset('images/fd.jpg') }}') no-repeat center center fixed;
                background-size: cover;
                position: relative;
            }
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.85);
                z-index: 0;
            }
            body::after {
                content: '';
                position: fixed;
                inset: 0;
                background-image:
                    linear-gradient(rgba(59, 130, 246, 0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(59, 130, 246, 0.04) 1px, transparent 1px);
                background-size: 50px 50px;
                z-index: 0;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-3 group">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-700 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-9 h-9 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-wide bg-gradient-to-r from-blue-300 via-blue-400 to-indigo-400 bg-clip-text text-transparent">PLAN SEMANAL TIC</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-slate-900/80 backdrop-blur-xl border border-blue-500/20 shadow-2xl shadow-blue-500/10 sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
