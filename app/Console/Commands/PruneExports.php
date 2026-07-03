<?php

namespace App\Console\Commands;

use App\Models\ExportJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneExports extends Command
{
    protected $signature = 'exports:prune';

    protected $description = 'Delete expired export files and mark jobs failed/expired';

    public function handle(): int
    {
        $expired = ExportJob::where('expires_at', '<', now())->where('status', 'complete')->get();

        foreach ($expired as $job) {
            if ($job->download_url) {
                Storage::disk('local')->delete($job->download_url);
            }
            $job->delete();
        }

        $this->info("Pruned {$expired->count()} expired export jobs.");

        return self::SUCCESS;
    }
}
