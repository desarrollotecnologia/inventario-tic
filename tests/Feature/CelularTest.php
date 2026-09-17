<?php

namespace Tests\Feature;

use App\Models\Celular;
use App\Models\EquipoComputo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CelularTest extends TestCase
{
    use RefreshDatabase;

    public function test_celular_module_is_ready_and_shows_the_form_fields(): void
    {
        $this->get(route('modules.relacion'))
            ->assertOk()
            ->assertSee('/relacion-inventario-sistemas/celulares', false);

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('Celulares y Tablets')
            ->assertSee('Registrar celular o tablet')
            ->assertSee('Aún no hay celulares ni tablets registrados.')
            ->assertDontSee('El formulario se habilitará cuando se definan sus campos');

        $this->get(route('celulares.create'))
            ->assertOk()
            ->assertSee('Registrar celular o tablet')
            ->assertSee('Departamento')
            ->assertSee('Área')
            ->assertSee('Usuario')
            ->assertSee('Cargo')
            ->assertSee('Tipo')
            ->assertSee('Elige celular o tablet')
            ->assertSee('>Celular</option>', false)
            ->assertSee('>Tablet</option>', false)
            ->assertSee('Marca')
            ->assertSee('Modelo')
            ->assertSee('Almacenamiento')
            ->assertSee('RAM')
            ->assertSee('RAM expansiva')
            ->assertSee('Serial')
            ->assertSee('IMEI 1')
            ->assertSee('IMEI 2')
            ->assertSee('Número celular')
            ->assertSee('Dirección MAC')
            ->assertSee('Fecha de entrega')
            ->assertSee('Antigüedad')
            ->assertSee('Observaciones')
            ->assertSee('Fecha garantía');
    }

    public function test_celular_user_field_lists_inventory_users_and_allows_a_new_one(): void
    {
        EquipoComputo::factory()->create(['usuario' => 'Laura Gómez']);
        Celular::factory()->create(['usuario' => 'Pedro Díaz']);

        $this->get(route('celulares.create'))
            ->assertOk()
            ->assertSee('Escribe o elige un usuario')
            ->assertSee('Laura Gómez')
            ->assertSee('Pedro Díaz')
            ->assertSee('Nuevo');
    }

    public function test_celular_cargo_field_lists_inventory_cargos_and_allows_a_new_one(): void
    {
        EquipoComputo::factory()->create(['cargo' => 'Analista TIC']);
        Celular::factory()->create(['cargo' => 'Supervisor de Planta']);

        $this->get(route('celulares.create'))
            ->assertOk()
            ->assertSee('Escribe o elige un cargo')
            ->assertSee('Analista TIC')
            ->assertSee('Supervisor de Planta')
            ->assertSee('Nuevo');
    }

    public function test_celular_model_options_depend_on_the_selected_brand(): void
    {
        Celular::factory()->create([
            'marca' => 'Samsung',
            'modelo' => 'Galaxy Corporativo X',
        ]);

        $this->get(route('celulares.create'))
            ->assertOk()
            ->assertSee('Elige una marca y luego el modelo')
            ->assertSee('data-filtra-por-marca="marca"', false)
            ->assertSee('data-marca="Samsung"', false)
            ->assertSee('data-marca="Apple"', false)
            ->assertSee('Galaxy A54')
            ->assertSee('iPhone 16')
            ->assertSee('Galaxy Corporativo X')
            ->assertSee('Elige una marca para ver los modelos.');
    }

    public function test_a_celular_can_be_registered_updated_and_deleted(): void
    {
        $this->post(route('celulares.store'), [
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista TIC',
            'tipo' => 'Celular',
            'marca' => 'Samsung',
            'modelo' => 'Galaxy A54',
            'almacenamiento' => '128 GB',
            'ram' => '8 GB',
            'ram_expansiva' => 'Sí',
            'serial' => 'SN-CEL-001',
            'imei_1' => '123456789012345',
            'imei_2' => '123456789012346',
            'numero_celular' => '3001234567',
            'direccion_mac' => 'AA:BB:CC:DD:EE:FF',
            'fecha_entrega' => '2026-01-15',
            'observaciones' => 'Línea corporativa',
            'fecha_garantia' => '2027-01-15',
        ])->assertRedirect(route('celulares.index'));

        $this->assertDatabaseHas('celulares', [
            'item' => '1',
            'usuario' => 'ANA PÉREZ',
            'cargo' => 'ANALISTA TIC',
            'tipo' => 'Celular',
            'marca' => 'SAMSUNG',
            'almacenamiento' => '128 GB',
            'ram' => '8 GB',
            'ram_expansiva' => 'SÍ',
            'imei_1' => '123456789012345',
            'numero_celular' => '3001234567',
        ]);

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('1 registro')
            ->assertSee('ANA PÉREZ')
            ->assertSee('ANALISTA TIC')
            ->assertSee('Celular')
            ->assertSee('GALAXY A54')
            ->assertSee('123456789012345')
            ->assertSee('3001234567');

        $celular = Celular::query()->firstOrFail();

        $this->put(route('celulares.update', $celular), [
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'tipo' => 'Tablet',
            'marca' => 'Samsung',
            'modelo' => 'Galaxy A54',
            'serial' => 'SN-CEL-001',
            'imei_1' => '123456789012345',
            'numero_celular' => '3009998877',
        ])->assertRedirect(route('celulares.index'));

        $this->assertDatabaseHas('celulares', [
            'id' => $celular->id,
            'usuario' => 'CARLOS RUIZ',
            'cargo' => 'COORDINADOR',
            'tipo' => 'Tablet',
            'numero_celular' => '3009998877',
        ]);

        $this->delete(route('celulares.destroy', $celular))
            ->assertRedirect(route('celulares.index'));

        $this->assertDatabaseMissing('celulares', ['id' => $celular->id]);
    }

    public function test_celulares_can_be_filtered_by_imei(): void
    {
        Celular::factory()->create([
            'imei_1' => '111111111111111',
            'usuario' => 'Laura Gómez',
        ]);
        Celular::factory()->create([
            'imei_1' => '222222222222222',
            'usuario' => 'Pedro Díaz',
        ]);

        $this->get(route('celulares.index', ['imei' => '111111111111111']))
            ->assertOk()
            ->assertSee('Laura Gómez')
            ->assertDontSee('Pedro Díaz')
            ->assertSee('1 de 2 registros');
    }

    public function test_celulares_can_be_filtered_by_tipo(): void
    {
        Celular::factory()->create([
            'tipo' => 'Celular',
            'usuario' => 'Laura Gómez',
        ]);
        Celular::factory()->create([
            'tipo' => 'Tablet',
            'usuario' => 'Pedro Díaz',
        ]);

        $this->get(route('celulares.index', ['tipo' => 'Tablet']))
            ->assertOk()
            ->assertSee('Pedro Díaz')
            ->assertDontSee('Laura Gómez')
            ->assertSee('1 de 2 registros');
    }

    public function test_celular_age_is_calculated_from_delivery_date(): void
    {
        $celular = Celular::factory()->create([
            'fecha_entrega' => now()->subYears(1)->subMonths(2)->subDays(4)->toDateString(),
        ]);

        $this->assertSame('1 año, 2 meses, 4 días', $celular->fresh()->antiguedad());

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('Antigüedad')
            ->assertSee('1 año, 2 meses, 4 días');

        $this->get(route('celulares.create'))
            ->assertOk()
            ->assertSee('Se calcula automáticamente con la fecha de entrega.');
    }

    public function test_celular_actions_match_computer_equipment(): void
    {
        $celular = Celular::factory()->create([
            'usuario' => 'Laura Gómez',
            'serial' => 'SN-TAB-9',
        ]);

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('title="Disponible"', false)
            ->assertSee('title="Traslado"', false)
            ->assertSee('title="Baja"', false)
            ->assertDontSee('Alistar Traslados')
            ->assertDontSee('Alistar Bajas');

        $this->patch(route('celulares.accion', $celular), [
            'campo' => 'en_disponible',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('celulares', [
            'id' => $celular->id,
            'en_disponible' => 1,
            'usuario' => 'Laura Gómez',
        ]);

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('bg-sky-100', false)
            ->assertSee('line-through text-slate-400', false)
            ->assertSee('Solo disponibles');

        $this->get(route('celulares.index', ['disponible' => '1']))
            ->assertOk()
            ->assertSee('Laura Gómez');

        $this->patch(route('celulares.accion', $celular), [
            'campo' => 'en_traslado',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('Alistar Traslados')
            ->assertDontSee('Alistar Bajas');

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('SN-TAB-9')
            ->assertSee('Laura Gómez');

        $this->patch(route('celulares.accion', $celular), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('celulares.index'))
            ->assertOk()
            ->assertSee('Alistar Traslados')
            ->assertSee('Alistar Bajas');

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('SN-TAB-9')
            ->assertSee('Laura Gómez');
    }
}
