<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'label', 'url', 'target', 'rel', 'description',
        'category', 'icon_class', 'sort_order', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function entries()
    {
        return $this->belongsToMany(Entry::class, 'entry_links')->withPivot('sort_order');
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'publication_links')->withPivot('sort_order');
    }
}
