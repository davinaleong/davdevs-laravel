<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Entry;
use App\Models\Publication;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'entries'      => Entry::count(),
            'publications' => Publication::count(),
            'published'    => Entry::where('status', 'published')->count(),
            'drafts'       => Entry::where('status', 'draft')->count(),
        ];

        $recentLogs = ActivityLog::latest('created_at')->limit(10)->get();

        return view('panel.dashboard', compact('stats', 'recentLogs'));
    }
}
