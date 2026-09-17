<?php

namespace Tests\Feature;

use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Impresora;
use App\Models\Radio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpresoraTest extends TestCase
{
    use RefreshDatabase;

    public function test_printer_module_is_ready_and_shows_the_form_fields(): void
    {
        $this->get(route('modules.relacion'))
            ->assertOk()
            ->assertSee('/relacion-inventario-sistemas/impresoras', false)
            ->assertSee('Inventario de impresoras y multifuncionales.')
            ->assertDontSee('Próximamente');

        $this->get(route('impresoras.index'))
            ->assertOk()
            ->assertSee('Impresoras')
            ->assertSee('Registrar impresora')
            ->assertSee('Aún no hay impresoras registradas.')
            ->assertDontSee('El formulario se habilitará cuando se definan sus campos')
            ->assertDontSee('Próximamente');

        $this->get(route('impresoras.create'))
            ->assertOk()
            ->assertSee('Registrar impresora')
            ->assertSee('Departamento')
            ->assertSee('Área')
            ->assertSee('Usuario')
            ->assertSee('Cargo')
            ->assertSee('Descripción')
            ->assertSee('Modelo')
            ->assertSee('Serial')
            ->assertSee('Tipo')
            ->assertSee('Escribe o elige un tipo')
            ->assertSee('Láser')
            ->assertSee('Transferencia térmica')
            ->assertSee('Inyección')
            ->assertSee('Cinta')
            ->assertDontSee('Elige el tipo')
            ->assertSee('Observaciones')
            ->assertSee('HP')
            ->assertSee('Epson');
    }

    public function test_a_printer_can_be_registered_updated_and_deleted(): void
    {
        $this->post(route('impresoras.store'), [
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista TIC',
            'descripcion' => 'HP LaserJet',
            'modelo' => 'M404dn',
            'serial' => 'SN-IMP-001',
            'tipo' => 'a laser',
            'observaciones' => 'Oficina principal',
        ])->assertRedirect(route('impresoras.index'));

        $this->assertDatabaseHas('impresoras', [
            'item' => '1',
            'usuario' => 'ANA PÉREZ',
            'cargo' => 'ANALISTA TIC',
            'descripcion' => 'HP LASERJET',
            'modelo' => 'M404DN',
            'serial' => 'SN-IMP-001',
            'tipo' => 'A LASER',
        ]);

        $this->get(route('impresoras.index'))
            ->assertOk()
            ->assertSee('1 registro')
            ->assertSee('ANA PÉREZ')
            ->assertSee('HP LASERJET')
            ->assertSee('M404DN')
            ->assertSee('SN-IMP-001');

        $impresora = Impresora::query()->firstOrFail();

        $this->put(route('impresoras.update', $impresora), [
            'departamento' => 'OPERACIONES',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'descripcion' => 'Epson EcoTank',
            'modelo' => 'L3250',
            'serial' => 'SN-IMP-001',
            'tipo' => 'Transferencia térmica',
        ])->assertRedirect(route('impresoras.index'));

        $this->assertDatabaseHas('impresoras', [
            'id' => $impresora->id,
            'usuario' => 'CARLOS RUIZ',
            'tipo' => 'TRANSFERENCIA TÉRMICA',
            'descripcion' => 'EPSON ECOTANK',
            'modelo' => 'L3250',
        ]);

        $this->delete(route('impresoras.destroy', $impresora))
            ->assertRedirect(route('impresoras.index'));

        $this->assertDatabaseMissing('impresoras', ['id' => $impresora->id]);
    }

    public function test_printers_can_be_filtered_by_tipo(): void
    {
        Impresora::factory()->create([
            'tipo' => 'Láser',
            'usuario' => 'Laura Gómez',
        ]);
        Impresora::factory()->create([
            'tipo' => 'Cinta',
            'usuario' => 'Pedro Díaz',
        ]);

        $this->get(route('impresoras.index', ['tipo' => 'Cinta']))
            ->assertOk()
            ->assertSee('Pedro Díaz')
            ->assertDontSee('Laura Gómez')
            ->assertSee('1 de 2 registros');
    }

    public function test_printer_actions_match_other_inventory_modules(): void
    {
        $impresora = Impresora::factory()->create([
            'usuario' => 'Laura Gómez',
            'serial' => 'SN-PLO-9',
        ]);

        $this->patch(route('impresoras.accion', $impresora), [
            'campo' => 'en_disponible',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('impresoras', [
            'id' => $impresora->id,
            'en_disponible' => 1,
            'usuario' => 'Laura Gómez',
        ]);

        $this->get(route('impresoras.index'))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('Solo disponibles');

        $this->patch(route('impresoras.accion', $impresora), [
            'campo' => 'en_traslado',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('impresoras.index'))
            ->assertOk()
            ->assertSee('Alistar Traslados');

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('SN-PLO-9')
            ->assertSee('Laura Gómez');

        $this->patch(route('impresoras.accion', $impresora), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('SN-PLO-9')
            ->assertSee('Laura Gómez');
    }

    public function test_printer_user_and_cargo_fields_list_inventory_values(): void
    {
        EquipoComputo::factory()->create(['usuario' => 'Laura Gómez', 'cargo' => 'Analista TIC']);
        Celular::factory()->create(['usuario' => 'Pedro Díaz', 'cargo' => 'Supervisor']);
        Radio::factory()->create(['usuario' => 'Marta López', 'cargo' => 'Operario']);

        $this->get(route('impresoras.create'))
            ->assertOk()
            ->assertSee('Escribe o elige un usuario')
            ->assertSee('Escribe o elige un cargo')
            ->assertSee('Laura Gómez')
            ->assertSee('Pedro Díaz')
            ->assertSee('Marta López')
            ->assertSee('Analista TIC')
            ->assertSee('Supervisor')
            ->assertSee('Operario')
            ->assertSee('Nuevo');
    }
}
