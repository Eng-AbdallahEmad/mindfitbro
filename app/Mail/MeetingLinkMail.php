<?php

namespace App\Mail;

use App\Models\MeetingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MeetingBooking $booking,
        public string $customerName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم تحديد رابط جلستك الأولى — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.meeting_link',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
