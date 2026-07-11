<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\SocialBroadcast;
use Illuminate\Http\Request;

class BroadcastController extends Controller
{
    public function index(Request $request)
    {
        $query = SocialBroadcast::with('broadcastable')->orderByDesc('created_at');

        if ($request->filled('platform')) {
            $query->where('platform', $request->string('platform'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $broadcasts = $query->paginate(30)->withQueryString();

        return view('panel.broadcasts', compact('broadcasts'));
    }
}
