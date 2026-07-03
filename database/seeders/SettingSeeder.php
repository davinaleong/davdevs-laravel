<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // display
            ['key' => 'frontend_date_format', 'value' => 'D MMM YYYY', 'cast' => 'string', 'group' => 'display'],
            ['key' => 'cms_date_format',       'value' => 'YYYY-MM-DD', 'cast' => 'string', 'group' => 'display'],
            // header
            ['key' => 'brand_name',            'value' => 'Dav/Devs',   'cast' => 'string', 'group' => 'header'],
            ['key' => 'brand_image_id',        'value' => null,          'cast' => 'integer', 'group' => 'header'],
            // footer
            ['key' => 'copyright_text',        'value' => '© {year} Davina Leong', 'cast' => 'string', 'group' => 'footer'],
            // lighthouse
            ['key' => 'lh_show_performance',   'value' => '1',  'cast' => 'boolean', 'group' => 'lighthouse'],
            ['key' => 'lh_show_accessibility', 'value' => '1',  'cast' => 'boolean', 'group' => 'lighthouse'],
            ['key' => 'lh_show_seo',           'value' => '1',  'cast' => 'boolean', 'group' => 'lighthouse'],
            ['key' => 'lh_show_best_practices', 'value' => '1',  'cast' => 'boolean', 'group' => 'lighthouse'],
            ['key' => 'lh_performance',        'value' => null, 'cast' => 'integer', 'group' => 'lighthouse'],
            ['key' => 'lh_accessibility',      'value' => null, 'cast' => 'integer', 'group' => 'lighthouse'],
            ['key' => 'lh_seo',                'value' => null, 'cast' => 'integer', 'group' => 'lighthouse'],
            ['key' => 'lh_best_practices',     'value' => null, 'cast' => 'integer', 'group' => 'lighthouse'],
            // api
            ['key' => 'api_enabled',           'value' => '0',  'cast' => 'boolean', 'group' => 'api'],
            // ai (bonus)
            ['key' => 'ai_api_key',            'value' => null, 'cast' => 'string', 'group' => 'ai'],
            ['key' => 'ai_model',              'value' => 'gpt-4o', 'cast' => 'string', 'group' => 'ai'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
