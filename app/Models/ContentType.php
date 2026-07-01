<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'table_target', 'react_island', 'listed', 'description'];

    protected function casts(): array
    {
        return ['react_island' => 'boolean', 'listed' => 'boolean'];
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
