<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\VideoEmbed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoEmbedController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoEmbed::query()->orderByDesc('id');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        $embeds = $query->cursorPaginate(20)->withQueryString();

        return view('panel.video-embeds', compact('embeds'));
    }

    public function create()
    {
        return view('panel.video-embeds-create');
    }

    public function store(Request $request)
    {
        $request->validate(['input' => 'required|string']);

        $videoId = $this->extractVideoId($request->input('input'));

        abort_unless($videoId, 422, 'Could not parse a YouTube video ID from that input.');

        if (VideoEmbed::where('video_id', $videoId)->exists()) {
            return back()->with('error', 'This video is already added.');
        }

        $meta = $this->fetchOEmbed($videoId);

        VideoEmbed::create([
            'video_id'         => $videoId,
            'title'            => $meta['title'] ?? $videoId,
            'channel_name'     => $meta['author_name'] ?? null,
            'thumbnail_url'    => $meta['thumbnail_url'] ?? "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg",
            'duration_seconds' => null,
            'published_at'     => null,
        ]);

        return redirect()->route('panel.video-embeds.index')->with('success', 'Video added.');
    }

    public function edit(VideoEmbed $videoEmbed)
    {
        return view('panel.video-embeds-edit', ['embed' => $videoEmbed]);
    }

    public function update(Request $request, VideoEmbed $videoEmbed)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:500',
            'description'  => 'nullable|string',
            'channel_name' => 'nullable|string|max:255',
        ]);

        $videoEmbed->update($data);

        return redirect()->route('panel.video-embeds.index')->with('success', 'Video updated.');
    }

    public function destroy(VideoEmbed $videoEmbed)
    {
        $videoEmbed->delete();

        return redirect()->route('panel.video-embeds.index')->with('success', 'Video removed.');
    }

    protected function extractVideoId(string $input): ?string
    {
        $input = trim($input);

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $input, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function fetchOEmbed(string $videoId): array
    {
        $url = "https://www.youtube.com/oembed?url=".urlencode("https://www.youtube.com/watch?v={$videoId}")."&format=json";

        $response = Http::timeout(5)->get($url);

        return $response->successful() ? $response->json() : [];
    }
}
