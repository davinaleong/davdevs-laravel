<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Entry;
use App\Models\ExportJob;
use App\Models\Image;
use App\Models\Link;
use App\Models\Publication;
use App\Models\Quip;
use App\Models\Setting;
use App\Models\VideoEmbed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BuildDataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public int $exportJobId) {}

    public function handle(): void
    {
        $job = ExportJob::findOrFail($this->exportJobId);
        $job->update(['status' => 'processing']);

        try {
            $files = [
                'entries.json' => Entry::with(['meta', 'tags', 'category', 'contentType'])->get(),
                'publications.json' => Publication::with(['meta', 'tags', 'store'])->get(),
                'images.json' => Image::all(),
                'links.json' => Link::all(),
                'youtube_embeds.json' => VideoEmbed::all(),
                'quips.json' => Quip::all(),
                'settings.json' => Setting::all(),
                'logs.json' => ActivityLog::where('created_at', '>=', now()->subDays(90))->get(),
            ];

            $filename = 'exports/export-'.now()->format('Ymd-His').'-'.uniqid().'.zip';
            $fullPath = Storage::disk('local')->path($filename);
            Storage::disk('local')->makeDirectory('exports');

            $zip = new ZipArchive;
            $zip->open($fullPath, ZipArchive::CREATE);

            foreach ($files as $name => $collection) {
                $zip->addFromString($name, $collection->toJson(JSON_PRETTY_PRINT));
            }

            $zip->close();

            $job->update([
                'status' => 'complete',
                'download_url' => $filename, // storage-relative path; served via a protected /panel route, never public
                'expires_at' => now()->addDay(),
            ]);

            ActivityLog::create(['channel' => 'export', 'level' => 'info', 'message' => "Export job #{$job->id} completed"]);
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            ActivityLog::create(['channel' => 'export', 'level' => 'error', 'message' => "Export job #{$job->id} failed: {$e->getMessage()}"]);
        }
    }
}
