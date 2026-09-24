<?php

namespace Tests\Feature;

use App\Models\EquipoComputo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class EquipoComputoExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_list_can_be_exported_to_excel_with_filters(): void
    {
        EquipoComputo::factory()->create([
            'item' => '1',
            'usuario' => 'ANA PEREZ',
            'departamento' => 'GERENCIA',
            'en_baja' => true,
        ]);
        EquipoComputo::factory()->create([
            'item' => '2',
            'usuario' => 'CARLOS RUIZ',
            'departamento' => 'OPERACIONES',
            'en_baja' => false,
        ]);

        $respuesta = $this->get(route('equipos-computo.exportar', ['baja' => '1']));

        $respuesta->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $respuesta->headers->get('content-type')
        );

        $ruta = sys_get_temp_dir().DIRECTORY_SEPARATOR.'equipos_test_'.uniqid('', true).'.xlsx';
        file_put_contents($ruta, $respuesta->streamedContent());

        try {
            $hoja = IOFactory::load($ruta)->getActiveSheet();
            $this->assertSame('Ítem', $hoja->getCell('A1')->getValue());
            $this->assertSame('ANA PEREZ', $hoja->getCell('E2')->getValue());
            $this->assertNull($hoja->getCell('E3')->getValue());
        } finally {
            if (is_file($ruta)) {
                unlink($ruta);
            }
        }
    }
}
