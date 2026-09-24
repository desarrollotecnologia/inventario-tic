<?php

namespace App\Support;

use App\Models\EquipoComputo;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EquipoComputoExcel
{
    /**
     * @return list<string>
     */
    public static function encabezados(): array
    {
        return [
            'Ítem',
            'Código Contable',
            'Departamento',
            'Área',
            'Usuario',
            'Cargo',
            'Descripción',
            'Marca y Referencia',
            'Service Tag / Product Name',
            'Serial adicional',
            'Procesador',
            'Memoria',
            'Disco',
            'Windows',
            'Serial Windows',
            'Office',
            'Serial Office',
            'Cuenta Office365',
            'Antivirus',
            'Usuario Deslock ESET',
            'Contraseña Deslock ESET',
            'Nombre de equipo',
            'MAC cableada',
            'MAC inalámbrica',
            'Observaciones',
            'Fecha adquisición',
            'Fecha garantía',
            'Disponible',
            'Traslado',
            'Baja',
        ];
    }

    public static function nombreArchivo(): string
    {
        return 'Inventario_Equipos_Computo_'.now()->format('Y-m-d_His').'.xlsx';
    }

    /**
     * @param  Collection<int, EquipoComputo>  $equipos
     */
    public static function bytes(Collection $equipos): string
    {
        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Equipos');
        $hoja->fromArray([
            self::encabezados(),
            ...self::filas($equipos),
        ], null, 'A1');

        $ultimaColumna = Coordinate::stringFromColumnIndex(count(self::encabezados()));
        $ultimaFila = max(1, 1 + $equipos->count());
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
        $ruta = sys_get_temp_dir().DIRECTORY_SEPARATOR.'equipos_'.uniqid('', true).'.xlsx';

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
     * @param  Collection<int, EquipoComputo>  $equipos
     * @return list<list<string>>
     */
    public static function filas(Collection $equipos): array
    {
        return $equipos
            ->values()
            ->map(fn (EquipoComputo $equipo): array => [
                (string) ($equipo->item ?: '—'),
                (string) ($equipo->codigo ?: '—'),
                (string) ($equipo->departamento ?: '—'),
                (string) ($equipo->area ?: '—'),
                (string) ($equipo->usuario ?: '—'),
                (string) ($equipo->cargo ?: '—'),
                (string) ($equipo->descripcion ?: '—'),
                (string) ($equipo->marca_referencia ?: '—'),
                (string) ($equipo->service_tag_product_name ?: '—'),
                (string) ($equipo->serial_adicional ?: '—'),
                (string) ($equipo->procesador ?: '—'),
                (string) ($equipo->memoria ?: '—'),
                (string) ($equipo->disco ?: '—'),
                (string) ($equipo->windows ?: '—'),
                (string) ($equipo->serial_windows ?: '—'),
                (string) ($equipo->office ?: '—'),
                (string) ($equipo->serial_office ?: '—'),
                (string) ($equipo->cuenta_office365 ?: '—'),
                (string) ($equipo->antivirus ?: '—'),
                (string) ($equipo->usuario_deslock_eset ?: '—'),
                (string) ($equipo->contrasena_deslock_eset ?: '—'),
                (string) ($equipo->nombre_equipo ?: '—'),
                (string) ($equipo->mac_cableada ?: '—'),
                (string) ($equipo->mac_inalambrica ?: '—'),
                (string) ($equipo->observaciones ?: '—'),
                $equipo->fecha_adquisicion?->format('d/m/Y') ?: '—',
                $equipo->fecha_garantia?->format('d/m/Y') ?: '—',
                $equipo->en_disponible ? 'Sí' : 'No',
                $equipo->en_traslado ? 'Sí' : 'No',
                $equipo->en_baja ? 'Sí' : 'No',
            ])
            ->values()
            ->all();
    }
}
