<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertasCriticas extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $alertasBombas,
        public array $alertasCables,
        public string $rigName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠ Alertas Críticas — {$this->rigName} — " . now()->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alertas-criticas',
        );
    }
}
