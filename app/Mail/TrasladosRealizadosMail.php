<?php

namespace App\Mail;

use App\Models\Firma;
use App\Models\Traslado;
use App\Support\TrasladoExcel;
use App\Support\TrasladoMensaje;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class TrasladosRealizadosMail extends Mailable
{
    /**
     * @param  Collection<int, Traslado>  $traslados
     */
    public function __construct(
        public Collection $traslados,
        public Firma $firma,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('tecnologia@colbeef.com', 'Tecnología Colbeef'),
            subject: TrasladoMensaje::asunto(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'traslados.correo',
            with: [
                'traslados' => $this->traslados,
                'firma' => $this->firma,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn (): string => TrasladoExcel::bytes($this->traslados),
                TrasladoExcel::nombreArchivo(),
            )->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
