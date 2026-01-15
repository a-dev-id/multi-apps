<?php

namespace App\Modules\GuestLetter\Observers;

use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use App\Modules\GuestLetter\Models\Booking;
use App\Modules\GuestLetter\Models\GuestLetterSend;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $booking->loadMissing(['guest', 'room']);

        $guest = $booking->guest;

        if (! $guest || ! $guest->email) {
            return;
        }

        // 1) Confirmation (send now)
        $confirmation = GuestLetterSend::query()->firstOrCreate(
            ['booking_id' => $booking->id, 'type' => 'confirmation'],
            [
                'status' => 'pending',
                'to_email' => $guest->email,
                'scheduled_for' => null,
            ]
        );

        if ($confirmation->wasRecentlyCreated) {
            SendGuestLetterJob::dispatch($confirmation->id);
        }

        // 2) Pre-arrival (scheduled dynamically based on booking lead time)
        if ($booking->arrival_date) {
            $daysUntilArrival = now()->diffInDays($booking->arrival_date, false);

            // Determine when to send pre-arrival based on lead time
            $preDays = null;
            $sendImmediately = false;

            if ($daysUntilArrival >= 14) {
                $preDays = 14;
            } elseif ($daysUntilArrival >= 7) {
                $preDays = 7;
            } elseif ($daysUntilArrival >= 3) {
                $preDays = 3;
            } elseif ($daysUntilArrival >= 0) {
                // Arrival in 0-2 days: send immediately
                $sendImmediately = true;
            }

            // Create pre-arrival letter
            if ($preDays !== null || $sendImmediately) {
                $preArrival = GuestLetterSend::query()->firstOrCreate(
                    ['booking_id' => $booking->id, 'type' => 'pre_arrival'],
                    [
                        'status' => 'pending',
                        'to_email' => $guest->email,
                        'scheduled_for' => $sendImmediately
                            ? null
                            : $booking->arrival_date->copy()->subDays($preDays)->startOfDay()->addHours(9),
                    ]
                );

                // Send immediately if within 2 days of arrival
                if ($sendImmediately && $preArrival->wasRecentlyCreated) {
                    SendGuestLetterJob::dispatch($preArrival->id);
                }
            }
        }

        // 3) Post-stay (scheduled)
        $postDays = (int) config('guestletter.post_stay_days', 1);
        $postAt = $booking->departure_date?->copy()->addDays($postDays);

        if ($postAt) {
            GuestLetterSend::query()->firstOrCreate(
                ['booking_id' => $booking->id, 'type' => 'post_stay'],
                [
                    'status' => 'pending',
                    'to_email' => $guest->email,
                    'scheduled_for' => $postAt->startOfDay()->addHours(10), // 10:00
                ]
            );
        }
    }
}
