<?php

namespace App\Modules\GuestLetter\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'content',
    ];
}
