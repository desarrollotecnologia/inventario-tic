<?php

namespace Tests\Feature;

use App\Mail\BajasRealizadasMail;
use App\Models\Baja;
use App\Models\EquipoComputo;
use App\Models\Firma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BajaDescuentaInventarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_notified_baja_is_removed_from_inventory_listing(): void
    {
        Mail::fake();
        Storage::fake('public');

        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-DESCUENTO-1',
            'usuario' => 'Usuario Descuento',
            'en_baja' => true,
        ]);

        $baja = Baja::registrarDesde($equipo);
        $baja->update([
            'solicitante_nombre' => 'JUAN CARREÑO',
            'solicitante_cargo' => 'ANALISTA TIC',
            'motivo_baja' => 'Obsoleto',
            'completada_at' => now(),
        ]);

        $firma = Firma::query()->create([
            'nombre' => 'Área de Tecnología',
            'imagen' => UploadedFile::fake()->image('firma.png')->store('firmas', 'public'),
        ]);

        $this->get(route('equipos-computo.index'))
            ->assertOk()
            ->assertSee('EQ-DESCUENTO-1');

        $this->post(route('bajas.correo'), [
            'correos' => 'tecnologia@colbeef.com',
            'firma_id' => $firma->id,
        ])->assertRedirect(route('bajas.historial'));

        Mail::assertSent(BajasRealizadasMail::class);
        $this->assertSoftDeleted($equipo);
        $this->assertSame(0, EquipoComputo::query()->where('codigo', 'EQ-DESCUENTO-1')->count());

        $this->get(route('equipos-computo.index'))
            ->assertOk()
            ->assertDontSee('EQ-DESCUENTO-1');

        $this->get(route('bajas.historial'))
            ->assertOk()
            ->assertSee('EQ-DESCUENTO-1')
            ->assertSee('Obsoleto');
    }
}
