<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPendingReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $customerName,
        public string $customerEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلب شراء جديد بانتظار المراجعة — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order_pending_review',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
