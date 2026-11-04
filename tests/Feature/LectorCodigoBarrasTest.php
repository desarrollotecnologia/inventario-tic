<?php

namespace Tests\Feature;

use App\Models\LectorCodigoBarras;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LectorCodigoBarrasTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_is_listed_and_shows_form_fields(): void
    {
        $this->get(route('modules.relacion'))
            ->assertOk()
            ->assertSee('/relacion-inventario-sistemas/lectores-codigos-barras', false)
            ->assertSee('Inventario de lectores de códigos de barras.')
            ->assertDontSee('Próximamente');

        $this->get(route('lectores-codigos-barras.index'))
            ->assertOk()
            ->assertSee('Lectores Códigos de Barras')
            ->assertSee('Registrar lector')
            ->assertSee('Aún no hay lectores de códigos de barras registrados.');

        $this->get(route('lectores-codigos-barras.create'))
            ->assertOk()
            ->assertSee('Registrar lector de códigos de barras')
            ->assertSee('Número inventario')
            ->assertSee('Código contable')
            ->assertSee('Departamento')
            ->assertSee('Área')
            ->assertSee('Usuario')
            ->assertSee('Cargo')
            ->assertSee('Marca y Referencia')
            ->assertSee('Modelo')
            ->assertSee('Serial')
            ->assertSee('Observaciones')
            ->assertSee('Zebra');
    }

    public function test_a_lector_can_be_registered_updated_filtered_and_deleted(): void
    {
        $this->post(route('lectores-codigos-barras.store'), [
            'codigo_contable' => 'CB-100',
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista TIC',
            'marca_referencia' => 'Zebra',
            'modelo' => 'DS2208',
            'serial' => 'SN-LCB-001',
            'observaciones' => 'Bodega principal',
        ])->assertRedirect(route('lectores-codigos-barras.index'));

        $this->assertDatabaseHas('lectores_codigos_barras', [
            'item' => '1',
            'codigo_contable' => 'CB-100',
            'usuario' => 'ANA PÉREZ',
            'marca_referencia' => 'ZEBRA',
            'modelo' => 'DS2208',
            'serial' => 'SN-LCB-001',
        ]);

        $lector = LectorCodigoBarras::query()->firstOrFail();

        $this->get(route('lectores-codigos-barras.index'))
            ->assertOk()
            ->assertSee('1 registro')
            ->assertSee('CB-100')
            ->assertSee('ANA PÉREZ')
            ->assertSee('ZEBRA')
            ->assertSee('SN-LCB-001');

        LectorCodigoBarras::factory()->create([
            'codigo_contable' => 'CB-999',
            'usuario' => 'Otro Usuario',
            'marca_referencia' => 'Honeywell',
        ]);

        $this->get(route('lectores-codigos-barras.index', ['codigo_contable' => 'CB-100']))
            ->assertOk()
            ->assertSee('ANA PÉREZ')
            ->assertDontSee('Otro Usuario')
            ->assertSee('1 de 2 registros');

        $this->put(route('lectores-codigos-barras.update', $lector), [
            'codigo_contable' => 'CB-100',
            'departamento' => 'OPERACIONES',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'marca_referencia' => 'Honeywell',
            'modelo' => 'Voyager 1200g',
            'serial' => 'SN-LCB-001',
            'observaciones' => 'Actualizado',
        ])->assertRedirect(route('lectores-codigos-barras.index'));

        $this->assertDatabaseHas('lectores_codigos_barras', [
            'id' => $lector->id,
            'usuario' => 'CARLOS RUIZ',
            'marca_referencia' => 'HONEYWELL',
        ]);

        $this->delete(route('lectores-codigos-barras.destroy', $lector))
            ->assertRedirect(route('lectores-codigos-barras.index'));

        $this->assertSoftDeleted($lector);
    }

    public function test_lector_actions_integrate_with_traslados_and_bajas(): void
    {
        $lector = LectorCodigoBarras::factory()->create([
            'usuario' => 'Laura Gómez',
            'serial' => 'SN-LCB-9',
            'codigo_contable' => 'CB-77',
        ]);

        $this->patch(route('lectores-codigos-barras.accion', $lector), [
            'campo' => 'en_disponible',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('lectores_codigos_barras', [
            'id' => $lector->id,
            'en_disponible' => 1,
        ]);

        $this->patch(route('lectores-codigos-barras.accion', $lector), [
            'campo' => 'en_traslado',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('SN-LCB-9')
            ->assertSee('Laura Gómez');

        $this->patch(route('lectores-codigos-barras.accion', $lector), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('SN-LCB-9')
            ->assertSee('Laura Gómez');
    }
}
