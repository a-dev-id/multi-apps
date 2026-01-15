<?php

namespace App\Modules\Birthday\Jobs;

use App\Modules\Birthday\Mail\BirthdayMail;
use App\Modules\Birthday\Models\BirthdaySend;
use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendBirthdayEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber) {}

    public function handle(): void
    {
        if (! $this->subscriber->is_active) {
            return;
        }

        $year = now()->year;

        $send = BirthdaySend::firstOrCreate(
            [
                'subscriber_id' => $this->subscriber->id,
                'year' => $year,
            ],
            [
                'status' => 'pending',
                'sent_at' => null,
                'error' => null,
            ]
        );

        // Already sent this year
        if ($send->sent_at !== null) {
            return;
        }

        try {
            Mail::to($this->subscriber->email)->send(
                new BirthdayMail($this->subscriber)
            );

            $send->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error' => null,
            ]);
        } catch (Throwable $e) {
            $send->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
