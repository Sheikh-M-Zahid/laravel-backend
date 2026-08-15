<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purpose, // 'register' | 'password_reset'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'register'
            ? 'Verify your email — Smart Agri-Advisory Platform'
            : 'Reset your password — Smart Agri-Advisory Platform';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp');
    }
}
