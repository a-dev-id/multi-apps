<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public Subscriber $subscriber,
        public ?NewsletterSend $newsletterSend = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject ?? 'Happy New Year 2026🎊 We Hope the Jungle Still Lingers in Your Memories',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.templates.default',
            with: [
                'newsletter' => $this->newsletter,
                'subscriber' => $this->subscriber,
                'newsletterSend' => $this->newsletterSend,
                'subject' => $this->newsletter->subject ?? 'Happy New Year 2026🎊 We Hope the Jungle Still Lingers in Your Memories',
            ],
        );
    }
}
