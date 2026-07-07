<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;

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
        // public/books/** (ebooks) is excluded — ebooks are deferred. Extension match is
        // case-insensitive on disk (a handful of files use .PNG), so filter manually rather
        // than relying on glob()'s case sensitivity.
        $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
        $files = collect(glob($publicRoot.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR))
            ->reject(fn ($dir) => basename($dir) === 'books')
            ->flatMap(fn ($dir) => glob($dir.DIRECTORY_SEPARATOR.'*.*'))
            ->filter(fn ($path) => in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions))
            ->values();

        if ($files->isEmpty()) {
            $this->error('No images found (or all were under public/books/, which is excluded — ebooks are deferred).');

            return self::FAILURE;
        }

        $map = [];
        $bar = $this->output->createProgressBar($files->count());
        $bar->start();

        foreach ($files as $path) {
            $relative = str_replace('\\', '/', str_replace($publicRoot.DIRECTORY_SEPARATOR, '', $path));

            if ($dryRun) {
                $map[$relative] = null;
                $bar->advance();

                continue;
            }

            $upload = $cloudinary->upload(new UploadedFile($path, basename($path), null, null, true));

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
        $this->line('Sample paths: '.$files->take(5)->map(fn ($p) => str_replace($publicRoot.DIRECTORY_SEPARATOR, '', $p))->implode(', '));

        return self::SUCCESS;
    }
}
