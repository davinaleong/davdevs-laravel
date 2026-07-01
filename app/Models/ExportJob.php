<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $fillable = ['status', 'download_url', 'expires_at', 'error_message'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
}
