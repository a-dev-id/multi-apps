<?php

namespace App\Modules\GuestLetter\Models;

use Illuminate\Database\Eloquent\Model;

class LetterSchedule extends Model
{
    protected $fillable = [
        'booking_id',
        'type',
        'scheduled_for',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];
}
