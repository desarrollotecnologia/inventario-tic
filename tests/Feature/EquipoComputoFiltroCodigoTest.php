<?php

namespace Tests\Feature;

use App\Models\EquipoComputo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoComputoFiltroCodigoTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_can_filter_equipos_by_codigo(): void
    {
        EquipoComputo::factory()->create([
            'codigo' => '2010-20',
            'usuario' => 'Usuario A',
        ]);

        EquipoComputo::factory()->create([
            'codigo' => '3055-01',
            'usuario' => 'Usuario B',
        ]);

        $this->get(route('equipos-computo.index', ['codigo' => '2010-20']))
            ->assertOk()
            ->assertSee('name="codigo"', false)
            ->assertSee('2010-20')
            ->assertSee('Usuario A')
            ->assertDontSee('3055-01')
            ->assertDontSee('Usuario B');
    }
}
