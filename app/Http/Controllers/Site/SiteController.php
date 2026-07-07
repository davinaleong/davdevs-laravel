<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentType;
use App\Models\Entry;
use App\Models\Publication;
use App\Models\Quip;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteController extends Controller
{
    public function home()
    {
        $types = ContentType::where('listed', true)->orderBy('name')->get();

        $sections = $types->mapWithKeys(function (ContentType $type) {
            return [
                $type->slug => Entry::published()
                    ->where('content_type_id', $type->id)
                    ->with(['contentType', 'category'])
                    ->orderByDesc('published_at')
                    ->limit(4)
                    ->get(),
            ];
        });

        $settings = $this->settings();

        return view('site.home', compact('types', 'sections', 'settings'));
    }

    public function listing(Request $request, string $typeSlug)
    {
        $type = ContentType::where('slug', $typeSlug)->where('listed', true)->firstOrFail();

        $query = Entry::published()->where('content_type_id', $type->id)->with(['category']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->string('tag')));
        }

        $sort = $request->string('sort', 'newest');
        match ((string) $sort) {
            'oldest' => $query->orderBy('published_at', 'asc'),
            'alpha' => $query->orderBy('title', 'asc'),
            default => $query->orderByDesc('published_at'),
        };

        $entries = $query->cursorPaginate(12)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'entries' => $entries->map(fn ($e) => [
                    'title' => $e->title,
                    'excerpt' => $e->excerpt,
                    'url' => route('site.show', [$type->slug, $e->slug]),
                    'published_at' => $e->published_at?->format('M j, Y'),
                    'read_time' => $e->read_time,
                ]),
                'next_page_url' => $entries->nextPageUrl(),
            ]);
        }

        $categories = Category::where('content_type_id', $type->id)->where('active', true)->get();

        return view('site.listing', compact('type', 'entries', 'categories'));
    }

    public function show(string $typeSlug, string $slug)
    {
        $type = ContentType::where('slug', $typeSlug)->firstOrFail();

        $entry = Entry::where('content_type_id', $type->id)
            ->where('slug', $slug)
            ->with(['contentType', 'category', 'layout', 'reactComponent', 'images', 'videoEmbeds', 'links', 'tags', 'meta'])
            ->firstOrFail();

        if ($entry->status === 'private' && ! auth()->check()) {
            return redirect()->route('login');
        }

        if ($entry->status === 'draft' || $entry->status === 'archived') {
            abort_unless(request()->hasValidSignature(), 404);
        }

        return view('site.entry-detail', compact('entry'));
    }

    public function ebooks()
    {
        $publications = Publication::published()->where('bundle', false)->orderByDesc('published_at')->cursorPaginate(12);

        $settings = Cache::remember('settings', 3600, function () {
            return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        });
        $showPrice = $settings['publication_ebook_show_price'] ?? true;

        return view('site.ebooks', compact('publications', 'showPrice'));
    }

    public function ebookShow(string $slug)
    {
        $publication = Publication::where('slug', $slug)
            ->with([
                'store', 'images', 'links', 'tags', 'meta',
                'coverImage', 'ogImage', 'template', 'variants', 'reactComponent',
                'bundleMembers.coverImage', 'bundleMembers.store',
            ])
            ->firstOrFail();

        if ($publication->status === 'private' && ! auth()->check()) {
            return redirect()->route('login');
        }

        if ($publication->template) {
            return view($publication->template->blade_path, compact('publication'));
        }

        return view('site.publication-detail', compact('publication'));
    }

    public function funny()
    {
        return view('site.funny');
    }

    public function randomQuip(Request $request)
    {
        $query = Quip::active();

        if ($request->filled('exclude')) {
            $query->where('id', '!=', $request->integer('exclude'));
        }

        $quip = $query->inRandomOrder()->first() ?? Quip::active()->inRandomOrder()->first();

        abort_unless($quip, 404);

        return response()->json([
            'id' => $quip->id,
            'variant' => $quip->variant,
            'question' => $quip->question,
            'punchline' => $quip->punchline,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $q = $request->string('q');

        $entries = Entry::published()
            ->whereFullText(['title', 'excerpt', 'body'], (string) $q)
            ->with('contentType')
            ->limit(10)
            ->get();

        $publications = Publication::published()
            ->whereFullText(['title', 'tagline', 'excerpt'], (string) $q)
            ->limit(10)
            ->get();

        return response()->json([
            'entries' => $entries->map(fn ($e) => ['title' => $e->title, 'url' => route('site.show', [$e->contentType->slug, $e->slug]), 'type' => $e->contentType->name]),
            'publications' => $publications->map(fn ($p) => ['title' => $p->title, 'url' => route('site.ebooks.show', $p->slug), 'type' => 'E-Book']),
        ]);
    }

    protected function settings(): array
    {
        return Cache::remember('settings', 3600, function () {
            return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        });
    }
}
