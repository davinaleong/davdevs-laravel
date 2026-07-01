<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    public function run(): void
    {
        $layouts = [
            ['name' => 'Standard',    'slug' => 'standard',    'blade_component' => 'layouts.standard'],
            ['name' => 'Product',     'slug' => 'product',     'blade_component' => 'layouts.product'],
            ['name' => 'Tool',        'slug' => 'tool',        'blade_component' => 'layouts.tool'],
            ['name' => 'Sermon',      'slug' => 'sermon',      'blade_component' => 'layouts.sermon'],
            ['name' => 'Publication', 'slug' => 'publication', 'blade_component' => 'layouts.publication'],
        ];

        foreach ($layouts as $layout) {
            Layout::firstOrCreate(['slug' => $layout['slug']], $layout);
        }
    }
}
