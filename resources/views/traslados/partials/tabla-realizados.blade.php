<section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
    <div class="overflow-hidden rounded-[14px] bg-white">
        <div class="flex h-1.5">
            <div class="w-1/2 bg-brand-green"></div>
            <div class="w-1/2 bg-brand-red"></div>
        </div>
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-800">{{ $titulo }}</h2>
                @isset($descripcion)
                    <p class="mt-0.5 text-xs text-slate-500">{{ $descripcion }}</p>
                @endisset
            </div>
            @if ($conAcciones ?? false)
                <div class="flex flex-wrap items-center gap-2">
                    <script type="application/json" data-whatsapp-mensaje>@json($mensajeWhatsapp)</script>
                    <a
                        href="{{ $urlWhatsapp }}"
                        target="_blank"
                        rel="noopener"
                        data-whatsapp-share
                        class="inline-flex items-center gap-1.5 rounded-lg bg-[#25D366] px-3 py-2 text-xs font-semibold text-white hover:bg-[#1da851]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.52 3.48A11.78 11.78 0 0 0 12.06 0C5.5 0 .16 5.33.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.76 1.47h.01c6.56 0 11.9-5.34 11.9-11.91 0-3.18-1.24-6.17-3.45-8.43ZM12.07 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.64-.24-.37a9.86 9.86 0 0 1-1.51-5.28c0-5.45 4.44-9.88 9.9-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.9 6.98c0 5.45-4.44 9.9-9.89 9.9Zm5.43-7.41c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
                        </svg>
                        WhatsApp
                    </a>
                    <button
                        type="button"
                        data-open-dialog="modal-correo"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-green px-3 py-2 text-xs font-semibold text-white hover:bg-brand-green-dark"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" />
                        </svg>
                        Correo
                    </button>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[80rem] w-full table-fixed text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                    <tr>
                        <th class="w-[5%] px-2 py-2.5 font-medium">Ítem</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Fecha</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Usuario Entrega</th>
                        <th class="w-[9%] px-2 py-2.5 font-medium">Código Contable</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Área</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Tipo</th>
                        <th class="w-[11%] px-2 py-2.5 font-medium">Descripción del equipo</th>
                        <th class="w-[9%] px-2 py-2.5 font-medium">Serial</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Usuario Recibe</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Área Recibe</th>
                        <th class="w-[12%] px-2 py-2.5 font-medium">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($traslados as $traslado)
                        <tr class="text-slate-700">
                            <td class="break-words px-2 py-2.5">{{ $loop->iteration }}</td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->fecha?->format('d/m/Y') ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->usuario_entrega ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $traslado->codigoMostrado() }}</td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->area_entrega ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->tipo ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->descripcion_equipo ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5 font-medium text-slate-900">{{ $traslado->serial ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">
                                {{ $traslado->usuario_recibe ?: '—' }}
                                @if (filled($traslado->cargo_recibe))
                                    <span class="mt-0.5 block text-[10px] text-slate-500">{{ $traslado->cargo_recibe }}</span>
                                @endif
                            </td>
                            <td class="break-words px-2 py-2.5">
                                {{ $traslado->area_recibe ?: '—' }}
                                @if (filled($traslado->departamento_recibe))
                                    <span class="mt-0.5 block text-[10px] text-slate-500">{{ $traslado->departamento_recibe }}</span>
                                @endif
                            </td>
                            <td class="break-words px-2 py-2.5">{{ $traslado->observaciones ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
