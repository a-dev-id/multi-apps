<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    protected $table = 'nl_newsletters';

    protected $fillable = [
        'subject',
        'body_html',
        'scheduled_at',
        'sent_at',
        'tag_id',
        'audience_type',
        'country_codes',
        'send_to_all',
    ];

    protected $casts = [
        'country_codes' => 'array',
        'send_to_all' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class, 'newsletter_id');
    }
}
