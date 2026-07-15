<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $customerName,
        public bool $accountAutoCreated = false,
        public ?string $passwordSetUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم الموافقة على اشتراكك! — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order_approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
