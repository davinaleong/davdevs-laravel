<?php

namespace App\Console\Commands;

use App\Models\PublicationTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncPublicationTemplates extends Command
{
    protected $signature = 'publication-templates:sync';

    protected $description = 'Scan resources/views/publications/templates/<publication_type>/*.blade.php and sync the publication_templates registry (07-db-schemas-v2.md §10.2)';

    public function handle(): int
    {
        $templatesPath = resource_path('views/publications/templates');
        $typeDirs = collect(glob($templatesPath.'/*', GLOB_ONLYDIR));

        $seen = [];

        foreach ($typeDirs as $typeDir) {
            $publicationType = basename($typeDir);
            $files = collect(glob($typeDir.'/*.blade.php'));

            foreach ($files as $file) {
                $stem = basename($file, '.blade.php');
                $slug = Str::kebab($stem);
                $seen[] = [$publicationType, $slug];

                $bladePath = 'publications.templates.'.$publicationType.'.'.$stem;

                PublicationTemplate::updateOrCreate(
                    ['publication_type' => $publicationType, 'slug' => $slug],
                    [
                        'name' => Str::headline($stem),
                        'blade_path' => $bladePath,
                        'active' => true,
                    ]
                );
            }
        }

        $deactivated = 0;
        foreach (PublicationTemplate::where('active', true)->get() as $template) {
            if (! collect($seen)->contains(fn ($pair) => $pair[0] === $template->publication_type && $pair[1] === $template->slug)) {
                $template->update(['active' => false]);
                $deactivated++;
            }
        }

        $this->info(sprintf('Synced %d template(s), deactivated %d missing file(s).', count($seen), $deactivated));

        return self::SUCCESS;
    }
}
