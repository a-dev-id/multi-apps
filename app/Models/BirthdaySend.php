<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirthdaySend extends Model
{
    protected $fillable = [
        'subscriber_id',
        'year',
        'sent_at',
        'status',
        'error',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
