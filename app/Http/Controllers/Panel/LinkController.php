<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Link::query()->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('label', 'like', '%'.$request->string('search').'%');
        }

        $links = $query->get();

        return view('panel.links', compact('links'));
    }

    public function create()
    {
        return view('panel.links-form', ['link' => new Link()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Link::create($data);

        return redirect()->route('panel.links.index')->with('success', 'Link created.');
    }

    public function edit(Link $link)
    {
        return view('panel.links-form', compact('link'));
    }

    public function update(Request $request, Link $link)
    {
        $link->update($this->validated($request));

        return redirect()->route('panel.links.index')->with('success', 'Link updated.');
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return redirect()->route('panel.links.index')->with('success', 'Link deleted.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->input('order', []) as $index => $id) {
            Link::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['ok' => true]);
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'label'       => 'required|string|max:255',
            'url'         => 'required|string|max:2000',
            'target'      => 'required|in:_self,_blank',
            'description' => 'nullable|string',
            'category'    => 'required|in:general,social,nav',
            'icon_class'  => 'nullable|string|max:100',
            'active'      => 'nullable|boolean',
        ]);

        $data['active'] = $request->boolean('active');
        $data['rel']    = $data['target'] === '_blank' ? 'noopener noreferrer' : '';

        return $data;
    }
}
