<?php

namespace App\Console\Commands;

use App\Models\ContentType;
use App\Models\Publication;
use Illuminate\Console\Command;

class AssignPublicationContentTypes extends Command
{
    protected $signature = 'publications:assign-content-type
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Audit existing publications and assign content_type_id based on their publication_type column.
                              Defaults: publication_type=ebook → "ebook" ContentType.';

    /** Map publication_type enum values → ContentType slug */
    private array $typeMap = [
        'ebook' => 'ebook',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        // Resolve ContentType records up front
        $contentTypes = ContentType::where('table_target', 'publications')
            ->whereIn('slug', array_values($this->typeMap))
            ->pluck('id', 'slug');

        if ($contentTypes->isEmpty()) {
            $this->error('No publication ContentTypes found. Run php artisan db:seed --class=PublicationTypeSeeder first.');
            return self::FAILURE;
        }

        $this->line('Resolved content types:');
        foreach ($contentTypes as $slug => $id) {
            $this->line("  {$slug} → id {$id}");
        }
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $unknown = 0;

        Publication::withTrashed()
            ->whereNull('content_type_id')
            ->orderBy('id')
            ->chunk(100, function ($publications) use ($contentTypes, $dryRun, &$updated, &$skipped, &$unknown) {
                foreach ($publications as $pub) {
                    $targetSlug = $this->typeMap[$pub->publication_type] ?? null;

                    if (! $targetSlug || ! isset($contentTypes[$targetSlug])) {
                        $this->warn("  Publication #{$pub->id} \"{$pub->title}\" — unknown publication_type \"{$pub->publication_type}\", skipping.");
                        $unknown++;
                        continue;
                    }

                    $ctId = $contentTypes[$targetSlug];
                    $action = $dryRun ? '[DRY RUN]' : 'Updated';
                    $this->line("  {$action} Publication #{$pub->id} \"{$pub->title}\" → {$targetSlug} (content_type_id={$ctId})");

                    if (! $dryRun) {
                        $pub->updateQuietly(['content_type_id' => $ctId]);
                    }

                    $updated++;
                }
            });

        $this->newLine();
        $this->info("Done. Updated: {$updated} | Skipped (unknown type): {$unknown}");

        if ($dryRun) {
            $this->warn('Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }
}
