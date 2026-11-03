<?php

namespace App\Support;

use App\Models\Baja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BajaActaAdjunto
{
    public static function html(Baja $baja): string
    {
        return view('bajas.acta-adjunto', [
            'baja' => $baja,
            'logoSrc' => self::dataUriDesdeRuta(public_path('images/logo-colbeef.png')),
            'firmaSrc' => self::dataUriDesdeStorage(is_string($baja->adjunto_acta) ? $baja->adjunto_acta : null),
            'evidenciasSrc' => collect($baja->rutasEvidencia())
                ->map(fn (string $ruta): ?string => self::dataUriDesdeStorage($ruta))
                ->filter()
                ->values()
                ->all(),
        ])->render();
    }

    public static function pdf(Baja $baja): string
    {
        $html = self::html($baja);
        $chromium = self::chromiumBinary();

        if ($chromium !== null) {
            $pdf = self::pdfViaChromium($html, $chromium);

            if (is_string($pdf) && str_starts_with($pdf, '%PDF')) {
                return $pdf;
            }
        }

        return Pdf::loadHTML($html)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isFontSubsettingEnabled' => true,
                'isPhpEnabled' => false,
            ])
            ->output();
    }

    public static function nombreArchivo(Baja $baja): string
    {
        return BajaAdjuntoNombres::acta($baja);
    }

    private static function pdfViaChromium(string $html, string $binary): ?string
    {
        $directorio = storage_path('app/tmp/acta-pdf');
        File::ensureDirectoryExists($directorio);

        $token = bin2hex(random_bytes(8));
        $htmlPath = $directorio.DIRECTORY_SEPARATOR."acta-{$token}.html";
        $pdfPath = $directorio.DIRECTORY_SEPARATOR."acta-{$token}.pdf";

        try {
            File::put($htmlPath, $html);

            $process = new Process([
                $binary,
                '--headless=new',
                '--disable-gpu',
                '--allow-file-access-from-files',
                '--no-pdf-header-footer',
                '--print-to-pdf='.$pdfPath,
                self::fileUri($htmlPath),
            ]);
            $process->setTimeout(60);
            $process->run();

            if (! $process->isSuccessful() || ! is_file($pdfPath)) {
                return null;
            }

            $contenido = File::get($pdfPath);

            return is_string($contenido) && $contenido !== '' ? $contenido : null;
        } finally {
            if (is_file($htmlPath)) {
                @unlink($htmlPath);
            }

            if (is_file($pdfPath)) {
                @unlink($pdfPath);
            }
        }
    }

    private static function chromiumBinary(): ?string
    {
        $configurado = config('services.chrome.binary');

        $candidatos = array_filter([
            is_string($configurado) && $configurado !== '' ? $configurado : null,
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ]);

        foreach ($candidatos as $ruta) {
            if (is_string($ruta) && is_file($ruta)) {
                return $ruta;
            }
        }

        return null;
    }

    private static function fileUri(string $path): string
    {
        $normalizado = str_replace('\\', '/', $path);

        if (! str_starts_with($normalizado, '/')) {
            $normalizado = '/'.$normalizado;
        }

        return 'file://'.$normalizado;
    }

    private static function dataUriDesdeStorage(?string $ruta): ?string
    {
        if (! filled($ruta) || ! Storage::disk('public')->exists($ruta)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($ruta) ?: 'application/octet-stream';

        if (! str_starts_with((string) $mime, 'image/')) {
            return null;
        }

        $contenido = Storage::disk('public')->get($ruta);

        if (! is_string($contenido) || $contenido === '') {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($contenido);
    }

    private static function dataUriDesdeRuta(string $ruta): ?string
    {
        if (! is_file($ruta)) {
            return null;
        }

        $contenido = file_get_contents($ruta);

        if ($contenido === false || $contenido === '') {
            return null;
        }

        $mime = mime_content_type($ruta) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($contenido);
    }
}
