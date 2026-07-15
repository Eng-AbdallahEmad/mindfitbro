<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Models\FamilyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamilyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FamilyInvitation $invitation,
        public Coupon $coupon,
        public string $inviterName,
        public int $discountPercent,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->inviterName . ' يدعوك للانضمام إلى MindFitBro 🎁',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.family_invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
