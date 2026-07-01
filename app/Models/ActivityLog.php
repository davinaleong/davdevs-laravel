<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $table = 'activity_log';

    protected $fillable = [
        'channel', 'level', 'message', 'context',
        'ip_hash', 'user_agent', 'url', 'method', 'status_code', 'duration_ms',
    ];

    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}
