<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $customerName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رحلتك بدأت اليوم! — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.subscription_started',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
