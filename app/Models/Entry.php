<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Entry extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'ulid', 'content_type_id', 'category_id', 'og_image_id', 'react_component_id',
        'title', 'slug', 'excerpt', 'body', 'read_time', 'status',
        'visibility', 'featured', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Entry $entry) {
            if (empty($entry->ulid)) {
                $entry->ulid = (string) Str::ulid();
            }
        });

        static::saving(function (Entry $entry) {
            if ($entry->body) {
                $wordCount = str_word_count(strip_tags($entry->body));
                $entry->read_time = max(1, (int) ceil($wordCount / 200));
            }
        });
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function reactComponent()
    {
        return $this->belongsTo(ReactComponent::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ogImage()
    {
        return $this->belongsTo(Image::class, 'og_image_id');
    }

    public function meta()
    {
        return $this->hasMany(EntryMeta::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'entry_images')
            ->withPivot('sort_order', 'caption_override')
            ->orderByPivot('sort_order');
    }

    public function videoEmbeds()
    {
        return $this->belongsToMany(VideoEmbed::class, 'entry_video_embeds')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function links()
    {
        return $this->belongsToMany(Link::class, 'entry_links')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'entry_tags');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function getMeta(string $key): ?string
    {
        return $this->meta()->where('key', $key)->value('value');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
