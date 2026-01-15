<?php

namespace App\Models;

use App\Modules\GuestLetter\Models\Booking;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->title, $this->first_name, $this->last_name]);
        return trim(implode(' ', $parts));
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function guestLetterSends(): HasMany
    {
        return $this->hasMany(GuestLetterSend::class);
    }
}
