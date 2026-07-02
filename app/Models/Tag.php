<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = ['content_type_id', 'scope', 'name', 'slug'];

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_tags');
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_tags');
    }
}
