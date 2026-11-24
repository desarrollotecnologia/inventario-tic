<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo }}</title>
    <style>
        body {
            margin: 0;
            background: #e2e8f0;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            gap: 0.75rem;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #fff;
            border-bottom: 1px solid #cbd5e1;
            font-family: system-ui, sans-serif;
        }

        .toolbar a, .toolbar button {
            border: 0;
            border-radius: 0.5rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .toolbar .volver { background: #e2e8f0; color: #334155; }
        .toolbar .editar { background: #dbeafe; color: #1e40af; }
        .toolbar .imprimir { background: #17823a; color: #fff; }

        .hoja {
            width: min(8.5in, calc(100% - 2rem));
            min-height: 11in;
            margin: 1rem auto 2rem;
            padding: 0.55in 0.65in;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            box-sizing: border-box;
        }

        .separador {
            margin: 0.7in 0 0.55in;
            border-top: 1px dashed #94a3b8;
        }

        @include('actas.partials.estilos')

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .hoja {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0.45in 0.55in;
                box-shadow: none;
            }
            .separador { border-top-color: transparent; margin: 0.45in 0 0.4in; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div style="display:flex;gap:0.5rem;">
            <a class="volver" href="{{ $volver }}">← Volver</a>
            <a class="editar" href="{{ $editar }}">Editar</a>
        </div>
        <button class="imprimir" type="button" onclick="window.print()">Imprimir / PDF</button>
    </div>

    <article class="hoja">
        @include('actas.partials.documento', ['acta' => $acta, 'titulo' => $titulo])
        <div class="separador"></div>
        @include('actas.partials.documento', ['acta' => $acta, 'titulo' => $titulo])
    </article>
</body>
</html>
