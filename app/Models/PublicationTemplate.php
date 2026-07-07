<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationTemplate extends Model
{
    public $timestamps = false;

    protected $fillable = ['publication_type', 'name', 'slug', 'blade_path', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function publications()
    {
        return $this->hasMany(Publication::class, 'publication_template_id');
    }
}
