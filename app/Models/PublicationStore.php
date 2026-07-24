<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationStore extends Model
{
    public $timestamps = false;

    protected $table = 'publication_store';

    protected $fillable = [
        'publication_id',
        'ls_store_url', 'price_display', 'currency', 'free_sample_url',
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
