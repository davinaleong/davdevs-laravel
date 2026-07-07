<?php

namespace App\Console\Commands;

use App\Services\Migration\FrontmatterParser;
use Illuminate\Console\Command;

class AuditMigrationContent extends Command
{
    protected $signature = 'migration:audit
        {--source= : Path to the old Next.js project root}
        {--report= : Path to write the Markdown report (default: _internal-docs/11-migration-audit-report.md)}';

    protected $description = 'Audit old-site Markdown content ahead of the CMS migration (10-migration-plan.md §8 step 1)';

    /** Expected content type folders per 10-migration-plan.md §2, mapped to their required frontmatter fields. */
    private array $textTypes = [
        'articles' => ['title', 'slug', 'description', 'date', 'published'],
        'projects' => ['title', 'slug', 'description', 'date', 'published'],
        'tools' => ['title', 'slug', 'description', 'date', 'published'],
        'notebooks' => ['title', 'slug', 'description', 'date', 'published'],
        'knowledge-sharing' => ['title', 'slug', 'description', 'date', 'published'],
        'fem' => ['title', 'slug', 'description', 'date', 'published'],
        'sermons' => ['title', 'slug', 'description', 'date', 'published'],
        'static' => ['title', 'slug', 'description'],
    ];

    private array $ebookRequiredFields = [
        'title', 'slug', 'description', 'author', 'publishedAt', 'coverImage', 'backImage', 'price', 'storeStatus', 'formats',
    ];

    public function handle(): int
    {
        $source = $this->option('source') ?: 'C:\\Development\\proj-davdevs-2025';
        $contentRoot = rtrim($source, '\\/').DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'content';

        if (! is_dir($contentRoot)) {
            $this->error("Content root not found: {$contentRoot}");

            return self::FAILURE;
        }

        $foundFolders = collect(glob($contentRoot.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR))
            ->map(fn ($p) => basename($p))
            ->sort()
            ->values();

        $expectedFolders = collect(array_keys($this->textTypes))->push('ebooks')->sort()->values();
        $unexpectedFolders = $foundFolders->diff($expectedFolders);
        $missingFolders = $expectedFolders->diff($foundFolders);

        $results = [];
        $tagVocabulary = [];
        $authorVocabulary = [];
        $slugMismatches = [];
        $missingFieldReport = [];
        $underscoreFiles = [];

        foreach ($foundFolders as $folder) {
            $isEbooks = $folder === 'ebooks';
            $required = $isEbooks ? $this->ebookRequiredFields : ($this->textTypes[$folder] ?? ['title', 'slug', 'description']);

            $files = collect(glob($contentRoot.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.'*.md'))->sort()->values();
            $count = 0;

            foreach ($files as $path) {
                $basename = basename($path, '.md');

                if (str_starts_with($basename, '_')) {
                    $underscoreFiles[] = "{$folder}/{$basename}.md";

                    continue;
                }

                $count++;
                $frontmatter = FrontmatterParser::parse($path)['frontmatter'];

                $missing = collect($required)->reject(fn ($field) => array_key_exists($field, $frontmatter) && $frontmatter[$field] !== null && $frontmatter[$field] !== '')->values();
                if ($missing->isNotEmpty()) {
                    $missingFieldReport[] = "{$folder}/{$basename}.md: missing ".$missing->implode(', ');
                }

                if (! $isEbooks && isset($frontmatter['slug']) && $frontmatter['slug'] !== $basename) {
                    $slugMismatches[] = "{$folder}/{$basename}.md: slug field '{$frontmatter['slug']}' != filename";
                }

                if (isset($frontmatter['tags']) && is_array($frontmatter['tags'])) {
                    foreach ($frontmatter['tags'] as $tag) {
                        $tagVocabulary[(string) $tag] = ($tagVocabulary[(string) $tag] ?? 0) + 1;
                    }
                }

                if (isset($frontmatter['author'])) {
                    $authorVocabulary[(string) $frontmatter['author']] = ($authorVocabulary[(string) $frontmatter['author']] ?? 0) + 1;
                }
            }

            $results[$folder] = [
                'planned_count' => $this->plannedCount($folder),
                'actual_count' => $count,
            ];
        }

        $report = $this->buildReport(
            $foundFolders,
            $unexpectedFolders,
            $missingFolders,
            $results,
            $missingFieldReport,
            $slugMismatches,
            $tagVocabulary,
            $authorVocabulary,
            $underscoreFiles,
        );

        $reportPath = $this->option('report') ?: base_path('_internal-docs/11-migration-audit-report.md');
        file_put_contents($reportPath, $report);

        $this->info("Audit complete. Report written to {$reportPath}");
        $this->line('');
        $this->line("Folders found: {$foundFolders->implode(', ')}");
        if ($unexpectedFolders->isNotEmpty()) {
            $this->warn("Unexpected folders (not in plan §2): {$unexpectedFolders->implode(', ')}");
        }
        if ($missingFolders->isNotEmpty()) {
            $this->warn("Planned folders not found: {$missingFolders->implode(', ')}");
        }
        $this->line('Missing-field issues: '.count($missingFieldReport));
        $this->line('Slug/filename mismatches: '.count($slugMismatches));
        $this->line('Distinct tags: '.count($tagVocabulary));
        $this->line('Underscore-prefixed files skipped (likely templates): '.count($underscoreFiles));

        return self::SUCCESS;
    }

