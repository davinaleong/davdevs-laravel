<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationMeta extends Model
{
    public $timestamps = false;

    protected $table = 'publication_meta';

    protected $fillable = ['publication_id', 'key', 'value'];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
