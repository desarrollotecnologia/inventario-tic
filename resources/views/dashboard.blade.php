<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome message -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 overflow-hidden shadow-xl shadow-blue-500/5 sm:rounded-2xl mb-8">
                <div class="p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-lg">¡Bienvenido, {{ Auth::user()->name }}!</p>
                        <p class="text-slate-400 text-sm">Panel de control - Plan Semanal TIC · <span class="text-blue-400">{{ Auth::user()->role_label }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Module cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Actividades -->
                <a href="{{ route('activities.index') }}" class="group bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 overflow-hidden shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6 hover:border-blue-500/40 hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Actividades</h3>
                            <p class="text-slate-400 text-sm">Plan semanal</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">Gestiona las actividades semanales del área, asigna responsables y realiza seguimiento.</p>
                    <div class="mt-4 flex items-center gap-1 text-blue-400 text-sm font-medium group-hover:gap-2 transition-all">
                        Ver actividades
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </a>

                <!-- Crear Actividad -->
                <a href="{{ route('activities.create') }}" class="group bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 overflow-hidden shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6 hover:border-blue-500/40 hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Nueva Actividad</h3>
                            <p class="text-slate-400 text-sm">Registrar</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">Registra una nueva actividad con responsables, semana y fecha de entrega.</p>
                    <div class="mt-4 flex items-center gap-1 text-emerald-400 text-sm font-medium group-hover:gap-2 transition-all">
                        Crear actividad
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </a>

                <!-- Usuarios -->
                @if(Auth::user()->canManageUsers())
                <a href="{{ route('users.index') }}" class="group bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 overflow-hidden shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6 hover:border-blue-500/40 hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Usuarios</h3>
                            <p class="text-slate-400 text-sm">Gestión</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">Administra los usuarios del sistema, crea cuentas y gestiona accesos.</p>
                    <div class="mt-4 flex items-center gap-1 text-amber-400 text-sm font-medium group-hover:gap-2 transition-all">
                        Ver usuarios
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </a>
                @endif

                <!-- Perfil -->
                <a href="{{ route('profile.edit') }}" class="group bg-slate-800/80 backdrop-blur-xl border border-blue-500/20 overflow-hidden shadow-xl shadow-blue-500/5 sm:rounded-2xl p-6 hover:border-blue-500/40 hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Mi Perfil</h3>
                            <p class="text-slate-400 text-sm">Configuración</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">Actualiza tu información personal y contraseña de acceso.</p>
                    <div class="mt-4 flex items-center gap-1 text-violet-400 text-sm font-medium group-hover:gap-2 transition-all">
                        Editar perfil
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
