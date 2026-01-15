<?php

namespace App\Mail\GuestLetter;

use App\Models\Booking;
use Illuminate\Mail\Mailable;

class ConfirmationLetterMail extends Mailable
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
            ? "Hi {$guestName}, your booking is confirmed"
            : "Your booking is confirmed";

        return $this
            ->subject($subject)
            ->cc([
                // 'reservation@nandinibali.com',
                // 'fo@nandinibali.com',
                // 'gm@nandinibali.com',
            ])
            ->view('emails.templates.guestletter.confirmation-letter', [
                'booking' => $this->booking,
                'guest'   => $guest,
                'room'    => $this->booking->room,
            ]);
    }
}
