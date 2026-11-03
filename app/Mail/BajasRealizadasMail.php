<?php

namespace App\Mail;

use App\Models\Baja;
use App\Models\Firma;
use App\Support\BajaActaAdjunto;
use App\Support\BajaAdjuntoNombres;
use App\Support\BajaMensaje;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BajasRealizadasMail extends Mailable
{
    /**
     * @param  Collection<int, Baja>  $bajas
     */
    public function __construct(
        public Collection $bajas,
        public Firma $firma,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('tecnologia@colbeef.com', 'Tecnología Colbeef'),
            subject: BajaMensaje::asunto(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'bajas.correo',
            with: [
                'bajas' => $this->bajas,
                'firma' => $this->firma,
                'tituloTabla' => BajaMensaje::tituloTabla(),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $adjuntos = [];

        foreach ($this->bajas as $baja) {
            $adjuntos[] = Attachment::fromData(
                fn (): string => BajaActaAdjunto::pdf($baja),
                BajaAdjuntoNombres::acta($baja),
            )->withMime('application/pdf');

            foreach ($baja->rutasEvidencia() as $foto => $ruta) {
                if (! Storage::disk('public')->exists($ruta)) {
                    continue;
                }

                $extension = pathinfo($ruta, PATHINFO_EXTENSION) ?: 'jpg';

                $adjuntos[] = Attachment::fromStorageDisk('public', $ruta)
                    ->as(BajaAdjuntoNombres::evidencia($baja, $extension, $foto + 1));
            }

            $soporte = $baja->adjunto_soporte;

            if (filled($soporte) && Storage::disk('public')->exists($soporte)) {
                $extension = pathinfo((string) $soporte, PATHINFO_EXTENSION) ?: 'bin';

                $adjuntos[] = Attachment::fromStorageDisk('public', $soporte)
                    ->as(BajaAdjuntoNombres::diagnostico($baja, $extension));
            }
        }

        return $adjuntos;
    }
}
