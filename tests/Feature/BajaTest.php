<?php

namespace Tests\Feature;

use App\Mail\BajasRealizadasMail;
use App\Models\Baja;
use App\Models\BajaSolicitante;
use App\Models\EquipoComputo;
use App\Models\Firma;
use App\Support\BajaAdjuntoNombres;
use App\Support\BajaMensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class BajaTest extends TestCase
{
    use RefreshDatabase;

    public function test_bajas_module_lists_pending_actas(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-BAJA-1',
            'usuario' => 'Ana Baja',
            'en_baja' => false,
        ]);

        $this->patch(route('equipos-computo.accion', $equipo), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('Historial de Bajas')
            ->assertSee('Completar acta')
            ->assertSee('EQ-BAJA-1')
            ->assertSee('Ana Baja');
    }

    public function test_marking_equipment_as_baja_creates_pending_draft(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-BAJA-2',
            'usuario' => 'Carlos Baja',
            'descripcion' => 'PORTATIL',
            'marca_referencia' => 'Dell Latitude',
            'service_tag_product_name' => 'SN-999',
            'en_baja' => false,
        ]);

        $this->patch(route('equipos-computo.accion', $equipo), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->assertTrue($equipo->fresh()->en_baja);
        $this->assertDatabaseHas('bajas', [
            'equipo_computo_id' => $equipo->id,
            'codigo' => 'EQ-BAJA-2',
            'usuario' => 'Carlos Baja',
            'categoria' => 'Equipo de cómputo',
            'completada_at' => null,
        ]);
    }

    public function test_unmarking_pending_baja_removes_draft(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-BAJA-3',
            'en_baja' => false,
        ]);

        $this->patch(route('equipos-computo.accion', $equipo), [
            'campo' => 'en_baja',
            'valor' => '1',
        ])->assertRedirect();

        $this->patch(route('equipos-computo.accion', $equipo), [
            'campo' => 'en_baja',
            'valor' => '0',
        ])->assertRedirect();

        $this->assertFalse($equipo->fresh()->en_baja);
        $this->assertDatabaseMissing('bajas', [
            'equipo_computo_id' => $equipo->id,
        ]);
    }

    public function test_completing_acta_saves_form_fields_and_continues_to_next_item(): void
    {
        Storage::fake('public');

        $primero = EquipoComputo::factory()->create([
            'codigo' => 'EQ-ACTA-1',
            'usuario' => 'Solicitante Uno',
            'cargo' => 'Analista',
            'departamento' => 'SISTEMAS',
            'en_baja' => false,
        ]);
        $segundo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-ACTA-2',
            'en_baja' => false,
        ]);

        Baja::registrarDesde($primero);
        Baja::registrarDesde($segundo);

        $baja = Baja::pendienteDe($primero);
        $this->assertNotNull($baja);

        $this->get(route('bajas.edit', $baja))
            ->assertOk()
            ->assertSee('Acta de Baja')
            ->assertSee('Motivo de la baja')
            ->assertSee('Observaciones')
            ->assertSee('EQ-ACTA-1');

        $this->put(route('bajas.update', $baja), [
            'solicitante_nombre' => 'JUAN CARREÑO',
            'solicitante_cargo' => 'ANALISTA TIC',
            'informacion' => 'Se solicita dar de baja al siguiente activo: Portátil EQ-ACTA-1',
            'motivo_baja' => 'El equipo superó su vida útil y el costo de reparación no es viable.',
            'observaciones' => '(11/08/2020) Cambio de disco duro. (30/07/2022) Dado de baja',
            'adjunto_acta' => UploadedFile::fake()->create('acta.pdf', 120, 'application/pdf'),
            'adjunto_evidencia' => [
                UploadedFile::fake()->image('evidencia-1.jpg'),
                UploadedFile::fake()->image('evidencia-2.jpg'),
            ],
        ])->assertRedirect();

        $baja->refresh();

        $this->assertNotNull($baja->completada_at);
        $this->assertSame('1', $baja->acta_numero);
        $this->assertSame(now()->toDateString(), $baja->fecha?->toDateString());
        $this->assertSame('JUAN CARREÑO', $baja->solicitante_nombre);
        $this->assertSame('ANALISTA TIC', $baja->solicitante_cargo);
        $this->assertSame('ADMINISTRATIVO Y FINANCIERO', $baja->solicitante_departamento);
        $this->assertSame('El equipo superó su vida útil y el costo de reparación no es viable.', $baja->motivo_baja);
        $this->assertSame('(11/08/2020) Cambio de disco duro. (30/07/2022) Dado de baja', $baja->observaciones);
        Storage::disk('public')->assertExists($baja->adjunto_acta);
        $this->assertCount(2, $baja->rutasEvidencia());
        foreach ($baja->rutasEvidencia() as $ruta) {
            Storage::disk('public')->assertExists($ruta);
        }

        $this->get(route('bajas.formato', $baja))
            ->assertOk()
            ->assertSee('ACTA DE BAJA')
            ->assertSee('INVENTARIOS')
            ->assertSee('JUAN CARREÑO')
            ->assertSee('Motivo de la Baja')
            ->assertSee('Firma quien Solicita la baja');

        $siguiente = Baja::pendienteDe($segundo);
        $this->assertNotNull($siguiente);
        $this->assertTrue($siguiente->is(Baja::query()->pendientes()->first()));

        $primero->update(['en_baja' => true]);
        $segundo->update(['en_baja' => true]);

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('EQ-ACTA-1')
            ->assertSee('Lista para enviar')
            ->assertSee('Enviar bajas')
            ->assertSee('EQ-ACTA-2')
            ->assertSee('Completar acta');

        $this->get(route('bajas.historial'))
            ->assertOk()
            ->assertDontSee('EQ-ACTA-1');
    }

    public function test_historial_lists_only_emailed_bajas(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-HIST-1',
            'usuario' => 'Historial Usuario',
            'en_baja' => false,
        ]);

        $baja = Baja::registrarDesde($equipo);
        $baja->update([
            'solicitante_nombre' => 'Historial Usuario',
            'solicitante_cargo' => 'Analista',
            'solicitante_departamento' => 'Sistemas',
            'informacion' => 'Se solicita dar de baja al siguiente activo: EQ-HIST-1',
            'motivo_baja' => 'Obsoleto',
            'completada_at' => now(),
            'correo_enviado_at' => now(),
        ]);

        $listas = Baja::registrarDesde(EquipoComputo::factory()->create([
            'codigo' => 'EQ-LISTA-1',
            'usuario' => 'Lista Usuario',
            'en_baja' => true,
        ]));
        $listas->update([
            'motivo_baja' => 'Pendiente de correo',
            'completada_at' => now(),
        ]);

        Baja::registrarDesde(EquipoComputo::factory()->create([
            'codigo' => 'EQ-PEND-1',
            'en_baja' => true,
        ]));

        $this->get(route('bajas.historial'))
            ->assertOk()
            ->assertSee('EQ-HIST-1')
            ->assertSee('Historial Usuario')
            ->assertSee('Obsoleto')
            ->assertSee('data-expand-row', false)
            ->assertSee('detalle-baja-'.$baja->id, false)
            ->assertSee('Se solicita dar de baja al siguiente activo: EQ-HIST-1')
            ->assertDontSee('EQ-PEND-1')
            ->assertDontSee('EQ-LISTA-1');
    }

    public function test_completed_bajas_can_be_emailed_and_move_to_historial(): void
    {
        Mail::fake();
        Storage::fake('public');

        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-MAIL-1',
            'usuario' => 'Usuario Mail',
            'descripcion' => 'PORTATIL',
            'marca_referencia' => 'HP 240',
            'area' => 'TECNOLOGIA',
            'en_baja' => true,
        ]);

        $baja = Baja::registrarDesde($equipo);
        $baja->update([
            'solicitante_nombre' => 'JUAN CARREÑO',
            'solicitante_cargo' => 'ANALISTA TIC',
            'motivo_baja' => 'PANTALLA PARTIDA',
            'observaciones' => 'CADUCIDAD DE VIDA UTIL',
            'adjunto_evidencia' => [
                UploadedFile::fake()->image('evidencia-a.jpg')->store('bajas', 'public'),
                UploadedFile::fake()->image('evidencia-b.jpg')->store('bajas', 'public'),
            ],
            'completada_at' => now(),
        ]);

        $firma = Firma::query()->create([
            'nombre' => 'Área de Tecnología',
            'imagen' => UploadedFile::fake()->image('firma.png')->store('firmas', 'public'),
        ]);

        $this->from(route('modules.bajas'))
            ->post(route('bajas.correo'), [
                'correos' => 'no-es-correo',
            ])
            ->assertRedirect(route('modules.bajas'))
            ->assertSessionHasErrors();

        $this->post(route('bajas.correo'), [
            'correos' => 'contabilidad@colbeef.com, inventarios@colbeef.com',
            'firma_id' => $firma->id,
        ])->assertRedirect(route('bajas.historial'));

        Mail::assertSent(BajasRealizadasMail::class, function (BajasRealizadasMail $mail) use ($firma): bool {
            $html = $mail->render();

            $this->assertStringContainsString('Cordial saludo para todos.', $html);
            $this->assertStringContainsString('BAJAR DEL INVENTARIO EQUIPOS DE CÓMPUTO', $html);
            $this->assertStringContainsString('ITEM', $html);
            $this->assertStringContainsString('MOTIVO', $html);
            $this->assertStringContainsString('PANTALLA PARTIDA', $html);
            $this->assertStringContainsString('HP 240', $html);
            $this->assertStringContainsString('overflow-x:auto', $html);
            $this->assertCount(3, $mail->attachments());

            $bajaMail = $mail->bajas->first();
            $nombres = collect($mail->attachments())->map(fn ($adjunto) => $adjunto->as)->all();

            $this->assertSame(BajaAdjuntoNombres::acta($bajaMail), $nombres[0] ?? null);
            $this->assertSame('Acta_Baja_HP 240.pdf', $nombres[0] ?? null);
            $this->assertSame('HP 240_evidencia.jpg', $nombres[1] ?? null);
            $this->assertSame('HP 240_evidencia_2.jpg', $nombres[2] ?? null);

            $acta = $mail->attachments()[0] ?? null;
            $actaPdf = $acta?->attachWith(
                fn (): string => '',
                fn ($data): string => is_callable($data) ? $data() : (string) $data,
            );
            $this->assertIsString($actaPdf);
            $this->assertStringStartsWith('%PDF', $actaPdf);

            $this->assertSame(BajaMensaje::asunto(), $mail->envelope()->subject);
            $this->assertStringStartsWith('Baja de equipos de computo ', BajaMensaje::asunto());

            return $mail->firma->is($firma)
                && $mail->hasTo('contabilidad@colbeef.com')
                && $mail->hasTo('inventarios@colbeef.com');
        });

        $this->assertNotNull($baja->fresh()->correo_enviado_at);
        $this->assertSoftDeleted($equipo);

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertDontSee('EQ-MAIL-1');

        $this->get(route('equipos-computo.index'))
            ->assertOk()
            ->assertDontSee('EQ-MAIL-1');

        $this->get(route('bajas.historial'))
            ->assertOk()
            ->assertSee('EQ-MAIL-1')
            ->assertSee('PANTALLA PARTIDA');
    }

    public function test_mail_transport_failure_keeps_bajas_in_module(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->andReturnSelf();
        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new TransportException('Connection could not be established'));

        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-SMTP-1',
            'en_baja' => true,
        ]);

        $baja = Baja::registrarDesde($equipo);
        $baja->update([
            'motivo_baja' => 'Falla SMTP',
            'completada_at' => now(),
        ]);

        $firma = Firma::query()->create([
            'nombre' => 'Firma SMTP',
            'contenido' => 'Firma SMTP',
            'imagen' => 'firmas/smtp.png',
        ]);

        $this->from(route('modules.bajas'))
            ->post(route('bajas.correo'), [
                'correos' => 'tecnologia@colbeef.com',
                'firma_id' => $firma->id,
            ])
            ->assertRedirect(route('modules.bajas'))
            ->assertSessionHas('status');

        $this->assertNull($baja->fresh()->correo_enviado_at);
        $this->assertTrue($equipo->fresh()->en_baja);
    }

    public function test_solicitante_catalog_can_add_and_remove_names(): void
    {
        $equipo = EquipoComputo::factory()->create(['en_baja' => false]);
        $baja = Baja::registrarDesde($equipo);

        $this->post(route('bajas.solicitantes.store'), [
            'nombre' => 'Maria Lopez',
            'baja_id' => $baja->id,
        ])->assertRedirect(route('bajas.edit', $baja));

        $this->assertDatabaseHas('baja_solicitantes', [
            'nombre' => 'MARIA LOPEZ',
        ]);

        $solicitante = BajaSolicitante::query()->where('nombre', 'MARIA LOPEZ')->firstOrFail();

        $this->delete(route('bajas.solicitantes.destroy', $solicitante), [
            'baja_id' => $baja->id,
        ])->assertRedirect(route('bajas.edit', $baja));

        $this->assertDatabaseMissing('baja_solicitantes', [
            'nombre' => 'MARIA LOPEZ',
        ]);
    }

    public function test_index_syncs_already_marked_baja_assets_into_pending_actas(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'codigo' => 'EQ-YA-MARCADO',
            'usuario' => 'Equipo Previo',
            'en_baja' => true,
        ]);

        Baja::query()->create([
            'equipo_computo_id' => $equipo->id,
            'fecha' => now()->toDateString(),
            'codigo' => 'EQ-YA-MARCADO',
            'categoria' => 'Equipo de cómputo',
            'usuario' => 'Equipo Previo',
            'informacion' => 'Se solicita dar de baja al siguiente activo: EQ-YA-MARCADO',
            'motivo_baja' => 'Registro histórico previo al acta digital.',
            'completada_at' => now(),
        ]);

        $this->get(route('modules.bajas'))
            ->assertOk()
            ->assertSee('EQ-YA-MARCADO')
            ->assertSee('Equipo Previo')
            ->assertSee('Completar acta');

        $this->assertDatabaseHas('bajas', [
            'equipo_computo_id' => $equipo->id,
            'codigo' => 'EQ-YA-MARCADO',
            'completada_at' => null,
        ]);
    }
}
