<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['reactionable_type', 'reactionable_id', 'token_hash', 'ip_hash'];

    public function reactionable()
    {
        return $this->morphTo();
    }
}
