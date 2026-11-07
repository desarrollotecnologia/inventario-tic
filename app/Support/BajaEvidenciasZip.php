<?php

namespace App\Support;

use App\Models\Baja;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BajaEvidenciasZip
{
    /**
     * @param  Collection<int, Baja>  $bajas
     */
    public static function bytes(Collection $bajas): ?string
    {
        $archivos = [];

        foreach ($bajas as $baja) {
            $carpeta = self::nombreCarpeta($baja);

            foreach ($baja->rutasEvidencia() as $indice => $ruta) {
                if (! Storage::disk('public')->exists($ruta)) {
                    continue;
                }

                $extension = pathinfo($ruta, PATHINFO_EXTENSION) ?: 'jpg';
                $nombre = BajaAdjuntoNombres::evidencia($baja, $extension, $indice + 1);
                $archivos[$carpeta.'/'.$nombre] = Storage::disk('public')->path($ruta);
            }

            $soporte = $baja->adjunto_soporte;

            if (filled($soporte) && Storage::disk('public')->exists($soporte)) {
                $extension = pathinfo((string) $soporte, PATHINFO_EXTENSION) ?: 'bin';
                $nombre = BajaAdjuntoNombres::diagnostico($baja, $extension);
                $archivos[$carpeta.'/'.$nombre] = Storage::disk('public')->path((string) $soporte);
            }
        }

        if ($archivos === []) {
            return null;
        }

        $directorio = storage_path('app/tmp/bajas-zip');
        File::ensureDirectoryExists($directorio);

        $rutaZip = $directorio.DIRECTORY_SEPARATOR.'evidencias-'.bin2hex(random_bytes(8)).'.zip';
        $zip = new ZipArchive;

        if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        try {
            foreach ($archivos as $rutaEnZip => $rutaLocal) {
                $zip->addFile($rutaLocal, $rutaEnZip);
            }

            $zip->close();

            $contenido = File::get($rutaZip);

            return is_string($contenido) && $contenido !== '' ? $contenido : null;
        } finally {
            if (is_file($rutaZip)) {
                @unlink($rutaZip);
            }
        }
    }

    public static function nombreArchivo(): string
    {
        return 'Evidencias_Bajas_'.now()->translatedFormat('F_Y').'.zip';
    }

    public static function nombreCarpeta(Baja $baja): string
    {
        $marca = BajaAdjuntoNombres::descripcion($baja);
        $serial = trim((string) ($baja->serial ?: ''));
        $serial = BajaAdjuntoNombres::sanear($serial !== '' ? $serial : 'SIN_SERIAL');

        return $marca.'_'.$serial;
    }
}
