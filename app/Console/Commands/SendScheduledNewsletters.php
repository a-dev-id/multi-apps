<?php

namespace App\Console\Commands;

use App\Modules\Newsletter\Jobs\SendNewsletterJob;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Console\Command;

class SendScheduledNewsletters extends Command
{
    protected $signature = 'newsletters:send-scheduled';

    protected $description = 'Send all newsletters that are scheduled and due';

    public function handle(): int
    {
        $dueNewsletters = Newsletter::whereNotNull('scheduled_at')
            ->whereNull('sent_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($dueNewsletters->isEmpty()) {
            $this->info('No newsletters to send.');
            return self::SUCCESS;
        }

        $subscribers = Subscriber::where('is_active', true)->get();

        foreach ($dueNewsletters as $newsletter) {
            foreach ($subscribers as $subscriber) {
                dispatch(new SendNewsletterJob($newsletter, $subscriber));
            }

            // mark as sent
            $newsletter->update([
                'sent_at' => now(),
            ]);

            $this->info("Queued newsletter #{$newsletter->id} ({$newsletter->subject})");
        }

        return self::SUCCESS;
    }
}
