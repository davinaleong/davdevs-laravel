<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['content_type_id', 'scope', 'name', 'slug', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}
