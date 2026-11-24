<?php

namespace Tests\Feature;

use App\Models\ActaEntrega;
use App\Models\ActaSalida;
use App\Models\Celular;
use App\Support\ActaInventarioCatalogo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActasTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_muestra_el_modulo_de_actas(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Actas')
            ->assertSee(route('modules.actas'), false);
    }

    public function test_modulo_actas_lista_los_submodulos(): void
    {
        $this->get(route('modules.actas'))
            ->assertOk()
            ->assertSee('Acta de Entrega')
            ->assertSee('Acta de Salida')
            ->assertSee(route('actas.entrega'), false)
            ->assertSee(route('actas.salida'), false);
    }

    public function test_puede_crear_e_imprimir_acta_de_salida(): void
    {
        $payload = [
            'ciudad' => 'Floridablanca',
            'fecha' => '2024-02-07',
            'representante' => 'Leonardo Lara',
            'destino' => 'LINEA DATASCAN',
            'proposito' => 'para realizar diagnostico y reparación',
            'entregado_por_nombre' => 'Otro Nombre',
            'entregado_por_cargo' => 'Cargo distinto',
            'recibido_por_nombre' => 'Otro Receptor',
            'recibido_por_documento' => '',
            'equipos' => [[
                'cantidad' => 1,
                'descripcion' => 'CELULAR MOVIL',
                'marca' => 'MOTO G41',
                'modelo' => 'XT2167-1',
                'serial' => "CELULAR: ZY22GBLXMJ\nIMEI: 350077382282873",
                'observaciones' => "Novedades:\nMODULO DE PANTALLA DEFECTUOSO, NO DA IMAGEN",
            ]],
        ];

        $this->get(route('actas.salida.create'))
            ->assertOk()
            ->assertSee('Escribe para filtrar inventario')
            ->assertSee(ActaInventarioCatalogo::CARGO_ENTREGA);

        $this->post(route('actas.salida.store'), $payload)
            ->assertRedirect();

        $acta = ActaSalida::query()->firstOrFail();

        $this->assertSame('LINEA DATASCAN', $acta->destino);
        $this->assertSame('Leonardo Lara', $acta->entregado_por_nombre);
        $this->assertSame('LINEA DATASCAN', $acta->recibido_por_nombre);
        $this->assertSame(ActaInventarioCatalogo::CARGO_ENTREGA, $acta->entregado_por_cargo);

        $this->get(route('actas.salida.formato', $acta))
            ->assertOk()
            ->assertSee('ACTA DE SALIDA')
            ->assertSee('LINEA DATASCAN')
            ->assertSee('CELULAR MOVIL')
            ->assertSee('Floridablanca 07 Febrero 2024');
    }

    public function test_formulario_de_salida_incluye_inventario_para_autocompletar(): void
    {
        Celular::factory()->create([
            'tipo' => 'CELULAR MOVIL',
            'marca' => 'MOTOROLA',
            'modelo' => 'G41',
            'serial' => 'ZY22TEST',
            'imei_1' => '350077382282873',
        ]);

        $this->get(route('actas.salida.create'))
            ->assertOk()
            ->assertSee('CELULAR MOVIL', false)
            ->assertSee('MOTOROLA', false)
            ->assertSee('ZY22TEST', false);
    }

    public function test_si_el_inventario_no_tiene_modelo_usa_la_marca(): void
    {
        Celular::factory()->create([
            'tipo' => 'TABLET',
            'marca' => 'SAMSUNG TAB',
            'modelo' => null,
            'serial' => 'TAB-001',
        ]);

        $item = collect(ActaInventarioCatalogo::equipos())
            ->first(fn (array $equipo): bool => ($equipo['serial'] ?? '') === 'CELULAR: TAB-001' || str_contains((string) ($equipo['serial'] ?? ''), 'TAB-001'));

        $this->assertNotNull($item);
        $this->assertSame('SAMSUNG TAB', $item['marca']);
        $this->assertSame('SAMSUNG TAB', $item['modelo']);
    }

    public function test_puede_crear_e_imprimir_acta_de_entrega(): void
    {
        $payload = ActaEntrega::factory()->make()->toArray();

        $this->post(route('actas.entrega.store'), $payload)
            ->assertRedirect();

        $acta = ActaEntrega::query()->firstOrFail();

        $this->get(route('actas.entrega.formato', $acta))
            ->assertOk()
            ->assertSee('ACTA DE ENTREGA')
            ->assertSee($acta->destino)
            ->assertSee('PORTATIL');
    }

    public function test_validacion_exige_al_menos_un_equipo_en_salida(): void
    {
        $this->from(route('actas.salida.create'))
            ->post(route('actas.salida.store'), [
                'ciudad' => 'Floridablanca',
                'fecha' => now()->toDateString(),
                'representante' => 'Leonardo Lara',
                'destino' => 'LINEA DATASCAN',
                'proposito' => 'para realizar diagnostico y reparación',
                'entregado_por_nombre' => 'Felipe Suarez',
                'entregado_por_cargo' => 'Coordinador TIC',
                'recibido_por_nombre' => 'Técnico',
                'equipos' => [],
            ])
            ->assertRedirect(route('actas.salida.create'))
            ->assertSessionHasErrors('equipos');
    }
}
