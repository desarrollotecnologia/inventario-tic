<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('activities.index') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <h2 class="font-bold text-xl text-white leading-tight">
                Registrar Nueva Actividad
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6">
                <form method="POST" action="{{ route('activities.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="nombre" class="block text-sm font-medium text-slate-300 mb-2">Nombre de la Actividad</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('nombre')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="semana" class="block text-sm font-medium text-slate-300 mb-2">Semana</label>
                        <input type="text" name="semana" id="semana" value="{{ old('semana') }}" required
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('semana')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="fecha_entrega" class="block text-sm font-medium text-slate-300 mb-2">Fecha de Entrega</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ old('fecha_entrega') }}"
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                        @error('fecha_entrega')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Responsables</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-700/30 border border-blue-500/20 rounded-xl p-4 max-h-48 overflow-y-auto">
                            @foreach($users as $user)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" name="responsables[]" value="{{ $user->name }}"
                                        {{ in_array($user->name, old('responsables', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-blue-500/30 bg-slate-700 text-blue-600 focus:ring-blue-500/30 focus:ring-offset-0">
                                    <span class="text-sm text-slate-300 group-hover:text-white transition-colors">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('responsables')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 transition-all duration-300">
                            Registrar Actividad
                        </button>
                        <a href="{{ route('activities.index') }}" class="px-6 py-2.5 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-700 hover:text-white transition-all">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
