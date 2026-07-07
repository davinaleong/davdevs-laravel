<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReactComponent extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'file_path', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
