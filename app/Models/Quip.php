<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quip extends Model
{
    use LogsActivity, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['variant', 'question', 'punchline', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
