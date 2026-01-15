<?php

namespace App\Observers;

use App\Jobs\SendGuestLetterJob;
use App\Models\Booking;
use App\Models\GuestLetterSend;

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

        // 2) Pre-arrival (scheduled)
        $preDays = (int) config('guestletter.pre_arrival_days', 3);
        $preAt = $booking->arrival_date?->copy()->subDays($preDays);

        if ($preAt) {
            GuestLetterSend::query()->firstOrCreate(
                ['booking_id' => $booking->id, 'type' => 'pre_arrival'],
                [
                    'status' => 'pending',
                    'to_email' => $guest->email,
                    'scheduled_for' => $preAt->startOfDay()->addHours(9), // 09:00
                ]
            );
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
