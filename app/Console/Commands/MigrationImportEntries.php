<?php

namespace App\Console\Commands;

use App\Models\ContentType;
use App\Models\Entry;
use App\Models\EntryMeta;
use App\Models\Image;
use App\Models\Layout;
use App\Models\Link;
use App\Models\Tag;
use App\Services\Migration\FrontmatterParser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MigrationImportEntries extends Command
{
    protected $signature = 'migration:import-entries
        {--source= : Path to the old Next.js project root}
        {--image-map= : Path to the images map produced by migration:import-images (default: storage/app/migration/image-map.json)}
        {--dry-run : Resolve and validate without writing to the database}';

    protected $description = 'Import old-site Markdown content into the entries table (10-migration-plan.md §8 step 4), excluding technical-demos and deferred ebooks';

    /** Old folder => content_types.slug, per plan §2 / ContentTypeSeeder. Deliberately excludes technical-demos and ebooks. */
    private array $folderToContentType = [
        'articles' => 'article',
        'projects' => 'project',
        'tools' => 'tool',
        'notebooks' => 'notebook',
        'knowledge-sharing' => 'knowledge-sharing',
        'fem' => 'fem',
        'sermons' => 'sermon',
        'static' => 'page',
    ];

    /** content_types.slug => layouts.slug. Everything not listed falls back to 'standard'. */
    private array $contentTypeToLayout = [
        'sermon' => 'sermon',
        'tool' => 'tool',
    ];

    private array $imageMap = [];

    private array $stats = [];

    public function handle(): int
    {
        $source = $this->option('source') ?: 'C:\\Development\\proj-davdevs-2025';
        $contentRoot = rtrim($source, '\\/').DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'content';
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($contentRoot)) {
            $this->error("Content root not found: {$contentRoot}");

            return self::FAILURE;
        }

        $imageMapPath = $this->option('image-map') ?: storage_path('app/migration/image-map.json');
        if (is_file($imageMapPath)) {
            $this->imageMap = json_decode(file_get_contents($imageMapPath), true) ?: [];
        } else {
            $this->warn("No image map found at {$imageMapPath} — images will be left unresolved.");
        }

        $contentTypes = ContentType::pluck('id', 'slug');
        $layouts = Layout::pluck('id', 'slug');

        foreach ($this->folderToContentType as $folder => $typeSlug) {
            $folderPath = $contentRoot.DIRECTORY_SEPARATOR.$folder;
            if (! is_dir($folderPath)) {
                $this->warn("Expected folder not found, skipping: {$folder}");

                continue;
            }

            if (! isset($contentTypes[$typeSlug])) {
                $this->error("content_types row missing for slug '{$typeSlug}' — run ContentTypeSeeder first.");

                return self::FAILURE;
            }

            $layoutSlug = $this->contentTypeToLayout[$typeSlug] ?? 'standard';
            if (! isset($layouts[$layoutSlug])) {
                $this->error("layouts row missing for slug '{$layoutSlug}' — run LayoutSeeder first.");

                return self::FAILURE;
            }

            $this->importFolder($folder, $typeSlug, $contentTypes[$typeSlug], $layouts[$layoutSlug], $dryRun);
        }

        $this->newLine();
        $this->table(
            ['Folder', 'Imported', 'Skipped (already imported)', 'Unresolved images'],
            collect($this->stats)->map(fn ($s, $folder) => [$folder, $s['imported'], $s['skipped'], $s['unresolved_images']])->values()
        );

        return self::SUCCESS;
    }

    private function importFolder(string $folder, string $typeSlug, int $contentTypeId, int $layoutId, bool $dryRun): void
    {
        $this->stats[$folder] = ['imported' => 0, 'skipped' => 0, 'unresolved_images' => 0];

        $paths = collect(glob($this->folderGlob($folder)))->sort()->values();

        foreach ($paths as $path) {
            $basename = basename($path, '.md');
            if (str_starts_with($basename, '_')) {
                continue;
            }

            $source = "{$folder}/{$basename}.md";

            if (! $dryRun && EntryMeta::where('key', 'migration_source')->where('value', $source)->exists()) {
                $this->stats[$folder]['skipped']++;

                continue;
            }

            ['frontmatter' => $fm, 'body' => $body] = FrontmatterParser::parse($path);

            $title = $fm['title'] ?? $basename;
            $publishedAt = ! empty($fm['date']) ? Carbon::parse($fm['date']) : null;
            $isPage = $typeSlug === 'page';

            $slug = $this->uniqueSlug($title, $isPage ? null : $publishedAt, $dryRun);

            [$resolvedBody, $unresolvedInBody] = $this->resolveInlineImages($body, $folder);

            $entryData = [
                'content_type_id' => $contentTypeId,
                'layout_id' => $layoutId,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $fm['description'] ?? null,
                'body' => $resolvedBody,
                'status' => ($fm['published'] ?? true) ? 'published' : 'draft',
                'featured' => (bool) ($fm['featured'] ?? false),
                'published_at' => $publishedAt,
            ];

            $unresolvedImages = $unresolvedInBody;

            if ($dryRun) {
                foreach (($fm['images'] ?? []) as $img) {
                    if (! $this->imagePathKnown($img['src'] ?? '', $folder)) {
                        $unresolvedImages++;
                    }
                }
                $this->stats[$folder]['imported']++;
                $this->stats[$folder]['unresolved_images'] += $unresolvedImages;

                continue;
            }

            $entry = Entry::create($entryData);

            EntryMeta::create(['entry_id' => $entry->id, 'key' => 'migration_source', 'value' => $source]);

            $this->attachTags($entry, $typeSlug, $contentTypeId, $fm['tags'] ?? []);
            $unresolvedImages += $this->attachImages($entry, $folder, $fm['images'] ?? []);
            $this->attachLinks($entry, $fm['links'] ?? []);

            $this->stats[$folder]['imported']++;
            $this->stats[$folder]['unresolved_images'] += $unresolvedImages;
        }
    }

    private function folderGlob(string $folder): string
    {
        $source = $this->option('source') ?: 'C:\\Development\\proj-davdevs-2025';

        return rtrim($source, '\\/').DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.'*.md';
    }

    private function uniqueSlug(string $title, ?Carbon $date, bool $dryRun): string
    {
        $base = $date ? $date->format('Ymd').'-'.Str::slug($title) : Str::slug($title);
        $slug = $base;

        if ($dryRun) {
            return $slug;
        }

        $i = 1;
        while (Entry::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** @return array{0: string, 1: int} [rewritten body, unresolved inline image count] */
    private function resolveInlineImages(string $body, string $folder): array
    {
        $unresolved = 0;

        $rewritten = preg_replace_callback('/!\[([^\]]*)\]\(([^)\s]+)\)/', function ($m) use ($folder, &$unresolved) {
            [$whole, $alt, $src] = $m;

            if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
                return $whole;
            }

            if (! $this->imagePathKnown($src, $folder)) {
                $unresolved++;

                return $whole;
            }

            $imageId = $this->resolveImageId($src, $folder);
            $url = $imageId ? Image::find($imageId)?->url : null;

            return $url ? "![{$alt}]({$url})" : $whole;
        }, $body);

        return [$rewritten, $unresolved];
    }

    private function imageKey(string $src, string $folder): ?string
    {
        if ($src === '') {
            return null;
        }

        $key = ltrim($src, '/');
        if (! str_starts_with($key, "{$folder}/")) {
            $key = "{$folder}/".basename($key);
        }

        return $key;
    }

    /** Whether this old-site path was found by migration:import-images, regardless of whether it has an uploaded ID yet (dry-run maps store null placeholders). */
    private function imagePathKnown(string $src, string $folder): bool
    {
        $key = $this->imageKey($src, $folder);

        return $key !== null && array_key_exists($key, $this->imageMap);
    }

    private function resolveImageId(string $src, string $folder): ?int
    {
        $key = $this->imageKey($src, $folder);

        return $key !== null ? ($this->imageMap[$key] ?? null) : null;
    }

    private function attachTags(Entry $entry, string $typeSlug, int $contentTypeId, array $tags): void
    {
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tagName = trim((string) $tagName);
            if ($tagName === '') {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['content_type_id' => $contentTypeId, 'slug' => Str::slug($tagName)],
                ['name' => $tagName, 'scope' => 'entries']
            );

            $tagIds[] = $tag->id;
        }

        $entry->tags()->sync($tagIds);
    }

    private function attachImages(Entry $entry, string $folder, array $images): int
    {
        $unresolved = 0;
        $sortOrder = 0;

        foreach ($images as $img) {
            $imageId = $this->resolveImageId($img['src'] ?? '', $folder);

            if ($imageId === null) {
                $unresolved++;

                continue;
            }

            if (! $entry->images()->where('image_id', $imageId)->exists()) {
                $entry->images()->attach($imageId, [
                    'sort_order' => $sortOrder,
                    'caption_override' => $img['alt'] ?? null,
                ]);
            }

            $sortOrder++;
        }

        return $unresolved;
    }

    private function attachLinks(Entry $entry, array $links): void
    {
        $sortOrder = 0;

        foreach ($links as $link) {
            $url = $link['href'] ?? null;
            if (! $url) {
                continue;
            }

            $linkModel = Link::firstOrCreate(
                ['url' => $url],
                ['label' => $link['label'] ?? $url, 'target' => '_blank', 'active' => true]
            );

            $entry->links()->attach($linkModel->id, ['sort_order' => $sortOrder]);
            $sortOrder++;
        }
    }
}
