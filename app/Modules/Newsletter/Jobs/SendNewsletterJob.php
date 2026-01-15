<?php

namespace App\Modules\Newsletter\Jobs;

use App\Modules\Newsletter\Mail\NewsletterMail;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterSend;
use App\Modules\Newsletter\Models\Subscriber;
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
            ]);
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
