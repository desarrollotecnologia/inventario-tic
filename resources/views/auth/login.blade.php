<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" class="!text-blue-200 font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full !bg-slate-800/60 !border-blue-500/30 !text-white placeholder-slate-400 focus:!border-blue-400 focus:!ring-blue-500/40 rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="correo@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" class="!text-blue-200 font-semibold" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full !bg-slate-800/60 !border-blue-500/30 !text-white placeholder-slate-400 focus:!border-blue-400 focus:!ring-blue-500/40 rounded-lg pr-10"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 mt-1 flex items-center pr-3 text-slate-400 hover:text-blue-400 transition-colors">
                    <svg id="eye-open" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg id="eye-closed" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                    </svg>
                </button>
            </div>
            <script>
                function togglePassword() {
                    const input = document.getElementById('password');
                    const eyeOpen = document.getElementById('eye-open');
                    const eyeClosed = document.getElementById('eye-closed');
                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    } else {
                        input.type = 'password';
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    }
                }
            </script>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-blue-500/30 bg-slate-800/60 text-blue-500 shadow-sm focus:ring-blue-500/40" name="remember">
                <span class="ms-2 text-sm text-slate-300">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-blue-400 hover:text-blue-300 transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>
