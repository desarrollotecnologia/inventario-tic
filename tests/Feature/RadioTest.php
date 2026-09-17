<?php

namespace Tests\Feature;

use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Radio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RadioTest extends TestCase
{
    use RefreshDatabase;

    public function test_radio_module_is_ready_and_shows_the_form_fields(): void
    {
        $this->get(route('modules.relacion'))
            ->assertOk()
            ->assertSee('/relacion-inventario-sistemas/radios-de-comunicacion', false)
            ->assertSee('Inventario de radios, antenas, amplificadores y filtros.');

        $this->get(route('radios.index'))
            ->assertOk()
            ->assertSee('Radios de Comunicación')
            ->assertSee('Registrar radio')
            ->assertSee('Aún no hay radios de comunicación registrados.')
            ->assertDontSee('El formulario se habilitará cuando se definan sus campos')
            ->assertDontSee('Próximamente');

        $this->get(route('radios.create'))
            ->assertOk()
            ->assertSee('Registrar radio de comunicación')
            ->assertSee('Departamento')
            ->assertSee('Área')
            ->assertSee('Usuario')
            ->assertSee('Cargo')
            ->assertSee('Tipo')
            ->assertSee('Elige el tipo')
            ->assertSee('>Radio</option>', false)
            ->assertSee('>Antena</option>', false)
            ->assertSee('>Amplificador</option>', false)
            ->assertSee('>Filtro</option>', false)
            ->assertSee('Marca y Referencia')
            ->assertSee('Modelo')
            ->assertSee('Serial')
            ->assertSee('Fecha de adquisición')
            ->assertSee('Antigüedad')
            ->assertSee('Se calcula automáticamente con la fecha de adquisición.')
            ->assertSee('Observaciones')
            ->assertSee('Motorola')
            ->assertSee('Kenwood');
    }

    public function test_a_radio_can_be_registered_updated_and_deleted(): void
    {
        $this->post(route('radios.store'), [
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista TIC',
            'tipo' => 'Radio',
            'marca_referencia' => 'Motorola',
            'modelo' => 'DP4800',
            'serial' => 'SN-RAD-001',
            'fecha_adquisicion' => '2024-03-10',
            'observaciones' => 'Base de operaciones',
        ])->assertRedirect(route('radios.index'));

        $this->assertDatabaseHas('radios_comunicacion', [
            'item' => '1',
            'usuario' => 'ANA PÉREZ',
            'cargo' => 'ANALISTA TIC',
            'tipo' => 'Radio',
            'marca_referencia' => 'MOTOROLA',
            'modelo' => 'DP4800',
            'serial' => 'SN-RAD-001',
        ]);

        $radio = Radio::query()->firstOrFail();
        $this->assertSame('2024-03-10', $radio->fecha_adquisicion?->toDateString());

        $this->get(route('radios.index'))
            ->assertOk()
            ->assertSee('1 registro')
            ->assertSee('ANA PÉREZ')
            ->assertSee('MOTOROLA')
            ->assertSee('DP4800')
            ->assertSee('SN-RAD-001')
            ->assertSee('Fecha de adquisición')
            ->assertSee('10/03/2024');

        $this->put(route('radios.update', $radio), [
            'departamento' => 'OPERACIONES',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'tipo' => 'Antena',
            'marca_referencia' => 'Kenwood',
            'modelo' => 'NX-5000',
            'serial' => 'SN-RAD-001',
            'fecha_adquisicion' => '2024-03-10',
        ])->assertRedirect(route('radios.index'));

        $this->assertDatabaseHas('radios_comunicacion', [
            'id' => $radio->id,
            'usuario' => 'CARLOS RUIZ',
            'tipo' => 'Antena',
            'marca_referencia' => 'KENWOOD',
        ]);

        $this->delete(route('radios.destroy', $radio))
            ->assertRedirect(route('radios.index'));

        $this->assertDatabaseMissing('radios_comunicacion', ['id' => $radio->id]);
    }

    public function test_radios_can_be_filtered_by_tipo(): void
    {
        Radio::factory()->create([
            'tipo' => 'Radio',
            'usuario' => 'Laura Gómez',
        ]);
        Radio::factory()->create([
            'tipo' => 'Filtro',
            'usuario' => 'Pedro Díaz',
        ]);

        $this->get(route('radios.index', ['tipo' => 'Filtro']))
            ->assertOk()
            ->assertSee('Pedro Díaz')
            ->assertDontSee('Laura Gómez')
            ->assertSee('1 de 2 registros');
    }

    public function test_radio_actions_match_other_inventory_modules(): void
    {
        $radio = Radio::factory()->create([
            'usuario' => 'Laura Gómez',
            'serial' => 'SN-ANT-9',
        ]);

        $this->patch(route('radios.accion', $radio), [
            'campo' => 'en_disponible',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('radios_comunicacion', [
            'id' => $radio->id,
            'en_disponible' => 1,
            'usuario' => 'Laura Gómez',
        ]);

        $this->get(route('radios.index'))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('Solo disponibles');

        $this->patch(route('radios.accion', $radio), [
            'campo' => 'en_traslado',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('radios.index'))
            ->assertOk()
            ->assertSee('Alistar Traslados');

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('SN-ANT-9')
            ->assertSee('Laura Gómez');

        $this->patch(route('radios.accion', $radio), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('SN-ANT-9')
            ->assertSee('Laura Gómez');
    }

    public function test_radio_user_and_cargo_fields_list_inventory_values(): void
    {
        EquipoComputo::factory()->create(['usuario' => 'Laura Gómez', 'cargo' => 'Analista TIC']);
        Celular::factory()->create(['usuario' => 'Pedro Díaz', 'cargo' => 'Supervisor']);

        $this->get(route('radios.create'))
            ->assertOk()
            ->assertSee('Escribe o elige un usuario')
            ->assertSee('Escribe o elige un cargo')
            ->assertSee('Laura Gómez')
            ->assertSee('Pedro Díaz')
            ->assertSee('Analista TIC')
            ->assertSee('Supervisor')
            ->assertSee('Nuevo');
    }

    public function test_radio_age_is_calculated_from_acquisition_date(): void
    {
        $radio = Radio::factory()->create([
            'fecha_adquisicion' => now()->subYears(1)->subMonths(2)->subDays(4)->toDateString(),
        ]);

        $this->assertSame('1 año, 2 meses, 4 días', $radio->fresh()->antiguedad());

        $this->get(route('radios.index'))
            ->assertOk()
            ->assertSee('Antigüedad')
            ->assertSee('1 año, 2 meses, 4 días');
    }
}
