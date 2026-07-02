<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLog extends Command
{
    protected $signature = 'logs:prune';

    protected $description = 'Delete activity_log entries older than 90 days';

    public function handle(): int
    {
        $deleted = ActivityLog::where('created_at', '<', now()->subDays(90))->delete();

        $this->info("Pruned {$deleted} log entries older than 90 days.");

        return self::SUCCESS;
    }
}
