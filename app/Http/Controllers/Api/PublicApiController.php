<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentType;
use App\Models\Entry;
use App\Models\Tag;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function posts(Request $request)
    {
        $query = Entry::published()->with(['contentType', 'category']);

        if ($request->filled('type')) {
            $query->whereHas('contentType', fn ($q) => $q->where('slug', $request->string('type')));
        }

        $entries = $query->cursorPaginate(20)->withQueryString();

        return response()->json([
            'data' => $entries->map(fn ($e) => $this->transformEntry($e)),
            'next_page_url' => $entries->nextPageUrl(),
        ]);
    }

    public function post(string $slug)
    {
        $entry = Entry::published()->where('slug', $slug)->with(['contentType', 'category', 'tags'])->firstOrFail();

        return response()->json(['data' => $this->transformEntry($entry, full: true)]);
    }

    public function contentTypes()
    {
        return response()->json([
            'data' => ContentType::where('listed', true)->get(['name', 'slug', 'react_island']),
        ]);
    }

    public function categories()
    {
        return response()->json([
            'data' => Category::where('active', true)->get(['id', 'name', 'slug', 'content_type_id']),
        ]);
    }

    public function tags()
    {
        return response()->json([
            'data' => Tag::all(['id', 'name', 'slug', 'content_type_id']),
        ]);
    }

    protected function transformEntry(Entry $entry, bool $full = false): array
    {
        $data = [
            'title' => $entry->title,
            'slug' => $entry->slug,
            'excerpt' => $entry->excerpt,
            'type' => $entry->contentType->name,
            'category' => $entry->category?->name,
            'read_time' => $entry->read_time,
            'published_at' => $entry->published_at?->toIso8601String(),
        ];

        if ($full) {
            $data['body'] = $entry->body;
            $data['tags'] = $entry->tags->pluck('name');
        }

        return $data;
    }
}
