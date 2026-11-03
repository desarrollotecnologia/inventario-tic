@php
    $logo = $logoSrc ?? asset('images/logo-colbeef.png');
    $firma = $firmaSrc ?? $baja->urlAdjunto('adjunto_acta');
    $evidencias = $evidenciasSrc ?? $baja->urlsEvidencia();
@endphp

<div class="documento">
    <table class="tabla-encabezado">
        <tr>
            <td class="col-control">
                <table class="tabla-control">
                    <tr><td>CODIGO: RFR – AF2</td></tr>
                    <tr><td>FECHA: 24/11/2014</td></tr>
                    <tr><td>VERSIÓN: 01</td></tr>
                    <tr><td>PAGINA 1 DE 1</td></tr>
                </table>
            </td>
            <td class="col-titulo">
                <div class="label-formato">FORMATO</div>
                <div class="titulo">ACTA DE BAJA ACTIVOS FIJOS E<br>INVENTARIOS</div>
            </td>
            <td class="col-logo">
                @if ($logo)
                    <img src="{{ $logo }}" alt="Colbeef">
                @else
                    <strong class="logo-texto">Colbeef</strong>
                @endif
                <div class="acta-numero">ACTA No. {{ $baja->acta_numero ?: '' }}</div>
            </td>
        </tr>
    </table>

    <table class="tabla-cuerpo">
        <tr>
            <td class="celda-dato celda-nombre">
                Nombre Completo:
                <strong>{{ $baja->solicitante_nombre ? mb_strtoupper($baja->solicitante_nombre) : '' }}</strong>
            </td>
            <td class="celda-dato celda-fecha">
                Fecha:
                <strong>{{ $baja->fecha?->format('d/m/Y') ?: '' }}</strong>
            </td>
        </tr>
        <tr>
            <td class="celda-dato celda-nombre">
                Cargo:
                <strong>{{ $baja->solicitante_cargo ? mb_strtoupper($baja->solicitante_cargo) : '' }}</strong>
            </td>
            <td class="celda-dato celda-fecha">
                Dpto:
                <strong>{{ $baja->solicitante_departamento ? mb_strtoupper($baja->solicitante_departamento) : '' }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="celda-info-titulo">INFORMACIÓN:</td>
        </tr>
        <tr>
            <td colspan="2" class="celda-info-cuerpo">
                <div class="texto-info">{{ $baja->informacion ?: '' }}</div>

                <div class="texto-motivo"><strong>Motivo de la Baja:</strong> {{ $baja->motivo_baja ?: '' }}</div>

                @if ($evidencias !== [])
                    <table class="evidencias">
                        @foreach (array_chunk($evidencias, 2) as $fila)
                            <tr>
                                @foreach ($fila as $src)
                                    <td><img src="{{ $src }}" alt="Evidencia fotográfica"></td>
                                @endforeach
                                @if (count($fila) === 1)
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
            </td>
        </tr>
    </table>

    <table class="firmas">
        <tr>
            <td>
                <div class="firma-espacio">
                    @if ($firma)
                        <img src="{{ $firma }}" alt="Firma solicitante">
                    @endif
                </div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma quien Solicita la baja</div>
            </td>
            <td>
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma Administrador Activos Fijos</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma Gerencia Admón. y Financiero</div>
            </td>
            <td>
                <div class="firma-espacio"></div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma Gerencia General</div>
            </td>
        </tr>
    </table>
</div>
