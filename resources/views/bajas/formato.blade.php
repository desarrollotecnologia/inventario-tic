<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acta de Baja No. {{ $baja->acta_numero }}</title>
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
        .toolbar .imprimir { background: #17823a; color: #fff; }

        .hoja {
            width: min(8.5in, calc(100% - 2rem));
            min-height: 11in;
            margin: 1rem auto 2rem;
            padding: 2.5cm;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        @include('bajas.partials.acta-estilos')

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .hoja { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a class="volver" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('bajas.historial') }}">← Volver</a>
        <button class="imprimir" type="button" onclick="window.print()">Imprimir / PDF</button>
    </div>

    <article class="hoja">
        @include('bajas.partials.acta-documento', ['baja' => $baja])
    </article>
</body>
</html>
