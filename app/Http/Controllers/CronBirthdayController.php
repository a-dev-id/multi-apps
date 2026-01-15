<?php

// app/Http/Controllers/CronBirthdayController.php
namespace App\Http\Controllers;

use App\Modules\Birthday\Jobs\SendBirthdayEmailJob;
use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Support\Facades\Cache;

class CronBirthdayController extends Controller
{
    public function sendToday(string $token)
    {
        if ($token !== config('app.cron_token')) abort(403);

        $lock = Cache::lock('cron:send-birthday', 55);
        if (! $lock->get()) return 'Locked';

        try {
            $today = now(); // uses APP_TIMEZONE
            $month = (int) $today->format('m');
            $day = (int) $today->format('d');

            $subs = Subscriber::query()
                ->where('is_active', true)
                ->whereNotNull('birth_date')
                ->whereMonth('birth_date', $month)
                ->whereDay('birth_date', $day)
                ->get();

            foreach ($subs as $sub) {
                SendBirthdayEmailJob::dispatch($sub);
            }

            return 'Queued: ' . $subs->count();
        } finally {
            optional($lock)->release();
        }
    }
}
