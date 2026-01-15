<?php

namespace App\Modules\GuestLetter\Mail;

use App\Models\Guest;
use App\Modules\GuestLetter\Models\Booking;
use Illuminate\Mail\Mailable;

class PostStayLetterMail extends Mailable
{
    public function __construct(public ?Booking $booking = null, public ?Guest $guest = null) {}

    public function build()
    {
        // For manually created post-stay letters without a booking
        if (!$this->booking) {
            $guestName = $this->guest ? trim(collect([
                $this->guest->title ?? null,
                $this->guest->first_name ?? null,
                $this->guest->last_name ?? null,
            ])->filter()->join(' ')) : '';

            $subject = $guestName !== ''
                ? "Thank you {$guestName}, for letting us be part of your Bali story."
                : "Thank you for letting us be part of your Bali story.";

            return $this
                ->subject($subject)
                ->cc([
                    // 'reservation@nandinibali.com',
                    // 'fo@nandinibali.com',
                    // 'gm@nandinibali.com',
                ])
                ->view('emails.templates.guestletter.post-stay-letter', [
                    'booking' => null,
                    'guest'   => $this->guest,
                    'room'    => null,
                ]);
        }

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
                // 'reservation@nandinibali.com',
                // 'fo@nandinibali.com',
                // 'gm@nandinibali.com',
            ])
            ->view('emails.templates.guestletter.post-stay-letter', [
                'booking' => $this->booking,
                'guest'   => $guest,
                'room'    => $this->booking->room,
            ]);
    }
}
