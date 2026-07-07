<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // cursorPaginate requires the sort column(s) to be unique, or tied rows get
        // silently dropped at page boundaries — id is appended as a tiebreaker
        // (multiple log rows can share the same created_at second).
        $query = ActivityLog::query()->orderByDesc('created_at')->orderByDesc('id');

        if ($request->filled('channel')) {
            $query->where('channel', $request->string('channel'));
        }
        if ($request->filled('level')) {
            $query->where('level', $request->string('level'));
        }
        if ($request->filled('search')) {
            $query->where('message', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $logs = $query->cursorPaginate(50)->withQueryString();

        $channels = ActivityLog::select('channel')->distinct()->pluck('channel');

        return view('panel.logs', compact('logs', 'channels'));
    }
}
