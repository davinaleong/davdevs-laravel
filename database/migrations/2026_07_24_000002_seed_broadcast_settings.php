<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['key' => 'broadcast_linkedin_enabled',   'value' => '0',  'cast' => 'boolean', 'group' => 'broadcast'],
            ['key' => 'broadcast_linkedin_token',     'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_linkedin_urn',       'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_facebook_enabled',   'value' => '0',  'cast' => 'boolean', 'group' => 'broadcast'],
            ['key' => 'broadcast_facebook_token',     'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_facebook_page_id',   'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_instagram_enabled',  'value' => '0',  'cast' => 'boolean', 'group' => 'broadcast'],
            ['key' => 'broadcast_instagram_token',    'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_instagram_user_id',  'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_threads_enabled',    'value' => '0',  'cast' => 'boolean', 'group' => 'broadcast'],
            ['key' => 'broadcast_threads_token',      'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
            ['key' => 'broadcast_threads_user_id',    'value' => null, 'cast' => 'string',  'group' => 'broadcast'],
        ];

        // Insert only rows whose key doesn't already exist (safe to run on both local and production)
        foreach ($rows as $row) {
            DB::table('settings')->insertOrIgnore($row);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'broadcast_linkedin_enabled',
            'broadcast_linkedin_token',
            'broadcast_linkedin_urn',
            'broadcast_facebook_enabled',
            'broadcast_facebook_token',
            'broadcast_facebook_page_id',
            'broadcast_instagram_enabled',
            'broadcast_instagram_token',
            'broadcast_instagram_user_id',
            'broadcast_threads_enabled',
            'broadcast_threads_token',
            'broadcast_threads_user_id',
        ])->delete();
    }
};
