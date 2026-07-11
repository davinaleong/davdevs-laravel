<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key'   => 'favicon_image_id',
            'value' => null,
            'cast'  => 'integer',
            'group' => 'display',
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'favicon_image_id')->delete();
    }
};
