<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Subscriber::class);
    }

    protected static function booted()
    {
        static::creating(fn($tag) => $tag->slug ??= \Illuminate\Support\Str::slug($tag->name));
    }
}
