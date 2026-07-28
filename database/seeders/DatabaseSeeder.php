<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ContentTypeSeeder::class,
            PublicationTypeSeeder::class,
            LayoutSeeder::class,
            SettingSeeder::class,
            QuipSeeder::class,
        ]);
    }
}
