<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Seeder;

class PublicationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'         => 'Ebook',
                'slug'         => 'ebook',
                'table_target' => 'publications',
                'react_island' => false,
                'listed'       => true,
                'show_price'   => true,
                'description'  => 'Digital books available for purchase or download.',
            ],
            [
                'name'         => 'Course',
                'slug'         => 'course',
                'table_target' => 'publications',
                'react_island' => false,
                'listed'       => true,
                'show_price'   => true,
                'description'  => 'Structured learning materials and video courses.',
            ],
            [
                'name'         => 'Template',
                'slug'         => 'template',
                'table_target' => 'publications',
                'react_island' => false,
                'listed'       => true,
                'show_price'   => true,
                'description'  => 'Downloadable design or code templates.',
            ],
            [
                'name'         => 'Free Resource',
                'slug'         => 'free-resource',
                'table_target' => 'publications',
                'react_island' => false,
                'listed'       => true,
                'show_price'   => false,
                'description'  => 'Free downloadable resources, checklists, and guides.',
            ],
        ];

        foreach ($types as $type) {
            ContentType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
