<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentType;
use App\Models\Image;
use App\Models\Layout;
use App\Models\Link;
use App\Models\Publication;
use App\Models\PublicationTemplate;
use App\Models\ReactComponent;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Publication::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('search')) {
            $query->whereFullText(['title', 'tagline', 'excerpt'], (string) $request->string('search'));
        }

        $publications = $query->cursorPaginate(20)->withQueryString();

        return view('panel.publications', compact('publications'));
    }

    public function create()
    {
        return $this->form(new Publication);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        $publication = Publication::create($data);

        $publication->store()->create($this->storeValidated($request));
        $this->syncRelations($request, $publication);
        $this->syncMeta($request, $publication);
        $this->syncVariants($request, $publication);

        return redirect()->route('panel.publications.edit', $publication)->with('success', 'Publication created.');
    }

    public function edit(Publication $publication)
    {
        return $this->form($publication);
    }

    public function update(Request $request, Publication $publication)
    {
        $data = $this->validated($request);

        if ($data['title'] !== $publication->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $publication->id);
        }

        $publication->update($data);

        $publication->store()->updateOrCreate([], $this->storeValidated($request));
        $this->syncRelations($request, $publication);
        $this->syncMeta($request, $publication);
        $this->syncVariants($request, $publication);

        return redirect()->route('panel.publications.edit', $publication)->with('success', 'Publication updated.');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete();

        return redirect()->route('panel.publications.index')->with('success', 'Publication deleted.');
    }

    protected function form(Publication $publication)
    {
        $layouts = Layout::where('active', true)->orderBy('name')->get();
        $contentTypes = ContentType::where('table_target', 'publications')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $images = Image::orderByDesc('id')->limit(60)->get();
        $links = Link::orderBy('sort_order')->get();
        $tags = Tag::all();
        $bundleCandidates = Publication::where('id', '!=', $publication->id ?? 0)->orderBy('title')->get();
        $templates = PublicationTemplate::where('publication_type', $publication->publication_type ?? 'ebook')
            ->where('active', true)->orderBy('name')->get();
        $reactComponents = ReactComponent::where('active', true)->orderBy('name')->get();

        return view('panel.publications-form', compact(
            'publication', 'layouts', 'contentTypes', 'categories', 'images', 'links', 'tags', 'bundleCandidates', 'templates', 'reactComponents'
        ));
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'content_type_id' => 'nullable|exists:content_types,id',
            'layout_id' => 'required|exists:layouts,id',
            'publication_template_id' => 'nullable|exists:publication_templates,id',
            'react_component_id' => 'nullable|exists:react_components,id',
            'category_id' => 'nullable|exists:categories,id',
            'og_image_id' => 'nullable|exists:images,id',
            'cover_image_id' => 'nullable|exists:images,id',
            'title' => 'required|string|max:500',
            'tagline' => 'nullable|string|max:500',
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'status' => 'required|in:draft,private,published,archived',
            'featured' => 'nullable|boolean',
            'bundle' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['featured'] = $request->boolean('featured');
        $data['bundle'] = $request->boolean('bundle');
        $data['publication_type'] = 'ebook';

        return $data;
    }

    protected function storeValidated(Request $request): array
    {
        return $request->validate([
            'ls_store_url' => 'nullable|string',
            'price_display' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:3',
            'free_sample_url' => 'nullable|string',
        ]);
    }

    protected function syncRelations(Request $request, Publication $publication): void
    {
        $imageIds = $request->input('images', []);
        $imageSync = [];
        foreach ($imageIds as $i => $id) {
            $imageSync[$id] = ['sort_order' => $i, 'caption_override' => $request->input("image_captions.$id")];
        }
        $publication->images()->sync($imageSync);

        $linkIds = $request->input('links', []);
        $linkSync = [];
        foreach ($linkIds as $i => $id) {
            $linkSync[$id] = ['sort_order' => $i];
        }
        $publication->links()->sync($linkSync);

        $publication->tags()->sync($request->input('tags', []));

        if ($publication->bundle) {
            $memberIds = $request->input('bundle_members', []);
            $memberSync = [];
            foreach ($memberIds as $i => $id) {
                $memberSync[$id] = ['sort_order' => $i];
            }
            $publication->bundleMembers()->sync($memberSync);
        }
    }

    protected function syncMeta(Request $request, Publication $publication): void
    {
        $publication->meta()->delete();

        $keys = $request->input('meta_key', []);
        $values = $request->input('meta_value', []);

        // Keys managed by dedicated named inputs — skip them from the free-form rows
        // to prevent duplicate key violations if the user had them in custom meta before.
        $reservedKeys = ['design_color_primary'];

        foreach ($keys as $i => $key) {
            if (blank($key) || in_array($key, $reservedKeys, true)) {
                continue;
            }
            $publication->meta()->create(['key' => $key, 'value' => $values[$i] ?? null]);
        }

        // Named design fields stored as meta
        foreach ($reservedKeys as $key) {
            if ($request->filled($key)) {
                $publication->meta()->create(['key' => $key, 'value' => $request->input($key)]);
            }
        }
    }

    protected function syncVariants(Request $request, Publication $publication): void
    {
        $publication->variants()->delete();

        $names = $request->input('variant_name', []);
        $prices = $request->input('variant_price_display', []);

        foreach ($names as $i => $name) {
            if (blank($name)) {
                continue;
            }
            $publication->variants()->create([
                'name' => $name,
                'price_display' => $prices[$i] ?? null,
                'sort_order' => $i,
            ]);
        }
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Publication::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
