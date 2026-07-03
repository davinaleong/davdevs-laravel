<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Jobs\BuildDataExport;
use App\Models\ExportJob;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index()
    {
        $jobs = ExportJob::orderByDesc('created_at')->limit(20)->get();

        return view('panel.exports', compact('jobs'));
    }

    public function store()
    {
        $job = ExportJob::create(['status' => 'queued']);

        BuildDataExport::dispatch($job->id);

        return redirect()->route('panel.exports.index')->with('success', 'Export queued — refresh in a moment.');
    }

    public function download(ExportJob $exportJob)
    {
        abort_unless($exportJob->status === 'complete' && $exportJob->download_url, 404);
        abort_if($exportJob->expires_at && $exportJob->expires_at->isPast(), 410, 'This export has expired.');
        abort_unless(Storage::disk('local')->exists($exportJob->download_url), 404);

        return Storage::disk('local')->download($exportJob->download_url, 'davdevs-export.zip');
    }
}
