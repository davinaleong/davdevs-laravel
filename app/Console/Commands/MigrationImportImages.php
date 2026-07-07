<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\CloudinaryService;
use App\Services\Migration\ContentTypeMap;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MigrationImportImages extends Command
{
    protected $signature = 'migration:import-images
        {--source= : Path to the old Next.js project root}
        {--map= : Path to write the old-path -> images.id JSON map (default: storage/app/migration/image-map.json)}
        {--dry-run : Walk and report without uploading to Cloudinary or writing to the database}';

    protected $description = 'Upload old-site images to Cloudinary and seed the images table (10-migration-plan.md §5), excluding deferred ebooks';

    public function handle(CloudinaryService $cloudinary): int
    {
        $source = $this->option('source') ?: 'C:\\Development\\proj-davdevs-2025';
        $publicRoot = rtrim($source, '\\/').DIRECTORY_SEPARATOR.'public';
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($publicRoot)) {
            $this->error("Public root not found: {$publicRoot}");

            return self::FAILURE;
        }

        // Old site's images live exactly one level deep: public/<content-folder>/<file>.
        // Only folders in ContentTypeMap::FOLDERS are considered — this naturally excludes
        // public/books/** (ebooks, deferred) and anything under technical-demos. Extension
        // match is case-insensitive on disk (a handful of files use .PNG), so filter
        // manually rather than relying on glob()'s case sensitivity.
        $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
        $files = collect(ContentTypeMap::FOLDERS)
            ->keys()
            ->filter(fn ($folder) => is_dir($publicRoot.DIRECTORY_SEPARATOR.$folder))
            ->flatMap(fn ($folder) => collect(glob($publicRoot.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.'*.*'))
                ->map(fn ($path) => ['folder' => $folder, 'path' => $path]))
            ->filter(fn ($f) => in_array(strtolower(pathinfo($f['path'], PATHINFO_EXTENSION)), $extensions))
            ->values();

        if ($files->isEmpty()) {
            $this->error('No images found under the known content folders.');

            return self::FAILURE;
        }

        $map = [];
        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        foreach ($files as $file) {
            ['folder' => $folder, 'path' => $path] = $file;
            $relative = "{$folder}/".basename($path);
            $typeSlug = ContentTypeMap::FOLDERS[$folder];
            $basename = pathinfo($path, PATHINFO_FILENAME);
            // e.g. davdevs/entries/article/20231229-0001-delving-into-ai — identifiable in the
            // Cloudinary console by content type + the old site's own date/slug-bearing filename,
            // with no need to cross-reference the database.
            $publicId = "entries/{$typeSlug}/".Str::slug($basename);

            if ($dryRun) {
                $map[$relative] = null;
                $bar->advance();

                continue;
            }

            $upload = $cloudinary->upload(new UploadedFile($path, basename($path), null, null, true), publicId: $publicId);

            $image = Image::create([
                'cloudinary_id' => $upload['cloudinary_id'],
                'url' => $upload['url'],
                'width' => $upload['width'],
                'height' => $upload['height'],
                'format' => $upload['format'],
                'bytes' => $upload['bytes'],
            ]);

            $map[$relative] = $image->id;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $mapPath = $this->option('map') ?: storage_path('app/migration/image-map.json');
        if (! is_dir(dirname($mapPath))) {
            mkdir(dirname($mapPath), 0755, true);
        }
        file_put_contents($mapPath, json_encode($map, JSON_PRETTY_PRINT));

        $this->info(($dryRun ? '[dry-run] ' : '')."Processed {$files->count()} images. Map written to {$mapPath}.");
        $this->line('Sample old paths: '.$files->take(3)->map(fn ($f) => "{$f['folder']}/".basename($f['path']))->implode(', '));
        $this->line('Sample Cloudinary public IDs: '.$files->take(3)->map(fn ($f) => 'davdevs/entries/'.ContentTypeMap::FOLDERS[$f['folder']].'/'.Str::slug(pathinfo($f['path'], PATHINFO_FILENAME)))->implode(', '));

        return self::SUCCESS;
    }
}
