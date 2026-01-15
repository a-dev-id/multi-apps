<?php

namespace App\Http\Controllers;

use App\Modules\Newsletter\Jobs\SendNewsletterJob;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Support\Facades\Cache;

class CronNewsletterController extends Controller
{
    public function sendScheduled(string $token)
    {
        if ($token !== config('app.cron_token')) {
            abort(403);
        }

        $lock = Cache::lock('cron:send-scheduled-newsletters', 55);

        if (! $lock->get()) {
            return 'Locked. Another cron is running.';
        }

        try {
            $dueNewsletters = Newsletter::query()
                ->whereNotNull('scheduled_at')
                ->whereNull('sent_at')
                ->where('scheduled_at', '<=', now())
                ->get();

            if ($dueNewsletters->isEmpty()) {
                return 'No newsletters to send.';
            }

            foreach ($dueNewsletters as $newsletter) {

                $subscribersQuery = Subscriber::query()
                    ->where('is_active', true);

                // ✅ Exactly 4 options: all | country | year | tags
                $audienceType = $newsletter->audience_type ?: 'all';

                switch ($audienceType) {
                    case 'country':
                        $codes = $newsletter->country_codes ?? [];
                        if (! empty($codes)) {
                            $subscribersQuery->whereIn('country_code', $codes);
                        } else {
                            $subscribersQuery->whereRaw('1=0');
                        }
                        break;

                    case 'year':
                        if (! empty($newsletter->guest_year)) {
                            $yearSlug = 'stayed-' . (int) $newsletter->guest_year;

                            $subscribersQuery->whereHas('tags', function ($q) use ($yearSlug) {
                                $q->where('slug', $yearSlug);
                            });
                        } else {
                            $subscribersQuery->whereRaw('1=0');
                        }
                        break;

                    case 'tags':
                        // Send if subscriber has ANY of the selected tags
                        $tagIds = $newsletter->tag_ids ?? []; // array of tag IDs
                        if (! empty($tagIds)) {
                            $subscribersQuery->whereHas('tags', function ($q) use ($tagIds) {
                                $q->whereIn('id', $tagIds);
                            });
                        } else {
                            $subscribersQuery->whereRaw('1=0');
                        }
                        break;

                    case 'all':
                    default:
                        break;
                }

                $subscribersQuery
                    ->orderBy('id')
                    ->chunkById(300, function ($subscribers) use ($newsletter) {
                        foreach ($subscribers as $subscriber) {
                            dispatch(new SendNewsletterJob($newsletter, $subscriber));
                        }
                    });

                $newsletter->update([
                    'sent_at' => now(),
                ]);
            }

            return 'OK';
        } finally {
            optional($lock)->release();
        }
    }
}
