<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['publication_id', 'name', 'price_display', 'sort_order'];

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
