<?php

namespace App\Modules\GuestLetter\Jobs;

use App\Modules\GuestLetter\Mail\ConfirmationLetterMail;
use App\Modules\GuestLetter\Mail\PreArrivalLetterMail;
use App\Modules\GuestLetter\Mail\PostStayLetterMail;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Support\Facades\Log;

class SendGuestLetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $sendId) {}

    public function handle(): void
    {
        // log start
        Log::info('SendGuestLetterJob start', ['id' => $this->sendId]);

        $send = GuestLetterSend::query()
            ->with(['booking.guest', 'booking.room', 'guestDirect'])
            ->find($this->sendId);

        if (! $send || $send->status !== 'pending') {
            return;
        }

        $booking = $send->booking;

        $to = $send->to_email;
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $send->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => 'Invalid email address',
            ]);
            return;
        }

        try {
            // For post_stay without booking, use guestDirect relationship
            $postStayGuest = null;
            if ($send->type === 'post_stay' && !$booking && $send->guestDirect) {
                $postStayGuest = $send->guestDirect;
            }

            $mailable = match ($send->type) {
                'confirmation' => new ConfirmationLetterMail($booking),
                'pre_arrival'  => new PreArrivalLetterMail($booking),
                'post_stay'    => new PostStayLetterMail($booking, $postStayGuest),
                default        => null,
            };

            if (! $mailable) {
                $send->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'error_message' => 'Unknown type: ' . $send->type,
                ]);
                return;
            }

            // log before send
            Log::info('SendGuestLetterJob before send', ['id' => $this->sendId, 'type' => $send->type, 'to' => $to]);

            Mail::to($to)->send($mailable);

            // log after send
            Log::info('SendGuestLetterJob after send', ['id' => $this->sendId]);


            $send->update([
                'status' => 'sent',
                'sent_at' => now(),
                'failed_at' => null,
                'error_message' => null,
            ]);

            if ($send->type === 'confirmation' && $booking) {
                $booking->forceFill(['confirmation_sent_at' => now()])->save();
            }
        } catch (Throwable $e) {
            // log error
            Log::error('SendGuestLetterJob failed', ['id' => $this->sendId, 'error' => $e->getMessage()]);

            $msg = $e->getMessage();
            if (is_string($msg) && strlen($msg) > 250) {
                $msg = substr($msg, 0, 250) . '...';
            }

            $send->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $msg,
            ]);
        }
    }
}
