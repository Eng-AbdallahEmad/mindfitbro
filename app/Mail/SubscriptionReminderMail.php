<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renewalUrl;

    public function __construct(
        public Subscription $subscription,
        public string $customerName,
    ) {
        $this->renewalUrl = $subscription->plan
            ? route('purchase.form', $subscription->plan)
            : url('/');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تذكير: اشتراكك ينتهي خلال 5 أيام — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.subscription_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
