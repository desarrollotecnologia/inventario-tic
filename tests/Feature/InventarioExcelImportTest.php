<?php

namespace Tests\Feature;

use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Support\InventarioExcelImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class InventarioExcelImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_inventory_can_be_imported_and_splits_mobiles(): void
    {
        $ruta = 'C:/Users/tecno/Downloads/inventario.xlsx';

        if (! is_file($ruta)) {
            $this->markTestSkipped('No está el archivo inventario.xlsx en Descargas.');
        }

        EquipoComputo::factory()->create(['item' => '999']);
        Celular::factory()->create(['item' => '888']);

        $exit = Artisan::call('inventario:importar', [
            'ruta' => $ruta,
            '--fresh' => true,
        ]);

        $this->assertSame(0, $exit);

        [$avisos, $equipos, $celulares] = InventarioExcelImport::parsear($ruta);

        $this->assertSame(count($equipos), EquipoComputo::query()->count());
        $this->assertSame(count($celulares), Celular::query()->count());
        $this->assertGreaterThan(150, EquipoComputo::query()->count());
        $this->assertGreaterThan(10, Celular::query()->count());
        $this->assertTrue(EquipoComputo::query()->where('en_baja', true)->exists());
        $this->assertTrue(
            EquipoComputo::query()
                ->whereDate('fecha_adquisicion', '2014-01-01')
                ->exists()
        );
    }
}
