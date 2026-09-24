<?php

namespace App\Console\Commands;

use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Support\InventarioExcelImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarInventarioExcelCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'inventario:importar
                            {ruta : Ruta absoluta del archivo .xlsx}
                            {--fresh : Vacía equipos de cómputo y celulares antes de importar}';

    /**
     * @var string
     */
    protected $description = 'Importa el inventario desde el Excel institucional';

    public function handle(): int
    {
        $ruta = (string) $this->argument('ruta');

        if (! is_file($ruta)) {
            $this->error("No se encontró el archivo: {$ruta}");

            return self::FAILURE;
        }

        [$avisos, $equipos, $celulares] = InventarioExcelImport::parsear($ruta);

        foreach ($avisos as $aviso) {
            $this->warn($aviso);
        }

        DB::transaction(function () use ($equipos, $celulares): void {
            if ($this->option('fresh')) {
                EquipoComputo::query()->delete();
                Celular::query()->delete();
            }

            $codigosVistos = [];

            foreach ($equipos as $datos) {
                $codigo = $datos['codigo'] ?? null;

                if (filled($codigo)) {
                    $clave = mb_strtolower((string) $codigo);

                    if (isset($codigosVistos[$clave])) {
                        $datos['codigo'] = null;
                    } else {
                        $codigosVistos[$clave] = true;
                    }
                }

                EquipoComputo::query()->create($datos);
            }

            foreach ($celulares as $datos) {
                Celular::query()->create($datos);
            }
        });

        $this->info('Importación completada.');
        $this->line('Equipos de cómputo: '.count($equipos));
        $this->line('Celulares / tablets: '.count($celulares));
        $this->line('Equipos en baja: '.collect($equipos)->where('en_baja', true)->count());
        $this->line('Móviles en baja: '.collect($celulares)->where('en_baja', true)->count());

        return self::SUCCESS;
    }
}
