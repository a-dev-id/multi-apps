<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subscriber extends Model
{
    protected $table = 'nl_subscribers';

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'unsubscribe_token',
        'country_code',
        'birth_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::uuid();
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'nl_subscriber_tag');
    }
}
