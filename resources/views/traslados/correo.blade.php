<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{{ \App\Support\TrasladoMensaje::asunto() }}</title>
        <style type="text/css">
            .tabla-traslados-scroll {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .tabla-traslados {
                width: 100% !important;
                min-width: 1100px !important;
                border-collapse: collapse !important;
            }
        </style>
    </head>
    <body style="margin:0;padding:12px;background:#f1f5f9;color:#0f172a;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.5;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;table-layout:fixed;width:100%;max-width:100%;background:#ffffff;border:1px solid #e2e8f0;">
            <tr>
                <td width="50%" style="height:6px;background:#17823a;font-size:0;line-height:0;">&nbsp;</td>
                <td width="50%" style="height:6px;background:#d0112b;font-size:0;line-height:0;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="padding:20px;">
                    <p style="margin:0 0 12px;">Cordial saludo para todos.</p>
                    <p style="margin:0 0 12px;">Reportó los últimos traslados de equipos de cómputo con su nuevo responsable.</p>
                    <p style="margin:0 0 12px;">Agradezco al área contable realizar los traslados de activos a cada responsable.</p>
                    <p style="margin:0 0 16px;">Si la tabla no cabe completa, desplace hacia la derecha para ver todas las columnas.</p>

                    <div class="tabla-traslados-scroll" style="display:block;width:100%;max-width:100%;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;">
                        <table class="tabla-traslados" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:11px;line-height:1.35;width:100%;min-width:1100px;">
                            <thead>
                                <tr style="background:#f8fafc;color:#475569;text-align:left;">
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;white-space:nowrap;">Ítem</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;white-space:nowrap;">Fecha</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Usuario Entrega</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Código Contable</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Área</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;white-space:nowrap;">Tipo</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Descripción del equipo</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;white-space:nowrap;">Serial</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Usuario Recibe</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Área Recibe</th>
                                    <th style="border:1px solid #cbd5e1;padding:4px 5px;">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($traslados as $traslado)
                                    <tr>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;white-space:nowrap;">{{ $loop->iteration }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;white-space:nowrap;">{{ $traslado->fecha?->format('d/m/Y') ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">{{ $traslado->usuario_entrega ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">{{ $traslado->codigoMostrado() }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">{{ $traslado->area_entrega ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;white-space:nowrap;">{{ $traslado->tipo ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">{{ $traslado->descripcion_equipo ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;white-space:nowrap;">{{ $traslado->serial ?: '—' }}</td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">
                                            {{ $traslado->usuario_recibe ?: '—' }}
                                            @if (filled($traslado->cargo_recibe))
                                                <br><span style="color:#64748b;">{{ $traslado->cargo_recibe }}</span>
                                            @endif
                                        </td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">
                                            {{ $traslado->area_recibe ?: '—' }}
                                            @if (filled($traslado->departamento_recibe))
                                                <br><span style="color:#64748b;">{{ $traslado->departamento_recibe }}</span>
                                            @endif
                                        </td>
                                        <td style="border:1px solid #cbd5e1;padding:4px 5px;vertical-align:top;">{{ $traslado->observaciones ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:28px;">
                        {!! \App\Support\FirmaHtml::paraCorreo(
                            $firma->contenido ?: e($firma->nombre),
                            $firma->imagen,
                            $message
                        ) !!}
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
