<section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
    <div class="overflow-hidden rounded-[14px] bg-white">
        <div class="flex h-1.5">
            <div class="w-1/2 bg-brand-green"></div>
            <div class="w-1/2 bg-brand-red"></div>
        </div>
        <div class="border-b border-slate-100 px-4 py-3">
            <h2 class="text-sm font-semibold text-slate-800">{{ $titulo }}</h2>
            <p class="mt-0.5 text-xs text-slate-500">Haz clic en una fila para ver el detalle completo de la baja.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[80rem] w-full table-fixed text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                    <tr>
                        <th class="w-[4%] px-2 py-2.5 font-medium">Ítem</th>
                        <th class="w-[7%] px-2 py-2.5 font-medium">Fecha</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Acta No.</th>
                        <th class="w-[12%] px-2 py-2.5 font-medium">Solicitante</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Categoría</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Código</th>
                        <th class="w-[12%] px-2 py-2.5 font-medium">Descripción</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Serial</th>
                        <th class="w-[14%] px-2 py-2.5 font-medium">Motivo</th>
                        <th class="w-[15%] px-2 py-2.5 font-medium">Documentos</th>
                    </tr>
                </thead>
                @foreach ($bajas as $baja)
                    <tbody class="border-t border-slate-100">
                        <tr
                            class="cursor-pointer text-slate-700 hover:bg-slate-50"
                            data-expand-row
                            data-expand-target="detalle-baja-{{ $baja->id }}"
                            aria-expanded="false"
                        >
                            <td class="break-words px-2 py-2.5">{{ $loop->iteration }}</td>
                            <td class="break-words px-2 py-2.5">{{ $baja->fecha?->format('d/m/Y') ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $baja->acta_numero ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">
                                {{ $baja->solicitante_nombre ?: '—' }}
                                @if (filled($baja->solicitante_cargo))
                                    <span class="mt-0.5 block text-[10px] text-slate-500">{{ $baja->solicitante_cargo }}</span>
                                @endif
                            </td>
                            <td class="break-words px-2 py-2.5">{{ $baja->categoria ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $baja->codigoMostrado() }}</td>
                            <td class="break-words px-2 py-2.5">{{ $baja->descripcion ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $baja->serial ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">{{ \Illuminate\Support\Str::limit($baja->motivo_baja ?: '—', 60) }}</td>
                            <td class="px-2 py-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col gap-1" data-expand-ignore>
                                        <a href="{{ route('bajas.formato', $baja) }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">Acta</a>
                                        @if ($baja->urlAdjunto('adjunto_acta'))
                                            <a href="{{ $baja->urlAdjunto('adjunto_acta') }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">Firma</a>
                                        @endif
                                    </div>
                                    <span class="text-slate-400" data-expand-icon>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr id="detalle-baja-{{ $baja->id }}" class="hidden bg-slate-50">
                            <td colspan="10" class="px-4 py-5">
                                <div class="grid gap-6 md:grid-cols-2">
                                    <section class="space-y-3">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Datos del acta</h3>
                                        <dl class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Acta No.</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->acta_numero ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Fecha</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->fecha?->format('d/m/Y') ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Solicitante</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->solicitante_nombre ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Cargo</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->solicitante_cargo ?: '—' }}</dd>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <dt class="text-xs font-medium text-slate-500">Departamento</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->solicitante_departamento ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Correo enviado</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->correo_enviado_at?->format('d/m/Y H:i') ?: '—' }}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <section class="space-y-3">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Activo</h3>
                                        <dl class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Categoría</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->categoria ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Código</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->codigoMostrado() }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Descripción</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->descripcion ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Serial</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->serial ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Usuario inventario</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->usuario ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Departamento inventario</dt>
                                                <dd class="mt-1 text-sm text-slate-900">{{ $baja->departamento ?: '—' }}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <section class="space-y-3 md:col-span-2">
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Información y motivo</h3>
                                        <dl class="grid gap-3">
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Información</dt>
                                                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900">{{ $baja->informacion ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Motivo de la baja</dt>
                                                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900">{{ $baja->motivo_baja ?: '—' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500">Observaciones</dt>
                                                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-900">{{ $baja->observaciones ?: '—' }}</dd>
                                            </div>
                                        </dl>
                                    </section>

                                    <section class="space-y-3 md:col-span-2" data-expand-ignore>
                                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Documentos</h3>
                                        <div class="flex flex-wrap gap-3">
                                            <a href="{{ route('bajas.formato', $baja) }}" target="_blank" class="inline-flex items-center rounded-lg border border-brand-green/30 bg-white px-3 py-2 text-xs font-semibold text-brand-green hover:bg-brand-green-soft">
                                                Ver acta
                                            </a>
                                            @if ($baja->urlAdjunto('adjunto_acta'))
                                                <a href="{{ $baja->urlAdjunto('adjunto_acta') }}" target="_blank" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    Firma
                                                </a>
                                            @endif
                                            @foreach ($baja->urlsEvidencia() as $indice => $url)
                                                <a href="{{ $url }}" target="_blank" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    Evidencia{{ count($baja->urlsEvidencia()) > 1 ? ' '.($indice + 1) : '' }}
                                                </a>
                                            @endforeach
                                            @if ($baja->urlAdjunto('adjunto_soporte'))
                                                <a href="{{ $baja->urlAdjunto('adjunto_soporte') }}" target="_blank" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    Soporte
                                                </a>
                                            @endif
                                        </div>

                                        @if ($baja->urlsEvidencia() !== [])
                                            <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                                @foreach ($baja->urlsEvidencia() as $url)
                                                    <a href="{{ $url }}" target="_blank" class="block overflow-hidden rounded-lg border border-slate-200 bg-white">
                                                        <img src="{{ $url }}" alt="Evidencia" class="h-36 w-full object-cover">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </section>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>
        </div>
    </div>
</section>
