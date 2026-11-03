<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Acta de Baja No. {{ $baja->acta_numero }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            background: #ffffff;
        }

        @include('bajas.partials.acta-estilos')
    </style>
</head>
<body>
    @include('bajas.partials.acta-documento', [
        'baja' => $baja,
        'logoSrc' => $logoSrc,
        'firmaSrc' => $firmaSrc,
        'evidenciasSrc' => $evidenciasSrc,
    ])
</body>
</html>
