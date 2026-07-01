<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryMeta extends Model
{
    public $timestamps = false;

    protected $fillable = ['entry_id', 'key', 'value'];

    protected $table = 'entry_meta';

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}
