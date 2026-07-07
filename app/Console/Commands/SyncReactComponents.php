<?php

namespace App\Console\Commands;

use App\Models\ReactComponent;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncReactComponents extends Command
{
    protected $signature = 'react-components:sync';

    protected $description = 'Scan resources/js/components/*.{jsx,tsx} and sync the react_components registry (10-migration-plan.md §10.1)';

    public function handle(): int
    {
        $componentsPath = resource_path('js/components');
        $files = collect(glob($componentsPath.'/*.{jsx,tsx}', GLOB_BRACE));

        $seenSlugs = [];

        foreach ($files as $file) {
            $stem = pathinfo($file, PATHINFO_FILENAME);
            $slug = Str::kebab($stem);
            $seenSlugs[] = $slug;

            ReactComponent::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $stem,
                    'file_path' => basename($file),
                    'active' => true,
                ]
            );
        }

        $deactivated = ReactComponent::whereNotIn('slug', $seenSlugs)->where('active', true)->update(['active' => false]);

        $this->info(sprintf('Synced %d component(s), deactivated %d missing file(s).', count($seenSlugs), $deactivated));

        return self::SUCCESS;
    }
}
