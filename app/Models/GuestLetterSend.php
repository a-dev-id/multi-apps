<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestLetterSend extends Model
{
    protected $fillable = [
        'booking_id',
        'type',
        'status',
        'to_email',
        'scheduled_for',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    protected $table = 'guest_letter_sends';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
