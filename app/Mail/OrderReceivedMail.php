<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $customerName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم استلام إيصالك — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order_received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
