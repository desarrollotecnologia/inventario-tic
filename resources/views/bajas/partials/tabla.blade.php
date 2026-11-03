<section class="borde-institucional mt-6 rounded-2xl p-[2px] shadow-sm">
    <div class="overflow-hidden rounded-[14px] bg-white">
        <div class="flex h-1.5">
            <div class="w-1/2 bg-brand-green"></div>
            <div class="w-1/2 bg-brand-red"></div>
        </div>
        <div class="border-b border-slate-100 px-4 py-3">
            <h2 class="text-sm font-semibold text-slate-800">{{ $titulo }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[80rem] w-full table-fixed text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase leading-tight tracking-wide text-slate-500">
                    <tr>
                        <th class="w-[4%] px-2 py-2.5 font-medium">Ítem</th>
                        <th class="w-[7%] px-2 py-2.5 font-medium">Fecha</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Acta No.</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Solicitante</th>
                        <th class="w-[9%] px-2 py-2.5 font-medium">Categoría</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Código</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Descripción</th>
                        <th class="w-[8%] px-2 py-2.5 font-medium">Serial</th>
                        <th class="w-[16%] px-2 py-2.5 font-medium">Motivo</th>
                        <th class="w-[10%] px-2 py-2.5 font-medium">Documentos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($bajas as $baja)
                        <tr class="text-slate-700">
                            <td class="break-words px-2 py-2.5">{{ $loop->iteration }}</td>
                            <td class="break-words px-2 py-2.5">{{ $baja->fecha?->format('d/m/Y') ?: '—' }}</td>
                            <td class="break-words px-2 py-2.5">{{ $baja->acta_numero ?: '—' }}</td>
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
                            <td class="break-words px-2 py-2.5">{{ \Illuminate\Support\Str::limit($baja->motivo_baja ?: '—', 80) }}</td>
                            <td class="break-words px-2 py-2.5">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('bajas.formato', $baja) }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">Acta</a>
                                    @if ($baja->urlAdjunto('adjunto_acta'))
                                        <a href="{{ $baja->urlAdjunto('adjunto_acta') }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">Firma</a>
                                    @endif
                                    @foreach ($baja->urlsEvidencia() as $indice => $url)
                                        <a href="{{ $url }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">
                                            Evidencia{{ count($baja->urlsEvidencia()) > 1 ? ' '.($indice + 1) : '' }}
                                        </a>
                                    @endforeach
                                    @if ($baja->urlAdjunto('adjunto_soporte'))
                                        <a href="{{ $baja->urlAdjunto('adjunto_soporte') }}" target="_blank" class="font-medium text-brand-green hover:text-brand-green-dark">Soporte</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
