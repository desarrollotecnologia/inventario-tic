<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('users.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-white leading-tight">
                Nuevo Usuario
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <!-- Nombre -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Contraseña</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                    </div>

                    <!-- Rol -->
                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-slate-300 mb-2">Rol</label>
                        <select name="role" id="role" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                            @foreach(\App\Models\User::ROLES as $value => $label)
                                <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 transition-all duration-300">
                            Crear Usuario
                        </button>
                        <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-700 hover:text-white transition-all">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
