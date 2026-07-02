<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Layout extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'blade_component', 'description', 'preview_image_id', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function previewImage()
    {
        return $this->belongsTo(Image::class, 'preview_image_id');
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
