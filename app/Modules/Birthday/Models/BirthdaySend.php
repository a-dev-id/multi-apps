<?php

namespace App\Modules\Birthday\Models;

use Illuminate\Database\Eloquent\Model;

class BirthdaySend extends Model
{
    protected $table = 'bd_birthday_sends';

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
