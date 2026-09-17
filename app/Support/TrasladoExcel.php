<?php

namespace App\Support;

use App\Models\Traslado;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TrasladoExcel
{
    /**
     * @return list<string>
     */
    public static function encabezados(): array
    {
        return [
            'Ítem',
            'Fecha',
            'Usuario Entrega',
            'Código Contable',
            'Área',
            'Tipo',
            'Descripción del equipo',
            'Serial',
            'Usuario Recibe',
            'Área Recibe',
            'Observaciones',
        ];
    }

    public static function nombreArchivo(): string
    {
        return 'Traslados_'.TrasladoMensaje::mes().'_'.now()->year.'.xlsx';
    }

    /**
     * @param  Collection<int, Traslado>  $traslados
     */
    public static function bytes(Collection $traslados): string
    {
        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Traslados');
        $hoja->fromArray([
            self::encabezados(),
            ...self::filas($traslados),
        ], null, 'A1');

        $ultimaColumna = Coordinate::stringFromColumnIndex(count(self::encabezados()));
        $ultimaFila = max(1, 1 + $traslados->count());
        $rango = "A1:{$ultimaColumna}{$ultimaFila}";

        $hoja->getStyle("A1:{$ultimaColumna}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '17823A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $hoja->getStyle($rango)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ]);

        foreach (range(1, count(self::encabezados())) as $columna) {
            $hoja->getColumnDimensionByColumn($columna)->setAutoSize(true);
        }

        $hoja->freezePane('A2');
        $hoja->setAutoFilter($rango);

        $writer = new Xlsx($spreadsheet);
        $ruta = sys_get_temp_dir().DIRECTORY_SEPARATOR.'traslados_'.uniqid('', true).'.xlsx';

        try {
            $writer->save($ruta);

            return (string) file_get_contents($ruta);
        } finally {
            $spreadsheet->disconnectWorksheets();

            if (is_file($ruta)) {
                unlink($ruta);
            }
        }
    }

    /**
     * @param  Collection<int, Traslado>  $traslados
     * @return list<list<string>>
     */
    public static function filas(Collection $traslados): array
    {
        return $traslados
            ->values()
            ->map(fn (Traslado $traslado, int $indice): array => [
                (string) ($indice + 1),
                $traslado->fecha?->format('d/m/Y') ?: '—',
                $traslado->usuario_entrega ?: '—',
                $traslado->codigoMostrado(),
                $traslado->area_entrega ?: '—',
                $traslado->tipo ?: '—',
                $traslado->descripcion_equipo ?: '—',
                $traslado->serial ?: '—',
                self::conDetalle($traslado->usuario_recibe, $traslado->cargo_recibe),
                self::conDetalle($traslado->area_recibe, $traslado->departamento_recibe),
                $traslado->observaciones ?: '—',
            ])
            ->values()
            ->all();
    }

    private static function conDetalle(?string $principal, ?string $detalle): string
    {
        $texto = filled($principal) ? $principal : '—';

        if (! filled($detalle)) {
            return $texto;
        }

        return $texto."\n".$detalle;
    }
}
