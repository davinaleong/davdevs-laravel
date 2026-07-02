<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('contentType')->orderBy('name')->get();

        return view('panel.categories', compact('categories'));
    }

    public function create()
    {
        $contentTypes = ContentType::orderBy('name')->get();

        return view('panel.categories-form', ['category' => new Category, 'contentTypes' => $contentTypes]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('panel.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $contentTypes = ContentType::orderBy('name')->get();

        return view('panel.categories-form', compact('category', 'contentTypes'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request));

        return redirect()->route('panel.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->entries()->exists() || $category->publications()->exists()) {
            return back()->with('error', 'Cannot delete: category is in use.');
        }

        $category->delete();

        return redirect()->route('panel.categories.index')->with('success', 'Category deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'content_type_id' => 'nullable|exists:content_types,id',
            'scope' => 'required|in:entries,publications,all',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = $request->boolean('active');

        return $data;
    }
}
