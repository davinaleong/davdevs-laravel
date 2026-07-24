<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Jobs\BroadcastPost;
use App\Models\Entry;
use App\Models\Publication;
use App\Models\Setting;
use App\Models\SocialBroadcast;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Http\Request;
use Throwable;

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

        $entries      = Entry::where('status', 'published')->orderByDesc('published_at')->get(['id', 'title']);
        $publications = Publication::where('status', 'published')->orderByDesc('published_at')->get(['id', 'title']);

        $broadcastSettings = Setting::whereIn('key', [
            'broadcast_linkedin_enabled',
            'broadcast_facebook_enabled',
            'broadcast_instagram_enabled',
            'broadcast_threads_enabled',
        ])->pluck('value', 'key');

        $enabledPlatforms = collect(['linkedin', 'facebook', 'instagram', 'threads'])
            ->filter(fn ($p) => filter_var($broadcastSettings["broadcast_{$p}_enabled"] ?? '0', FILTER_VALIDATE_BOOLEAN))
            ->values()
            ->all();

        return view('panel.broadcasts', compact('broadcasts', 'entries', 'publications', 'enabledPlatforms'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'broadcastable_type'   => 'required|in:entry,publication',
            'broadcastable_id'     => 'required|integer',
            'platform_text'        => 'required|array|min:1',
            'platform_text.*'      => 'required|string|max:5000',
        ]);

        $model = $data['broadcastable_type'] === 'entry'
            ? Entry::with(['contentType', 'ogImage'])->findOrFail($data['broadcastable_id'])
            : Publication::with(['ogImage', 'coverImage'])->findOrFail($data['broadcastable_id']);

        abort_unless($model->status === 'published', 422, 'Only published content can be broadcast.');

        $url = $model instanceof Entry
            ? route('site.show', [$model->contentType->slug, $model->slug])
            : route('site.ebooks.show', $model->slug);

        $imageUrl = $model instanceof Entry
            ? $model->ogImage?->url
            : ($model->ogImage?->url ?? $model->coverImage?->url);

        // Sanity-check and auto-correct each platform text
        $provider      = AiProviderFactory::make();
        $platformTexts = [];
        $corrections   = [];

        foreach ($data['platform_text'] as $platform => $text) {
            try {
                $result = $provider->sanitizeBroadcast($platform, $text);
                $platformTexts[$platform] = $result['corrected'];
                if ($result['changed']) {
                    $corrections[$platform] = $result['corrected'];
                }
            } catch (Throwable) {
                // If AI sanity-check fails, use original text and continue
                $platformTexts[$platform] = $text;
            }
        }

        BroadcastPost::dispatch($model, $platformTexts, $url, $imageUrl);

        return response()->json([
            'success'     => true,
            'corrections' => $corrections,
        ]);
    }
}
