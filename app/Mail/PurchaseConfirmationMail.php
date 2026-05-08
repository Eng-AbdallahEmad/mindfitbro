<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تأكيد شراء باقتك — MindFitBro 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.purchase_confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
