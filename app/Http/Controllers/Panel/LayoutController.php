<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LayoutController extends Controller
{
    public function index()
    {
        $layouts = Layout::orderBy('name')->get();

        return view('panel.layouts', compact('layouts'));
    }

    public function create()
    {
        return view('panel.layouts-form', ['layout' => new Layout()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        Layout::create($data);

        return redirect()->route('panel.layouts.index')->with('success', 'Layout created.');
    }

    public function edit(Layout $layout)
    {
        return view('panel.layouts-form', compact('layout'));
    }

    public function update(Request $request, Layout $layout)
    {
        $layout->update($this->validated($request));

        return redirect()->route('panel.layouts.index')->with('success', 'Layout updated.');
    }

    public function destroy(Layout $layout)
    {
        if ($layout->entries()->exists() || $layout->publications()->exists()) {
            return back()->with('error', 'Cannot delete: layout is in use.');
        }

        $layout->delete();

        return redirect()->route('panel.layouts.index')->with('success', 'Layout deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'blade_component' => 'required|string|max:200',
            'description'     => 'nullable|string',
            'active'          => 'nullable|boolean',
        ]);

        abort_unless(
            view()->exists($data['blade_component']),
            422,
            "Blade component [{$data['blade_component']}] does not exist."
        );

        $data['active'] = $request->boolean('active');

        return $data;
    }
}
