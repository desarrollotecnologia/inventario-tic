<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{{ \App\Support\BajaMensaje::asunto() }}</title>
        <style type="text/css">
            .tabla-bajas-scroll {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .tabla-bajas {
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
                    <p style="margin:0 0 16px;">Envío listado y registro fotográfico de equipos de cómputo a los cuales se debe dar de baja del inventario de activos por múltiples causas reportadas a continuación.</p>

                    <div class="tabla-bajas-scroll" style="display:block;width:100%;max-width:100%;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;">
                        <table class="tabla-bajas" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:11px;line-height:1.35;width:100%;min-width:1100px;">
                            <thead>
                                <tr>
                                    <th colspan="10" style="border:1px solid #000000;padding:8px 6px;background:#17823a;color:#ffffff;font-size:13px;font-weight:700;text-align:center;">
                                        {{ $tituloTabla }}
                                    </th>
                                </tr>
                                <tr style="background:#d0112b;color:#ffffff;text-align:center;">
                                    <th style="border:1px solid #000000;padding:5px 4px;white-space:nowrap;color:#ffffff;">ITEM</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;white-space:nowrap;color:#ffffff;">FECHA</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">DESCRIPCIÓN</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">MARCA Y MODELO</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;white-space:nowrap;color:#ffffff;">SERIAL</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">USUARIO</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">COD. CONTABLE</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">RESPONSABLE ÁREA</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">MOTIVO</th>
                                    <th style="border:1px solid #000000;padding:5px 4px;color:#ffffff;">OBSERVACIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bajas as $baja)
                                    <tr>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;white-space:nowrap;">{{ $loop->iteration }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;white-space:nowrap;">{{ $baja->fecha?->format('d/m/Y') ?: '—' }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->tipo ?: ($baja->categoria ?: '—') }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->descripcion ?: '—' }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;white-space:nowrap;">{{ $baja->serial ?: 'N/A' }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->usuario ?: '—' }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->codigoMostrado() === '—' ? 'N/A' : $baja->codigoMostrado() }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->area ?: ($baja->departamento ?: '—') }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->motivo_baja ?: '—' }}</td>
                                        <td style="border:1px solid #000000;padding:5px 4px;vertical-align:middle;text-align:center;">{{ $baja->observaciones ?: '—' }}</td>
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
