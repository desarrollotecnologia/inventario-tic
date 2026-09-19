<?php

namespace Tests\Feature;

use App\Models\Biometrico;
use App\Models\Celular;
use App\Models\EquipoComputo;
use App\Models\Impresora;
use App\Models\Radio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiometricoTest extends TestCase
{
    use RefreshDatabase;

    public function test_biometric_module_is_ready_and_shows_the_form_fields(): void
    {
        $this->get(route('modules.relacion'))
            ->assertOk()
            ->assertSee('/relacion-inventario-sistemas/biometricos', false)
            ->assertSee('Inventario de lectores biométricos y control de acceso.')
            ->assertDontSee('Próximamente');

        $this->get(route('biometricos.index'))
            ->assertOk()
            ->assertSee('Biométricos')
            ->assertSee('Registrar biométrico')
            ->assertSee('Aún no hay biométricos registrados.')
            ->assertDontSee('El formulario se habilitará cuando se definan sus campos')
            ->assertDontSee('Próximamente');

        $this->get(route('biometricos.create'))
            ->assertOk()
            ->assertSee('Registrar biométrico')
            ->assertSee('Departamento')
            ->assertSee('Área')
            ->assertSee('Usuario')
            ->assertSee('Cargo')
            ->assertSee('Marca y Referencia')
            ->assertSee('Modelo')
            ->assertSee('Serial')
            ->assertSee('Fecha de adquisición')
            ->assertSee('Antigüedad')
            ->assertSee('Observaciones')
            ->assertSee('ZKTeco')
            ->assertSee('Hikvision');
    }

    public function test_a_biometric_can_be_registered_updated_and_deleted(): void
    {
        $this->post(route('biometricos.store'), [
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista TIC',
            'marca_referencia' => 'ZKTeco',
            'modelo' => 'MB360',
            'serial' => 'SN-BIO-001',
            'fecha_adquisicion' => '2024-03-10',
            'observaciones' => 'Portería principal',
        ])->assertRedirect(route('biometricos.index'));

        $this->assertDatabaseHas('biometricos', [
            'item' => '1',
            'usuario' => 'ANA PÉREZ',
            'cargo' => 'ANALISTA TIC',
            'marca_referencia' => 'ZKTECO',
            'modelo' => 'MB360',
            'serial' => 'SN-BIO-001',
        ]);

        $biometrico = Biometrico::query()->firstOrFail();
        $this->assertSame('2024-03-10', $biometrico->fecha_adquisicion?->toDateString());

        $this->get(route('biometricos.index'))
            ->assertOk()
            ->assertSee('1 registro')
            ->assertSee('ANA PÉREZ')
            ->assertSee('ZKTECO')
            ->assertSee('MB360')
            ->assertSee('SN-BIO-001');

        $this->put(route('biometricos.update', $biometrico), [
            'departamento' => 'OPERACIONES',
            'area' => 'TECNOLOGIA',
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'marca_referencia' => 'Hikvision',
            'modelo' => 'DS-K1T671M',
            'serial' => 'SN-BIO-001',
            'fecha_adquisicion' => '2024-03-10',
        ])->assertRedirect(route('biometricos.index'));

        $this->assertDatabaseHas('biometricos', [
            'id' => $biometrico->id,
            'usuario' => 'CARLOS RUIZ',
            'marca_referencia' => 'HIKVISION',
            'modelo' => 'DS-K1T671M',
        ]);

        $this->delete(route('biometricos.destroy', $biometrico))
            ->assertRedirect(route('biometricos.index'));

        $this->assertDatabaseMissing('biometricos', ['id' => $biometrico->id]);
    }

    public function test_biometrics_can_be_filtered_by_brand(): void
    {
        Biometrico::factory()->create([
            'marca_referencia' => 'ZKTeco',
            'usuario' => 'Laura Gómez',
        ]);
        Biometrico::factory()->create([
            'marca_referencia' => 'Hikvision',
            'usuario' => 'Pedro Díaz',
        ]);

        $this->get(route('biometricos.index', ['marca_referencia' => 'Hikvision']))
            ->assertOk()
            ->assertSee('Pedro Díaz')
            ->assertDontSee('Laura Gómez')
            ->assertSee('1 de 2 registros');
    }

    public function test_biometric_actions_match_other_inventory_modules(): void
    {
        $biometrico = Biometrico::factory()->create([
            'usuario' => 'Laura Gómez',
            'serial' => 'SN-BIO-9',
        ]);

        $this->patch(route('biometricos.accion', $biometrico), [
            'campo' => 'en_disponible',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('biometricos', [
            'id' => $biometrico->id,
            'en_disponible' => 1,
            'usuario' => 'Laura Gómez',
        ]);

        $this->get(route('biometricos.index'))
            ->assertOk()
            ->assertSee('Disponible')
            ->assertSee('Solo disponibles');

        $this->patch(route('biometricos.accion', $biometrico), [
            'campo' => 'en_traslado',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('biometricos.index'))
            ->assertOk()
            ->assertSee('Alistar Traslados');

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('SN-BIO-9')
            ->assertSee('Laura Gómez');

        $this->patch(route('biometricos.accion', $biometrico), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('SN-BIO-9')
            ->assertSee('Laura Gómez');
    }

    public function test_biometric_user_and_cargo_fields_list_inventory_values(): void
    {
        EquipoComputo::factory()->create(['usuario' => 'Laura Gómez', 'cargo' => 'Analista TIC']);
        Celular::factory()->create(['usuario' => 'Pedro Díaz', 'cargo' => 'Supervisor']);
        Radio::factory()->create(['usuario' => 'Marta López', 'cargo' => 'Operario']);
        Impresora::factory()->create(['usuario' => 'Julián Castro', 'cargo' => 'Auxiliar']);

        $this->get(route('biometricos.create'))
            ->assertOk()
            ->assertSee('Escribe o elige un usuario')
            ->assertSee('Escribe o elige un cargo')
            ->assertSee('Laura Gómez')
            ->assertSee('Pedro Díaz')
            ->assertSee('Marta López')
            ->assertSee('Julián Castro')
            ->assertSee('Analista TIC')
            ->assertSee('Supervisor')
            ->assertSee('Operario')
            ->assertSee('Auxiliar')
            ->assertSee('Nuevo');
    }

    public function test_biometric_age_is_calculated_from_acquisition_date(): void
    {
        $biometrico = Biometrico::factory()->create([
            'fecha_adquisicion' => now()->subYears(1)->subMonths(2)->subDays(4)->toDateString(),
        ]);

        $this->assertSame('1 año, 2 meses, 4 días', $biometrico->fresh()->antiguedad());

        $this->get(route('biometricos.index'))
            ->assertOk()
            ->assertSee('Antigüedad')
            ->assertSee('1 año, 2 meses, 4 días');
    }
}
