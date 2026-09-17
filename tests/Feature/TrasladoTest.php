<?php

namespace Tests\Feature;

use App\Mail\TrasladosRealizadosMail;
use App\Models\EquipoComputo;
use App\Models\Firma;
use App\Models\Traslado;
use App\Support\TrasladoExcel;
use App\Support\TrasladoMensaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class TrasladoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_transfers_show_the_requested_columns(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'item' => '88',
            'codigo' => 'EQ-4004',
            'usuario' => 'Ana Pérez',
            'area' => 'TECNOLOGIA',
            'descripcion' => 'PORTATIL',
            'marca_referencia' => 'Dell Latitude',
            'service_tag_product_name' => 'ABC123',
            'en_traslado' => true,
        ]);

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('Ítem')
            ->assertSee('Fecha')
            ->assertSee('Usuario Entrega')
            ->assertSee('Código Contable')
            ->assertSee('Área')
            ->assertSee('Tipo')
            ->assertSee('Descripción del equipo')
            ->assertSee('Serial')
            ->assertSee('Usuario Recibe')
            ->assertSee('Área Recibe')
            ->assertSee('Observaciones')
            ->assertSee('EQ-4004')
            ->assertSee('Ana Pérez')
            ->assertSee('TECNOLOGIA')
            ->assertSee('PORTATIL')
            ->assertSee('Dell Latitude')
            ->assertSee('ABC123')
            ->assertSee('>1<', false)
            ->assertDontSee('>88<', false)
            ->assertSee('id="traslados-pendientes"', false)
            ->assertSee('Aplicar')
            ->assertSee('Historial de Traslados');

        $this->assertSame(
            1,
            substr_count($this->get(route('modules.traslados'))->getContent(), 'id="traslados-pendientes"')
        );
    }

    public function test_transfer_item_counts_listed_equipment_instead_of_inventory_number(): void
    {
        EquipoComputo::factory()->create([
            'item' => '40',
            'codigo' => 'EQ-A40',
            'en_traslado' => true,
        ]);
        EquipoComputo::factory()->create([
            'item' => '7',
            'codigo' => 'EQ-B07',
            'en_traslado' => true,
        ]);

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSeeInOrder(['EQ-B07', 'EQ-A40'])
            ->assertSeeInOrder(['>1<', '>2<'], false)
            ->assertDontSee('>40<', false)
            ->assertDontSee('>7<', false);
    }

    public function test_receive_fields_list_registered_people_and_accept_new_values(): void
    {
        EquipoComputo::factory()->create([
            'usuario' => 'Laura Gómez',
            'cargo' => 'Auxiliar',
            'area' => 'TESORERIA',
            'departamento' => 'ADMIN Y FINANCIERO',
            'en_traslado' => false,
        ]);
        EquipoComputo::factory()->create([
            'usuario' => 'Laura Gómez',
            'cargo' => 'Auxiliar',
            'area' => 'TESORERIA',
            'departamento' => 'ADMIN Y FINANCIERO',
            'en_traslado' => false,
        ]);
        EquipoComputo::factory()->create([
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'area' => 'MANTENIMIENTO',
            'departamento' => 'OPERACIONES',
            'en_traslado' => false,
        ]);

        $equipo = EquipoComputo::factory()->create([
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista',
            'en_traslado' => true,
        ]);

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('Laura Gómez')
            ->assertSee('Carlos Ruiz')
            ->assertSee('Auxiliar')
            ->assertSee('Coordinador')
            ->assertSee('Escribe o elige un usuario')
            ->assertSee('Escribe o elige un cargo')
            ->assertSee('Escribe o elige un área')
            ->assertSee('Escribe o elige un departamento')
            ->assertSee('Nuevo')
            ->assertSee('data-perfiles-usuarios', false)
            ->assertSee('"cargo":"Auxiliar"', false)
            ->assertSee('"area":"TESORERIA"', false)
            ->assertSee('"departamento":"ADMIN Y FINANCIERO"', false);

        $this->post(route('traslados.store'), [
            'traslados' => [
                $equipo->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Pedro Díaz',
                    'cargo_recibe' => 'Supervisor nuevo',
                    'departamento_recibe' => 'NUEVO DEPARTAMENTO',
                    'area_recibe' => 'NUEVA AREA',
                ],
            ],
        ])->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseHas('equipos_computo', [
            'id' => $equipo->id,
            'usuario' => 'Pedro Díaz',
            'cargo' => 'Supervisor nuevo',
            'departamento' => 'NUEVO DEPARTAMENTO',
            'area' => 'NUEVA AREA',
            'en_traslado' => 0,
        ]);
    }

    public function test_a_transfer_updates_the_equipment_owner(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'usuario' => 'Ana Pérez',
            'cargo' => 'Analista',
            'departamento' => 'GERENCIA',
            'area' => 'TECNOLOGIA',
            'en_traslado' => true,
        ]);

        $this->post(route('traslados.store'), [
            'traslados' => [
                $equipo->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Carlos Ruiz',
                    'cargo_recibe' => 'Coordinador',
                    'departamento_recibe' => 'OPERACIONES',
                    'area_recibe' => 'MANTENIMIENTO',
                    'observaciones' => 'Entrega en portería',
                ],
            ],
        ])->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseHas('equipos_computo', [
            'id' => $equipo->id,
            'usuario' => 'Carlos Ruiz',
            'cargo' => 'Coordinador',
            'departamento' => 'OPERACIONES',
            'area' => 'MANTENIMIENTO',
            'en_traslado' => 0,
        ]);

        $this->assertDatabaseHas('traslados', [
            'equipo_computo_id' => $equipo->id,
            'usuario_entrega' => 'Ana Pérez',
            'usuario_recibe' => 'Carlos Ruiz',
            'area_entrega' => 'TECNOLOGIA',
            'area_recibe' => 'MANTENIMIENTO',
            'observaciones' => 'Entrega en portería',
        ]);

        $this->assertSame(1, Traslado::query()->count());
        $this->assertNull(Traslado::query()->first()?->correo_enviado_at);

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertSee('Traslados realizados')
            ->assertSee('Carlos Ruiz')
            ->assertSee('Ana Pérez')
            ->assertSee('Entrega en portería')
            ->assertSee('WhatsApp')
            ->assertSee('Correo')
            ->assertSee('api.whatsapp.com', false)
            ->assertSee('Escribe o elige un correo')
            ->assertSee('gerencia.general@colbeef.com')
            ->assertSee(TrasladoMensaje::asunto())
            ->assertSee('Historial de Traslados');

        $this->get(route('traslados.historial'))
            ->assertOk()
            ->assertSee('Historial de Traslados')
            ->assertSee('Aún no hay traslados en el historial')
            ->assertDontSee('Entrega en portería');
    }

    public function test_a_transfer_requires_the_receiving_person_fields(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'en_traslado' => true,
        ]);

        $this->from(route('modules.traslados'))
            ->post(route('traslados.store'), [
                'traslados' => [
                    $equipo->id => [
                        'fecha' => '2026-09-12',
                        'usuario_recibe' => 'Carlos Ruiz',
                    ],
                ],
            ])
            ->assertRedirect(route('modules.traslados'))
            ->assertSessionHasErrors([
                'traslados.'.$equipo->id.'.cargo_recibe',
                'traslados.'.$equipo->id.'.departamento_recibe',
                'traslados.'.$equipo->id.'.area_recibe',
            ]);

        $this->assertDatabaseMissing('traslados', [
            'equipo_computo_id' => $equipo->id,
        ]);
        $this->assertTrue($equipo->fresh()->en_traslado);
    }

    public function test_a_transfer_cannot_be_applied_unless_the_equipment_is_marked(): void
    {
        $equipo = EquipoComputo::factory()->create([
            'en_traslado' => false,
        ]);

        $this->post(route('traslados.store'), [
            'traslados' => [
                $equipo->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Carlos Ruiz',
                    'cargo_recibe' => 'Coordinador',
                    'departamento_recibe' => 'OPERACIONES',
                    'area_recibe' => 'MANTENIMIENTO',
                ],
            ],
        ])->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseCount('traslados', 0);
    }

    public function test_one_apply_saves_all_completed_transfers(): void
    {
        $primero = EquipoComputo::factory()->create([
            'usuario' => 'Ana Pérez',
            'en_traslado' => true,
        ]);
        $segundo = EquipoComputo::factory()->create([
            'usuario' => 'Laura Gómez',
            'en_traslado' => true,
        ]);

        $this->post(route('traslados.store'), [
            'traslados' => [
                $primero->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Carlos Ruiz',
                    'cargo_recibe' => 'Coordinador',
                    'departamento_recibe' => 'OPERACIONES',
                    'area_recibe' => 'MANTENIMIENTO',
                ],
                $segundo->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Pedro Díaz',
                    'cargo_recibe' => 'Supervisor',
                    'departamento_recibe' => 'GERENCIA',
                    'area_recibe' => 'TECNOLOGIA',
                ],
            ],
        ])->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseHas('equipos_computo', [
            'id' => $primero->id,
            'usuario' => 'Carlos Ruiz',
            'en_traslado' => 0,
        ]);
        $this->assertDatabaseHas('equipos_computo', [
            'id' => $segundo->id,
            'usuario' => 'Pedro Díaz',
            'en_traslado' => 0,
        ]);
        $this->assertSame(2, Traslado::query()->count());
    }

    public function test_a_signature_can_be_created_with_validation(): void
    {
        Storage::fake('public');

        $this->from(route('modules.traslados'))
            ->post(route('traslados.firmas.store'), [])
            ->assertRedirect(route('modules.traslados'))
            ->assertSessionHasErrors(['nombre', 'contenido']);

        $this->post(route('traslados.firmas.store'), [
            'nombre' => 'Juan Pérez',
            'contenido' => '<p>Área de Sistemas<br>Tecnología Colbeef</p>',
        ])->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseHas('firmas', [
            'nombre' => 'Juan Pérez',
        ]);

        $firma = Firma::query()->firstOrFail();
        Storage::disk('public')->assertExists($firma->imagen);

        $this->from(route('modules.traslados'))
            ->delete(route('traslados.firmas.destroy', $firma))
            ->assertRedirect(route('modules.traslados'));

        $this->assertDatabaseMissing('firmas', ['id' => $firma->id]);
        Storage::disk('public')->assertMissing($firma->imagen);
    }

    public function test_completed_transfers_can_be_emailed_to_several_addresses(): void
    {
        Mail::fake();
        Storage::fake('public');

        $equipo = EquipoComputo::factory()->create(['en_traslado' => true]);
        $this->post(route('traslados.store'), [
            'traslados' => [
                $equipo->id => [
                    'fecha' => '2026-09-12',
                    'usuario_recibe' => 'Carlos Ruiz',
                    'cargo_recibe' => 'Coordinador',
                    'departamento_recibe' => 'OPERACIONES',
                    'area_recibe' => 'MANTENIMIENTO',
                ],
            ],
        ]);

        $firma = Firma::query()->create([
            'nombre' => 'Área de Sistemas',
            'imagen' => UploadedFile::fake()->image('firma.png')->store('firmas', 'public'),
        ]);

        $this->from(route('modules.traslados'))
            ->post(route('traslados.correo'), [
                'correos' => 'no-es-correo',
            ])
            ->assertRedirect(route('modules.traslados'))
            ->assertSessionHasErrors();

        $this->post(route('traslados.correo'), [
            'correos' => 'contabilidad@colbeef.com, sistemas@colbeef.com',
            'firma_id' => $firma->id,
        ])->assertRedirect(route('traslados.historial'));

        Mail::assertSent(TrasladosRealizadosMail::class, function (TrasladosRealizadosMail $mail) use ($firma): bool {
            $adjuntos = $mail->attachments();
            $excel = $adjuntos[0] ?? null;
            $contenido = $excel?->attachWith(
                fn (): string => '',
                fn ($data): string => $data(),
            );

            $this->assertCount(1, $adjuntos);
            $this->assertSame(TrasladoExcel::nombreArchivo(), $excel?->as);
            $this->assertIsString($contenido);
            $this->assertNotSame('', $contenido);
            $this->assertSame(
                ['Ítem', 'Fecha', 'Usuario Entrega', 'Código Contable', 'Área', 'Tipo', 'Descripción del equipo', 'Serial', 'Usuario Recibe', 'Área Recibe', 'Observaciones'],
                $this->primeraFilaExcel($contenido),
            );
            $this->assertStringContainsString('Carlos Ruiz', implode("\n", $this->celdasExcel($contenido)));
            $this->assertStringContainsString('1', implode("\n", $this->primeraFilaDatosExcel($contenido)));
            $this->assertStringContainsString('overflow-x:auto', $mail->render());
            $this->assertStringContainsString('min-width:1100px', $mail->render());
            $this->assertStringNotContainsString('overflow:hidden', $mail->render());

            return $mail->firma->is($firma)
                && $mail->hasTo('contabilidad@colbeef.com')
                && $mail->hasTo('sistemas@colbeef.com')
                && $mail->envelope()->subject === TrasladoMensaje::asunto();
        });

        $this->assertNotNull(Traslado::query()->first()?->correo_enviado_at);

        $this->get(route('modules.traslados'))
            ->assertOk()
            ->assertDontSee('Carlos Ruiz');

        $this->get(route('traslados.historial'))
            ->assertOk()
            ->assertSee('Carlos Ruiz');
    }

    public function test_whatsapp_message_is_short_and_lists_serials(): void
    {
        $mensaje = TrasladoMensaje::whatsapp(collect([
            new Traslado([
                'tipo' => 'PORTATIL',
                'descripcion_equipo' => 'LENOVO THINKPAD',
                'serial' => 'ABC123',
            ]),
        ]));

        $this->assertStringContainsString('✅ Se enviaron los traslados', $mensaje);
        $this->assertStringContainsString('💻 PORTATIL - LENOVO THINKPAD - ABC123', $mensaje);
        $this->assertStringNotContainsString('Cordial saludo', $mensaje);
    }

    public function test_excel_table_includes_every_email_column(): void
    {
        $traslado = new Traslado([
            'fecha' => '2026-09-12',
            'usuario_entrega' => 'BRAYAN GOMEZ',
            'codigo' => 'FRF-067',
            'area_entrega' => 'JURIDICA',
            'tipo' => 'PORTATIL',
            'descripcion_equipo' => 'DELL VOSTRO 3400',
            'serial' => '8V27JM3',
            'usuario_recibe' => 'JUAN PABLO CARREÑO',
            'cargo_recibe' => 'AUXILIAR TIC',
            'area_recibe' => 'TECNOLOGIA',
            'departamento_recibe' => 'ADMIN Y FINANCIERO',
            'observaciones' => 'Cambio de responsable',
        ]);
        $traslado->setRelation('equipo', new EquipoComputo(['item' => '99']));

        $segundo = new Traslado([
            'fecha' => '2026-09-12',
            'usuario_entrega' => 'ANA PEREZ',
            'codigo' => '2020-65',
            'area_entrega' => 'JURIDICA',
            'tipo' => 'PORTATIL',
            'descripcion_equipo' => 'LENOVO THINKPAD',
            'serial' => 'PF11CEYW',
            'usuario_recibe' => 'JUAN PABLO CARREÑO',
            'cargo_recibe' => 'AUXILIAR TIC',
            'area_recibe' => 'TECNOLOGIA',
            'departamento_recibe' => 'ADMIN Y FINANCIERO',
            'observaciones' => '—',
        ]);
        $segundo->setRelation('equipo', new EquipoComputo(['item' => '40']));

        $filas = TrasladoExcel::filas(collect([$traslado, $segundo]));

        $this->assertSame('1', $filas[0][0]);
        $this->assertSame('2', $filas[1][0]);
        $this->assertSame([
            [
                '1',
                '12/09/2026',
                'BRAYAN GOMEZ',
                'FRF-067',
                'JURIDICA',
                'PORTATIL',
                'DELL VOSTRO 3400',
                '8V27JM3',
                "JUAN PABLO CARREÑO\nAUXILIAR TIC",
                "TECNOLOGIA\nADMIN Y FINANCIERO",
                'Cambio de responsable',
            ],
        ], [$filas[0]]);
    }

    /**
     * @return list<string>
     */
    private function primeraFilaExcel(string $contenido): array
    {
        return $this->celdasExcel($contenido, 1);
    }

    /**
     * @return list<string>
     */
    private function primeraFilaDatosExcel(string $contenido): array
    {
        return $this->celdasExcel($contenido, 2);
    }

    /**
     * @return list<string>
     */
    private function celdasExcel(string $contenido, ?int $fila = null): array
    {
        $ruta = sys_get_temp_dir().DIRECTORY_SEPARATOR.'traslados_test_'.uniqid('', true).'.xlsx';
        file_put_contents($ruta, $contenido);

        try {
            $hoja = IOFactory::load($ruta)->getActiveSheet();
            $valores = $fila === null
                ? $hoja->toArray()
                : [$hoja->rangeToArray("A{$fila}:K{$fila}")[0] ?? []];

            return collect($valores)
                ->flatten()
                ->map(fn (mixed $valor): string => trim((string) $valor))
                ->filter()
                ->values()
                ->all();
        } finally {
            if (is_file($ruta)) {
                unlink($ruta);
            }
        }
    }
}
