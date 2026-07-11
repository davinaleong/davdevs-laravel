<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentTypeController extends Controller
{
    public function index()
    {
        $contentTypes = ContentType::orderBy('name')->get();

        return view('panel.content-types', compact('contentTypes'));
    }

    public function create()
    {
        return view('panel.content-types-form', ['contentType' => new ContentType]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        ContentType::create($data);

        return redirect()->route('panel.content-types.index')->with('success', 'Content type created.');
    }

    public function edit(ContentType $contentType)
    {
        return view('panel.content-types-form', compact('contentType'));
    }

    public function update(Request $request, ContentType $contentType)
    {
        $contentType->update($this->validated($request));

        return redirect()->route('panel.content-types.index')->with('success', 'Content type updated.');
    }

    public function destroy(ContentType $contentType)
    {
        if ($contentType->entries()->exists()) {
            return back()->with('error', 'Cannot delete: content type is in use.');
        }

        $contentType->delete();

        return redirect()->route('panel.content-types.index')->with('success', 'Content type deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'table_target' => 'required|in:entries,publications',
            'description' => 'nullable|string',
            'react_island' => 'nullable|boolean',
            'listed' => 'nullable|boolean',
            'show_price' => 'nullable|boolean',
        ]);

        $data['react_island'] = $request->boolean('react_island');
        $data['listed'] = $request->boolean('listed');
        $data['show_price'] = $request->boolean('show_price');

        return $data;
    }
}
