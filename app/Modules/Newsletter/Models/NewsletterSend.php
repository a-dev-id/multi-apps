<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSend extends Model
{
    protected $table = 'nl_newsletter_sends';

    protected $fillable = [
        'newsletter_id',
        'subscriber_id',
        'email',
        'sent_at',
        'open_count',
        'opened_at',
        'last_open_ip',
        'last_open_country',
        'last_open_user_agent',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    public $timestamps = true;
}
