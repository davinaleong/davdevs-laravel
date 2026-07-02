<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentType;
use App\Models\Entry;
use App\Models\Image;
use App\Models\Layout;
use App\Models\Link;
use App\Models\Tag;
use App\Models\VideoEmbed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $query = Entry::query()->with(['contentType', 'category'])->orderByDesc('id');

        if ($request->filled('type')) {
            $query->where('content_type_id', $request->integer('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }
        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }
        if ($request->filled('from')) {
            $query->whereDate('published_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('published_at', '<=', $request->date('to'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereFullText(['title', 'excerpt', 'body'], (string) $search);
        }

        $sort = $request->string('sort', 'newest');
        match ((string) $sort) {
            'oldest' => $query->reorder('published_at', 'asc'),
            'alpha' => $query->reorder('title', 'asc'),
            default => null, // already newest via id desc
        };

        $entries = $query->cursorPaginate(20)->withQueryString();

        $contentTypes = ContentType::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('panel.entries', compact('entries', 'contentTypes', 'categories'));
    }

    public function create()
    {
        return $this->form(new Entry);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        $entry = Entry::create($data);

        $this->syncRelations($request, $entry);
        $this->syncMeta($request, $entry);

        return redirect()->route('panel.entries.edit', $entry)->with('success', 'Entry created.');
    }

    public function edit(Entry $entry)
    {
        return $this->form($entry);
    }

    public function update(Request $request, Entry $entry)
    {
        $data = $this->validated($request);

        if ($data['title'] !== $entry->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $entry->id);
        }

        $entry->update($data);

        $this->syncRelations($request, $entry);
        $this->syncMeta($request, $entry);

        return redirect()->route('panel.entries.edit', $entry)->with('success', 'Entry updated.');
    }

    public function destroy(Entry $entry)
    {
        $entry->delete();

        return redirect()->route('panel.entries.index')->with('success', 'Entry deleted.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,archive,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:entries,id',
        ]);

        $entries = Entry::whereIn('id', $request->input('ids'));

        match ($request->string('action')->toString()) {
            'publish' => $entries->update(['status' => 'published', 'published_at' => now()]),
            'archive' => $entries->update(['status' => 'archived']),
            'delete' => $entries->delete(),
        };

        return redirect()->route('panel.entries.index')->with('success', 'Bulk action applied.');
    }

    public function duplicate(Entry $entry)
    {
        $copy = $entry->replicate(['ulid', 'slug', 'published_at']);
        $copy->title = $entry->title.' (Copy)';
        $copy->slug = $this->uniqueSlug($copy->title);
        $copy->status = 'draft';
        $copy->save();

        foreach ($entry->meta as $meta) {
            $copy->meta()->create(['key' => $meta->key, 'value' => $meta->value]);
        }
        foreach ($entry->images as $image) {
            $copy->images()->attach($image->id, ['sort_order' => $image->pivot->sort_order, 'caption_override' => $image->pivot->caption_override]);
        }
        foreach ($entry->videoEmbeds as $video) {
            $copy->videoEmbeds()->attach($video->id, ['sort_order' => $video->pivot->sort_order]);
        }
        foreach ($entry->links as $link) {
            $copy->links()->attach($link->id, ['sort_order' => $link->pivot->sort_order]);
        }
        $copy->tags()->attach($entry->tags->pluck('id'));

        return redirect()->route('panel.entries.edit', $copy)->with('success', 'Entry duplicated.');
    }

    protected function form(Entry $entry)
    {
        $contentTypes = ContentType::orderBy('name')->get();
        $layouts = Layout::where('active', true)->orderBy('name')->get();
        $categories = $entry->content_type_id
            ? Category::where('content_type_id', $entry->content_type_id)->orWhereNull('content_type_id')->get()
            : Category::all();
        $tags = $entry->content_type_id
            ? Tag::where('content_type_id', $entry->content_type_id)->orWhereNull('content_type_id')->get()
            : Tag::all();
        $images = Image::orderByDesc('id')->limit(60)->get();
        $videoEmbeds = VideoEmbed::orderByDesc('id')->limit(60)->get();
        $links = Link::orderBy('sort_order')->get();

        return view('panel.entries-form', compact(
            'entry', 'contentTypes', 'layouts', 'categories', 'tags', 'images', 'videoEmbeds', 'links'
        ));
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'content_type_id' => 'required|exists:content_types,id',
            'layout_id' => 'required|exists:layouts,id',
            'category_id' => 'nullable|exists:categories,id',
            'og_image_id' => 'nullable|exists:images,id',
            'title' => 'required|string|max:500',
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'status' => 'required|in:draft,private,published,archived',
            'visibility' => 'required|in:public,unlisted',
            'featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['featured'] = $request->boolean('featured');

        return $data;
    }

    protected function syncRelations(Request $request, Entry $entry): void
    {
        // Images (ordered multi-select with optional caption override)
        $imageIds = $request->input('images', []);
        $imageSync = [];
        foreach ($imageIds as $i => $id) {
            $imageSync[$id] = [
                'sort_order' => $i,
                'caption_override' => $request->input("image_captions.$id"),
            ];
        }
        $entry->images()->sync($imageSync);

        // Video embeds
        $videoIds = $request->input('video_embeds', []);
        $videoSync = [];
        foreach ($videoIds as $i => $id) {
            $videoSync[$id] = ['sort_order' => $i];
        }
        $entry->videoEmbeds()->sync($videoSync);

        // Links
        $linkIds = $request->input('links', []);
        $linkSync = [];
        foreach ($linkIds as $i => $id) {
            $linkSync[$id] = ['sort_order' => $i];
        }
        $entry->links()->sync($linkSync);

        // Tags
        $entry->tags()->sync($request->input('tags', []));
    }

    protected function syncMeta(Request $request, Entry $entry): void
    {
        $entry->meta()->delete();

        $keys = $request->input('meta_key', []);
        $values = $request->input('meta_value', []);

        foreach ($keys as $i => $key) {
            if (blank($key)) {
                continue;
            }
            $entry->meta()->create(['key' => $key, 'value' => $values[$i] ?? null]);
        }
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = date('Ymd').'-'.Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Entry::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
