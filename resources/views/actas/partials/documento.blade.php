@php
    /** @var \App\Models\ActaSalida|\App\Models\ActaEntrega $acta */
    $equipos = is_array($acta->equipos) ? $acta->equipos : [];
    $logo = $logoSrc ?? asset('images/logo-colbeef.png');
@endphp

<div class="copia-acta">
    <div class="encabezado">
        <div class="fecha">{{ $acta->fechaFormateada() }}</div>
        <div class="marca">
            @if ($logo)
                <img src="{{ $logo }}" alt="Colbeef" class="logo">
            @else
                <div class="logo-texto"><span class="col">Col</span><span class="beef">beef</span></div>
            @endif
            <div class="empresa">COLBEEF SAS</div>
            <div class="titulo">{{ $titulo }}</div>
        </div>
    </div>

    <p class="intro">{{ $acta->textoIntroduccion() }}</p>

    <table class="equipos">
        <thead>
            <tr>
                <th class="col-cant">Cantidad</th>
                <th class="col-desc">Descripción</th>
                <th class="col-marca">Marca</th>
                <th class="col-modelo">Modelo</th>
                <th class="col-serial">Serial</th>
                <th class="col-obs">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipos as $equipo)
                <tr>
                    <td class="centro">{{ $equipo['cantidad'] ?? '' }}</td>
                    <td>{{ $equipo['descripcion'] ?? '' }}</td>
                    <td>{{ $equipo['marca'] ?? '' }}</td>
                    <td>{{ $equipo['modelo'] ?? '' }}</td>
                    <td class="serial">{!! nl2br(e($equipo['serial'] ?? '')) !!}</td>
                    <td class="obs">{!! nl2br(e($equipo['observaciones'] ?? '')) !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="firmas">
        <div class="firma">
            <div class="etiqueta">Persona quien entrega:</div>
            <div class="nombre">{{ $acta->entregado_por_nombre }}</div>
            <div class="cargo">{{ $acta->entregado_por_cargo }}</div>
            <div class="linea"></div>
        </div>
        <div class="firma">
            <div class="etiqueta">Persona quien recibe:</div>
            <div class="nombre">{{ $acta->recibido_por_nombre }}</div>
            <div class="cargo">CC: {{ $acta->recibido_por_documento ?: '____________________' }}</div>
            <div class="linea"></div>
        </div>
    </div>
</div>
