<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialBroadcast extends Model
{
    protected $fillable = [
        'broadcastable_type', 'broadcastable_id',
        'platform', 'status', 'post_url', 'error',
    ];

    public function broadcastable(): MorphTo
    {
        return $this->morphTo();
    }
}
