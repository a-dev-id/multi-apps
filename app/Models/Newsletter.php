<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    protected $guarded = [];

    public function sends(): HasMany
    {
        return $this->hasMany(\App\Models\NewsletterSend::class, 'newsletter_id');
    }

    protected $casts = [
        'country_codes' => 'array',
        'send_to_all' => 'boolean',
        'tag_ids' => 'array',
    ];
}
