<?php

namespace App\Modules\GuestLetter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class GuestLetterSend extends Model
{
    protected $fillable = [
        'booking_id',
        'guest_id',
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

    protected $table = 'gl_guest_letter_sends';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function guestDirect(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Guest::class, 'guest_id');
    }

    public function guest(): HasOneThrough
    {
        return $this->hasOneThrough(
            \App\Models\Guest::class,
            Booking::class,
            'id',
            'id',
            'booking_id',
            'guest_id'
        );
    }
}
