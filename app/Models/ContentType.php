<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'table_target', 'react_island', 'listed', 'show_price', 'description'];

    protected function casts(): array
    {
        return ['react_island' => 'boolean', 'listed' => 'boolean', 'show_price' => 'boolean'];
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
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
