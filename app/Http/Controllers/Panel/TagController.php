<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ContentType;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with('contentType')->orderBy('name')->get();

        return view('panel.tags', compact('tags'));
    }

    public function create()
    {
        $contentTypes = ContentType::orderBy('name')->get();

        return view('panel.tags-form', ['tag' => new Tag, 'contentTypes' => $contentTypes]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        Tag::create($data);

        return redirect()->route('panel.tags.index')->with('success', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        $contentTypes = ContentType::orderBy('name')->get();

        return view('panel.tags-form', compact('tag', 'contentTypes'));
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($this->validated($request));

        return redirect()->route('panel.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        if ($tag->entries()->exists() || $tag->publications()->exists()) {
            return back()->with('error', 'Cannot delete: tag is in use.');
        }

        $tag->delete();

        return redirect()->route('panel.tags.index')->with('success', 'Tag deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'content_type_id' => 'nullable|exists:content_types,id',
            'scope' => 'required|in:entries,publications,all',
            'name' => 'required|string|max:100',
        ]);
    }
}
