<?php

namespace Tests\Feature;

use App\Support\EquipoComputoFields;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoComputoOffice365Test extends TestCase
{
    use RefreshDatabase;

    public function test_office365_account_field_lists_catalog_and_allows_filtering(): void
    {
        $this->get(route('equipos-computo.create'))
            ->assertOk()
            ->assertSee('Cuenta Office365')
            ->assertSee('Escribe para filtrar o elige una cuenta')
            ->assertSee('tic@frigoriofrio365.onmicrosoft.com')
            ->assertSee('gerencia.general@frigoriofrio365.onmicrosoft.com')
            ->assertSee('aux.tic@frigoriofrio365.onmicrosoft.com')
            ->assertSee('Nuevo');

        $this->assertContains(
            'servicioalcliente@frigoriofrio365.onmicrosoft.com',
            EquipoComputoFields::cuentasOffice365Catalogo()
        );
    }
}
