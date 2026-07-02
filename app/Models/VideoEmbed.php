<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class VideoEmbed extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'video_id', 'title', 'description', 'channel_name',
        'thumbnail_url', 'duration_seconds', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'date'];
    }

    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_video_embeds')->withPivot('sort_order');
    }
}
