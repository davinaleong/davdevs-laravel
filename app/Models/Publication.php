<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class Publication extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'ulid', 'layout_id', 'category_id', 'og_image_id', 'cover_image_id',
        'publication_type', 'title', 'slug', 'tagline', 'excerpt', 'body',
        'status', 'featured', 'bundle', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured'     => 'boolean',
            'bundle'       => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Publication $pub) {
            if (empty($pub->ulid)) {
                $pub->ulid = (string) Str::ulid();
            }
        });
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ogImage()
    {
        return $this->belongsTo(Image::class, 'og_image_id');
    }

    public function coverImage()
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    public function store()
    {
        return $this->hasOne(PublicationStore::class);
    }

    public function meta()
    {
        return $this->hasMany(PublicationMeta::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'publication_images')
            ->withPivot('sort_order', 'caption_override')
            ->orderByPivot('sort_order');
    }

    public function links()
    {
        return $this->belongsToMany(Link::class, 'publication_links')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'publication_tags');
    }

    public function bundleMembers()
    {
        return $this->belongsToMany(Publication::class, 'publication_bundles', 'bundle_id', 'member_id')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function bundleOf()
    {
        return $this->belongsToMany(Publication::class, 'publication_bundles', 'member_id', 'bundle_id');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
