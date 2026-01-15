<?php

namespace App\Http\Controllers;

use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use Illuminate\Support\Facades\Cache;

class CronGuestLetterController extends Controller
{
    public function dispatch(string $token)
    {
        if ($token !== config('app.cron_token')) {
            abort(403);
        }

        $lock = Cache::lock('cron:guestletter-dispatch', 55);

        if (! $lock->get()) {
            return response()->json([
                'status'  => 'locked',
                'message' => 'Another cron is running.',
            ], 423);
        }

        try {
            // Shared-hosting safe:
            // - Do NOT rely on queue workers (use dispatchSync)
            // - Do NOT set processing here (job will do it)
            // - Recover stuck processing rows older than 10 minutes

            $due = GuestLetterSend::query()
                ->where(function ($q) {
                    // Normal due "pending" rows
                    $q->where(function ($q1) {
                        $q1->where('status', 'pending')
                            ->where(function ($q2) {
                                $q2->whereNull('scheduled_for')
                                    ->orWhere('scheduled_for', '<=', now());
                            });
                    })
                        // Recover stuck "processing" rows (worker crashed / timeout)
                        ->orWhere(function ($q3) {
                            $q3->where('status', 'processing')
                                ->where('updated_at', '<=', now()->subMinutes(10))
                                ->where(function ($q4) {
                                    $q4->whereNull('scheduled_for')
                                        ->orWhere('scheduled_for', '<=', now());
                                });
                        });
                })
                ->orderByRaw('scheduled_for IS NULL, scheduled_for ASC')
                ->limit(300)
                ->get();

            if ($due->isEmpty()) {
                return response()->json([
                    'status'  => 'ok',
                    'message' => 'No guest letters due.',
                    'count'   => 0,
                    'now'     => now()->toDateTimeString(),
                    'tz'      => config('app.timezone'),
                ]);
            }

            $sent = 0;
            $failed = 0;
            $skipped = 0;
            $errors = [];

            foreach ($due as $send) {
                try {
                    // Ensure it can be processed
                    if (! in_array($send->status, ['pending', 'processing'], true)) {
                        $skipped++;
                        continue;
                    }

                    // If it was recovered "processing", reset to pending so job runs cleanly
                    if ($send->status === 'processing') {
                        $send->update([
                            'status'        => 'pending',
                            'failed_at'     => null,
                            'error_message' => null,
                            'updated_at'    => now(),
                        ]);
                    }

                    SendGuestLetterJob::dispatchSync($send->id);

                    $send->refresh();

                    if ($send->status === 'sent') {
                        $sent++;
                    } elseif ($send->status === 'failed') {
                        $failed++;
                        $errors[] = [
                            'id' => $send->id,
                            'type' => $send->type,
                            'error' => $send->error_message,
                        ];
                    } else {
                        // Never leave it stuck
                        $send->update([
                            'status' => 'pending',
                            'updated_at' => now(),
                        ]);

                        $failed++;
                        $errors[] = [
                            'id' => $send->id,
                            'type' => $send->type,
                            'error' => 'Job did not finish (status not sent/failed). Reset to pending.',
                        ];
                    }
                } catch (\Throwable $e) {
                    // Never leave it stuck
                    $send->update([
                        'status'        => 'failed',
                        'failed_at'     => now(),
                        'error_message' => $e->getMessage(),
                        'updated_at'    => now(),
                    ]);

                    $failed++;
                    $errors[] = [
                        'id' => $send->id,
                        'type' => $send->type,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'status'  => 'ok',
                'message' => 'Processed.',
                'count'   => $due->count(),
                'sent'    => $sent,
                'failed'  => $failed,
                'skipped' => $skipped,
                'now'     => now()->toDateTimeString(),
                'tz'      => config('app.timezone'),
                'errors'  => array_slice($errors, 0, 20),
            ]);
        } finally {
            optional($lock)->release();
        }
    }

    public function dispatchPostStay(string $token)
    {
        if ($token !== config('app.cron_token')) {
            abort(403);
        }

        $lock = Cache::lock('cron:guestletter-dispatch-post-stay', 55);

        if (! $lock->get()) {
            return response()->json([
                'status'  => 'locked',
                'message' => 'Another cron is running.',
            ], 423);
        }

        try {
            // Find all pending post-stay letters that are due
            $due = GuestLetterSend::query()
                ->where('type', 'post_stay')
                ->where(function ($q) {
                    // Normal due "pending" rows
                    $q->where(function ($q1) {
                        $q1->where('status', 'pending')
                            ->where(function ($q2) {
                                $q2->whereNull('scheduled_for')
                                    ->orWhere('scheduled_for', '<=', now());
                            });
                    })
                        // Recover stuck "processing" rows (worker crashed / timeout)
                        ->orWhere(function ($q3) {
                            $q3->where('status', 'processing')
                                ->where('updated_at', '<=', now()->subMinutes(10))
                                ->where(function ($q4) {
                                    $q4->whereNull('scheduled_for')
                                        ->orWhere('scheduled_for', '<=', now());
                                });
                        });
                })
                ->orderByRaw('scheduled_for IS NULL, scheduled_for ASC')
                ->limit(300)
                ->get();

            if ($due->isEmpty()) {
                return response()->json([
                    'status'  => 'ok',
                    'message' => 'No post-stay letters due.',
                    'count'   => 0,
                    'now'     => now()->toDateTimeString(),
                    'tz'      => config('app.timezone'),
                ]);
            }

            $sent = 0;
            $failed = 0;
            $skipped = 0;
            $errors = [];

            foreach ($due as $send) {
                try {
                    // Ensure it can be processed
                    if (! in_array($send->status, ['pending', 'processing'], true)) {
                        $skipped++;
                        continue;
                    }

                    // If it was recovered "processing", reset to pending so job runs cleanly
                    if ($send->status === 'processing') {
                        $send->update([
                            'status'        => 'pending',
                            'failed_at'     => null,
                            'error_message' => null,
                            'updated_at'    => now(),
                        ]);
                    }

                    SendGuestLetterJob::dispatchSync($send->id);

                    $send->refresh();

                    if ($send->status === 'sent') {
                        $sent++;
                    } elseif ($send->status === 'failed') {
                        $failed++;
                        $errors[] = [
                            'id' => $send->id,
                            'type' => $send->type,
                            'error' => $send->error_message,
                        ];
                    } else {
                        // Never leave it stuck
                        $send->update([
                            'status' => 'pending',
                            'updated_at' => now(),
                        ]);

                        $failed++;
                        $errors[] = [
                            'id' => $send->id,
                            'type' => $send->type,
                            'error' => 'Job did not finish (status not sent/failed). Reset to pending.',
                        ];
                    }
                } catch (\Throwable $e) {
                    // Never leave it stuck
                    $send->update([
                        'status'        => 'failed',
                        'failed_at'     => now(),
                        'error_message' => $e->getMessage(),
                        'updated_at'    => now(),
                    ]);

                    $failed++;
                    $errors[] = [
                        'id' => $send->id,
                        'type' => $send->type,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'status'  => 'ok',
                'message' => 'Processed.',
                'count'   => $due->count(),
                'sent'    => $sent,
                'failed'  => $failed,
                'skipped' => $skipped,
                'now'     => now()->toDateTimeString(),
                'tz'      => config('app.timezone'),
                'errors'  => array_slice($errors, 0, 20),
            ]);
        } finally {
            optional($lock)->release();
        }
    }
}
