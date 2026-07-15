<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoachOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $coachName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق من بريدك الإلكتروني — MindFitBro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.coach_otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
