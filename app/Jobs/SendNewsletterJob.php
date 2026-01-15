<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public Subscriber $subscriber,
    ) {}

    public function handle(): void
    {
        if (! $this->subscriber->is_active) {
            return;
        }

        $send = NewsletterSend::firstOrCreate(
            [
                'newsletter_id' => $this->newsletter->id,
                'subscriber_id' => $this->subscriber->id,
            ],
            [
                'sent_at' => null,
                'open_count' => 0,
                'failed' => 0,
            ]
        );

        // Prevent duplicate sending
        if ($send->sent_at !== null) {
            return;
        }

        try {
            Mail::to($this->subscriber->email)->send(
                new NewsletterMail(
                    newsletter: $this->newsletter,
                    subscriber: $this->subscriber,
                    newsletterSend: $send,
                )
            );

            // ✅ Mark as sent AFTER success
            $send->update([
                'sent_at' => now(),
                'failed' => 0,
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            $send->update([
                'failed' => 1,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
