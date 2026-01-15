<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $table = 'nl_tags';

    protected $fillable = ['name', 'slug'];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'nl_subscriber_tag');
    }

    protected static function booted()
    {
        static::creating(fn($tag) => $tag->slug ??= \Illuminate\Support\Str::slug($tag->name));
    }
}
