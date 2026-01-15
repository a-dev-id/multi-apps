<?php

namespace App\Modules\GuestLetter\Mail;

use App\Modules\GuestLetter\Models\Booking;
use Illuminate\Mail\Mailable;

class PreArrivalLetterMail extends Mailable
{
    public function __construct(public Booking $booking) {}

    public function build()
    {
        $guest = $this->booking->guest;

        $guestName = trim(collect([
            $guest->title ?? null,
            $guest->first_name ?? null,
            $guest->last_name ?? null,
        ])->filter()->join(' '));

        $subject = $guestName !== ''
            ? "We're getting everything ready for your stay, {$guestName}!"
            : "We're getting everything ready for your stay!";


        return $this
            ->subject($subject)
            ->cc([
                // 'reservation@nandinibali.com',
                // 'fo@nandinibali.com',
                // 'gm@nandinibali.com',
            ])
            ->view('emails.templates.guestletter.pre-arrival-letter', [
                'booking' => $this->booking,
                'guest'   => $guest,
                'room'    => $this->booking->room,
            ]);
    }
}
