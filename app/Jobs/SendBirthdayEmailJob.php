<?php

namespace App\Jobs;

use App\Mail\GuestLetter\ConfirmationLetterMail;
use App\Mail\GuestLetter\PreArrivalLetterMail;
use App\Mail\GuestLetter\PostStayLetterMail;
use App\Models\GuestLetterSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendGuestLetterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $sendId) {}

    public function handle(): void
    {
        Log::info('SendGuestLetterJob start', ['id' => $this->sendId]);

        $send = GuestLetterSend::query()
            ->with(['booking.guest', 'booking.room'])
            ->find($this->sendId);

        // Allow pending OR recovered processing
        if (! $send || ! in_array($send->status, ['pending', 'processing'], true)) {
            Log::info('SendGuestLetterJob skip (not pending/processing)', [
                'id' => $this->sendId,
                'status' => $send?->status,
            ]);
            return;
        }

        // Job owns the transition to processing (cron should not set it)
        $send->update([
            'status' => 'processing',
            'updated_at' => now(),
        ]);

        $booking = $send->booking;
        if (! $booking) {
            $send->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => 'Booking not found',
            ]);
            return;
        }

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
            $mailable = match ($send->type) {
                'confirmation' => new ConfirmationLetterMail($booking),
                'pre_arrival'  => new PreArrivalLetterMail($booking),
                'post_stay'    => new PostStayLetterMail($booking),
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

            Log::info('SendGuestLetterJob before send', [
                'id' => $this->sendId,
                'type' => $send->type,
                'to' => $to,
            ]);

            Mail::to($to)->send($mailable);

            Log::info('SendGuestLetterJob after send', ['id' => $this->sendId]);

            $send->update([
                'status' => 'sent',
                'sent_at' => now(),
                'failed_at' => null,
                'error_message' => null,
            ]);

            // Optional: keep this if you still want the Booking list "Confirmation Sent" column
            if ($send->type === 'confirmation') {
                $booking->forceFill(['confirmation_sent_at' => now()])->save();
            }
        } catch (Throwable $e) {
            Log::error('SendGuestLetterJob failed', [
                'id' => $this->sendId,
                'error' => $e->getMessage(),
            ]);

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
