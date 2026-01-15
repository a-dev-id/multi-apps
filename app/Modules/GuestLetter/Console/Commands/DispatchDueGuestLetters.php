<?php

namespace App\Modules\GuestLetter\Console\Commands;

use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DispatchDueGuestLetters extends Command
{
    protected $signature = 'guestletter:dispatch-due';
    protected $description = 'Dispatch due guest letters (pending and scheduled_for <= now)';

    public function handle(): int
    {
        $lock = Cache::lock('cron:guestletter-dispatch-due', 55);

        if (! $lock->get()) {
            $this->info('Locked. Another run in progress.');
            return self::SUCCESS;
        }

        try {
            $due = GuestLetterSend::query()
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('scheduled_for')
                        ->orWhere('scheduled_for', '<=', now());
                })
                ->orderBy('scheduled_for')
                ->limit(500)
                ->get();

            if ($due->isEmpty()) {
                $this->info('No due guest letters.');
                return self::SUCCESS;
            }

            foreach ($due as $send) {
                SendGuestLetterJob::dispatch($send->id);
            }

            $this->info('Dispatched: ' . $due->count());
            return self::SUCCESS;
        } finally {
            optional($lock)->release();
        }
    }
}
