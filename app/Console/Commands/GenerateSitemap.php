<?php

namespace App\Console\Commands;

use App\Models\ContentType;
use App\Models\Entry;
use App\Models\Publication;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Regenerate the public sitemap.xml (excludes jokes/quips and unlisted content types)';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create(route('home'))->setPriority(1.0));
        $sitemap->add(Url::create(route('site.ebooks'))->setPriority(0.8));

        foreach (ContentType::where('listed', true)->get() as $type) {
            $sitemap->add(Url::create(route('site.listing', $type->slug))->setPriority(0.7));

            Entry::published()
                ->where('content_type_id', $type->id)
                ->where('visibility', 'public')
                ->chunk(200, function ($entries) use ($sitemap, $type) {
                    foreach ($entries as $entry) {
                        $sitemap->add(
                            Url::create(route('site.show', [$type->slug, $entry->slug]))
                                ->setLastModificationDate($entry->updated_at)
                                ->setPriority(0.6)
                        );
                    }
                });
        }

        Publication::published()->chunk(200, function ($publications) use ($sitemap) {
            foreach ($publications as $pub) {
                $sitemap->add(
                    Url::create(route('site.ebooks.show', $pub->slug))
                        ->setLastModificationDate($pub->updated_at)
                        ->setPriority(0.6)
                );
            }
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated: '.public_path('sitemap.xml'));

        return self::SUCCESS;
    }
}