    private function plannedCount(string $folder): ?int
    {
        return match ($folder) {
            'articles' => 24,
            'projects' => 32,
            'tools' => 13,
            'notebooks' => 27,
            'knowledge-sharing' => 4,
            'fem' => 24,
            'sermons' => 92,
            'static' => 5,
            'ebooks' => 7,
            default => null,
        };
    }

    private function buildReport(
        $foundFolders,
        $unexpectedFolders,
        $missingFolders,
        array $results,
        array $missingFieldReport,
        array $slugMismatches,
        array $tagVocabulary,
        array $authorVocabulary,
        array $underscoreFiles,
    ): string {
        $lines = [];
        $lines[] = '# Migration Content Audit Report';
        $lines[] = '';
        $lines[] = 'Generated by `php artisan migration:audit` per [10-migration-plan.md](10-migration-plan.md) §8 step 1.';
        $lines[] = '';
        $lines[] = '## Folder inventory vs. plan §2';
        $lines[] = '';
        $lines[] = '| Folder | Planned count | Actual count | Match? |';
        $lines[] = '| --- | --- | --- | --- |';
        foreach ($results as $folder => $r) {
            $planned = $r['planned_count'] ?? '—';
            $match = $r['planned_count'] === null ? '⚠️ not in plan' : ($r['planned_count'] === $r['actual_count'] ? '✅' : '❌');
            $lines[] = "| {$folder} | {$planned} | {$r['actual_count']} | {$match} |";
        }
        $lines[] = '';

        if ($unexpectedFolders->isNotEmpty()) {
            $lines[] = '### Folders present on disk but not listed in plan §2';
            $lines[] = '';
            foreach ($unexpectedFolders as $f) {
                $lines[] = "- `{$f}`";
            }
            $lines[] = '';
        }

        if ($missingFolders->isNotEmpty()) {
            $lines[] = '### Folders listed in plan §2 but not found on disk';
            $lines[] = '';
            foreach ($missingFolders as $f) {
                $lines[] = "- `{$f}`";
            }
            $lines[] = '';
        }

        if (! empty($underscoreFiles)) {
            $lines[] = '## Underscore-prefixed files (skipped as templates, not counted above)';
            $lines[] = '';
            foreach ($underscoreFiles as $f) {
                $lines[] = "- `{$f}`";
            }
            $lines[] = '';
        }

        $lines[] = '## Slug format (§7.2)';
        $lines[] = '';
        if (empty($slugMismatches)) {
            $lines[] = 'Every text-type file\'s `slug` frontmatter field matches its filename (minus `.md`). Old-site slugs are the filename itself — for dated types (articles/projects/tools/notebooks/knowledge-sharing/fem/sermons) that means `YYYYMMDD-freeform-slug`; `static` pages use a plain freeform slug with no date prefix.';
        } else {
            $lines[] = 'Mismatches found between `slug` field and filename:';
            $lines[] = '';
            foreach ($slugMismatches as $m) {
                $lines[] = "- {$m}";
            }
        }
        $lines[] = '';

        $lines[] = '## Missing/inconsistent required frontmatter fields';
        $lines[] = '';
        if (empty($missingFieldReport)) {
            $lines[] = 'None — all files have all required fields populated.';
        } else {
            foreach ($missingFieldReport as $m) {
                $lines[] = "- {$m}";
            }
        }
        $lines[] = '';

        $lines[] = '## Author field (§7.3)';
        $lines[] = '';
        $lines[] = 'Distinct `author` values found: '.count($authorVocabulary);
        $lines[] = '';
        foreach ($authorVocabulary as $author => $n) {
            $lines[] = "- `{$author}`: {$n} files";
        }
        $lines[] = '';

        $lines[] = '## Tag vocabulary (§6)';
        $lines[] = '';
        $lines[] = 'Distinct tags found: '.count($tagVocabulary);
        $lines[] = '';
        ksort($tagVocabulary, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($tagVocabulary as $tag => $n) {
            $lines[] = "- `{$tag}` ({$n})";
        }
        $lines[] = '';

        return implode("\n", $lines)."\n";
    }
}
