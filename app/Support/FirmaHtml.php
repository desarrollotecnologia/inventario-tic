<?php

namespace App\Support;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmaHtml
{
    public static function sanitizar(string $html): string
    {
        $html = strip_tags($html, '<p><br><div><span><strong><b><em><i><u><img><a><table><tbody><thead><tr><td><th><font>');
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/javascript:/i', '', $html) ?? $html;

        return trim($html);
    }

    /**
     * @return array{html: string, imagen: string|null}
     */
    public static function persistirImagenes(string $html): array
    {
        $primera = null;

        $html = preg_replace_callback(
            '/src="(data:image\/(?:png|jpeg|jpg|webp|gif);base64,[^"]+)"/i',
            function (array $match) use (&$primera): string {
                $ruta = self::guardarDataUrl($match[1]);

                if ($ruta === null) {
                    return 'src=""';
                }

                $primera ??= $ruta;

                return 'src="'.e($ruta).'"';
            },
            $html
        ) ?? $html;

        return [
            'html' => $html,
            'imagen' => $primera,
        ];
    }

    public static function paraCorreo(string $html, ?string $imagen, Message $message): string
    {
        $reemplazado = preg_replace_callback(
            '/src="([^"]+)"/',
            function (array $match) use ($message): string {
                $origen = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');

                if (str_starts_with($origen, 'cid:') || str_starts_with($origen, 'http://') || str_starts_with($origen, 'https://')) {
                    return $match[0];
                }

                $ruta = Storage::disk('public')->path($origen);

                if (! is_file($ruta)) {
                    return $match[0];
                }

                return 'src="'.$message->embed($ruta).'"';
            },
            $html
        ) ?? $html;

        if (! str_contains($reemplazado, '<img') && filled($imagen)) {
            $ruta = Storage::disk('public')->path($imagen);

            if (is_file($ruta)) {
                $reemplazado = '<img src="'.$message->embed($ruta).'" alt="Firma" style="display:block;max-width:320px;height:auto;">'.$reemplazado;
            }
        }

        return $reemplazado;
    }

    public static function tieneContenido(string $html): bool
    {
        $html = preg_replace('/<br\s*\/?>/i', ' ', $html) ?? $html;
        $texto = trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));

        return $texto !== '' || str_contains(strtolower($html), '<img');
    }

    public static function nombreDesde(string $html): string
    {
        $html = preg_replace('/<br\s*\/?>/i', ' ', $html) ?? $html;
        $texto = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8')) ?? '');

        if ($texto === '') {
            return 'Firma';
        }

        return Str::limit($texto, 80, '');
    }

    private static function guardarDataUrl(string $dataUrl): ?string
    {
        if (! preg_match('/^data:image\/(png|jpeg|jpg|webp|gif);base64,(.+)$/i', $dataUrl, $partes)) {
            return null;
        }

        $binario = base64_decode($partes[2], true);

        if ($binario === false || $binario === '') {
            return null;
        }

        $extension = strtolower($partes[1]) === 'jpeg' ? 'jpg' : strtolower($partes[1]);
        $ruta = 'firmas/'.Str::uuid().'.'.$extension;
        Storage::disk('public')->put($ruta, $binario);

        return $ruta;
    }
}
