<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-white leading-tight">
                Gestión de Usuarios
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 transition-all duration-300">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nuevo Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes flash -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Buscador -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6 mb-6">
                <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-700/50 border border-blue-500/20 rounded-xl text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/30">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-700 hover:text-white transition-all">
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-700 hover:text-white transition-all">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabla de usuarios -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-blue-500/20">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Registro</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-500/10">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-400">{{ $user->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-500/20">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $roleColors = [
                                                'coordinador' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                                'analista' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'apoyo' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-lg border {{ $roleColors[$user->role] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                            {{ $user->role_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-500/20 hover:border-blue-500/40 transition-all">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Editar
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 hover:border-red-500/40 transition-all">
                                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        No se encontraron usuarios.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-blue-500/20">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
