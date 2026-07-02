<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use LogsActivity, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'cloudinary_id', 'url', 'title', 'alt', 'caption',
        'credit', 'width', 'height', 'format', 'bytes', 'qr_code',
    ];

    protected function casts(): array
    {
        return ['qr_code' => 'boolean'];
    }

    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_images')
            ->withPivot('sort_order', 'caption_override')
            ->orderByPivot('sort_order');
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_images')
            ->withPivot('sort_order', 'caption_override')
            ->orderByPivot('sort_order');
    }

    public function layouts()
    {
        return $this->hasMany(Layout::class, 'preview_image_id');
    }
}
