<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Article',           'slug' => 'article',           'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Project',           'slug' => 'project',           'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Tool',              'slug' => 'tool',              'table_target' => 'entries',      'react_island' => true,  'listed' => true],
            ['name' => 'Notebook',          'slug' => 'notebook',          'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Knowledge Sharing', 'slug' => 'knowledge-sharing', 'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Frontend Mentor',   'slug' => 'fem',               'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Sermon',            'slug' => 'sermon',            'table_target' => 'entries',      'react_island' => false, 'listed' => true],
            ['name' => 'Page',              'slug' => 'page',              'table_target' => 'entries',      'react_island' => false, 'listed' => false],
        ];

        foreach ($types as $type) {
            ContentType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
