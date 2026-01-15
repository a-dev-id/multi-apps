<?php

namespace App\Modules\Birthday\Mail;

use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BirthdayMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Happy Birthday from Nandini Jungle Family',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.birthday',
            with: [
                'subscriber' => $this->subscriber,
                'token' => $this->subscriber->unsubscribe_token,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
