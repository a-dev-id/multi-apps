<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscriber extends Model
{
    protected $guarded = [];
    protected static function booted()
    {
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::uuid();
        });
    }

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'subscriber_tag');
    }
}
