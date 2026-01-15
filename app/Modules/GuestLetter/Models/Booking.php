<?php

namespace App\Modules\GuestLetter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Guest;
use App\Models\Room;

class Booking extends Model
{
    protected $table = 'gl_bookings';

    protected $fillable = [
        'guest_id',
        'room_id',
        'booking_number',
        'arrival_date',
        'departure_date',
        'adult',
        'child',
        'campaign_name',
        'campaign_benefit',
        'remark',
        'confirmation_sent_at',
        'reference'
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'confirmation_sent_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function letterSchedules()
    {
        return $this->hasMany(LetterSchedule::class, 'booking_id');
    }

    public function guestLetterSends(): HasMany
    {
        return $this->hasMany(GuestLetterSend::class, 'booking_id');
    }
}
