<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    /**
     * Crear una nueva instancia del mensaje.
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    /**
     * Encabezado del correo (asunto, remitente, etc.).
     */
   public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Restablecer tu contraseña - App-EPS',
        from: new \Illuminate\Mail\Mailables\Address(
            config('mail.from.address'),
            config('mail.from.name')
        )
    );
}


    /**
     * Contenido del correo (vista markdown).
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.olvideClave',
            with: [
                'datos' => $this->datos
            ]
        );
    }

    /**
     * Archivos adjuntos (si los hubiera).
     */
    public function attachments(): array
    {
        return [];
    }
}
