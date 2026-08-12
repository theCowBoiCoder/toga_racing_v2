<?php

namespace App\Mail;

use App\Models\DriverApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DriverApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to TOGA Racing');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-accepted',
            text: 'emails.driver-accepted-text',
        );
    }
}
