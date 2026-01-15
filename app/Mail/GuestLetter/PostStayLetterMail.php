<?php

namespace App\Mail\GuestLetter;

use App\Models\Booking;
use Illuminate\Mail\Mailable;

class PostStayLetterMail extends Mailable
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
            ? "Thank you {$guestName}, for letting us be part of your Bali story."
            : "Thank you for letting us be part of your Bali story.";

        return $this
            ->subject($subject)
            ->cc([
                'reservation@nandinibali.com',
                'fo@nandinibali.com',
                'gm@nandinibali.com',
            ])
            ->view('emails.templates.guestletter.post-stay-letter', [
                'booking' => $this->booking,
                'guest'   => $guest,
                'room'    => $this->booking->room,
            ]);
    }
}
