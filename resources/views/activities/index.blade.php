<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-white leading-tight">
                Plan Semanal de Actividades
            </h2>
            <a href="{{ route('activities.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 transition-all duration-300">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nueva Actividad
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

            <!-- Filtros -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl px-4 py-3 mb-6">
                <form method="GET" action="{{ route('activities.index') }}" class="flex items-center gap-3 flex-wrap">
                    <select name="estado" class="px-3 py-2 bg-slate-700/50 border border-blue-500/20 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                        <option value="">Estado</option>
                        <option value="realizado" {{ request('estado') === 'realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="seguimiento" {{ request('estado') === 'seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                    </select>
                    <input type="text" name="responsable" value="{{ request('responsable') }}" placeholder="Responsable..."
                        class="px-3 py-2 bg-slate-700/50 border border-blue-500/20 rounded-lg text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 w-40">
                    <input type="text" name="semana" value="{{ request('semana') }}" placeholder="Semana..."
                        class="px-3 py-2 bg-slate-700/50 border border-blue-500/20 rounded-lg text-white placeholder-slate-400 text-sm focus:outline-none focus:border-blue-500/50 w-32">
                    <button type="submit" class="px-4 py-2 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-700 hover:text-white transition-all">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['estado', 'responsable', 'semana']))
                        <a href="{{ route('activities.index') }}" class="px-4 py-2 bg-slate-700/50 border border-blue-500/20 text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-700 hover:text-white transition-all">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 shadow-xl shadow-blue-500/5 sm:rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <thead>
                            <tr class="border-b border-blue-500/20">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-[35%]">Actividad</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Semana</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Fecha Entrega</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Responsables</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Observaciones</th>
                                @if(auth()->user()->canEditActivities())
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-500/10">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm text-white max-w-xs break-words whitespace-normal">{{ $activity->nombre }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $activity->semana }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $activity->fecha_entrega ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $activity->responsables }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $estadoColors = [
                                                'realizado' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'pendiente' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                                'seguimiento' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-lg border {{ $estadoColors[$activity->estado] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20' }}">
                                            {{ ucfirst($activity->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400 max-w-xs">{{ $activity->observaciones ?? '—' }}</td>
                                    @if(auth()->user()->canEditActivities())
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('activities.edit', $activity) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-500/20 hover:border-blue-500/40 transition-all">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                    Editar
                                                </a>
                                                @if(auth()->user()->canDeleteActivities())
                                                    <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta actividad?')">
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
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->canEditActivities() ? 7 : 6 }}" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        No hay actividades registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
