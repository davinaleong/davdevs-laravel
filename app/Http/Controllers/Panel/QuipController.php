<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Quip;
use Illuminate\Http\Request;

class QuipController extends Controller
{
    public function index(Request $request)
    {
        $query = Quip::query()->orderByDesc('id');

        if ($request->filled('variant')) {
            $query->where('variant', $request->string('variant'));
        }

        $quips = $query->paginate(20)->withQueryString();

        return view('panel.quips', compact('quips'));
    }

    public function create()
    {
        return view('panel.quips-form', ['quip' => new Quip]);
    }

    public function store(Request $request)
    {
        Quip::create($this->validated($request));

        return redirect()->route('panel.quips.index')->with('success', 'Quip created.');
    }

    public function edit(Quip $quip)
    {
        return view('panel.quips-form', compact('quip'));
    }

    public function update(Request $request, Quip $quip)
    {
        $quip->update($this->validated($request));

        return redirect()->route('panel.quips.index')->with('success', 'Quip updated.');
    }

    public function destroy(Quip $quip)
    {
        $quip->delete();

        return redirect()->route('panel.quips.index')->with('success', 'Quip deleted.');
    }

    public function bulkToggle(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'active' => 'required|boolean']);

        Quip::whereIn('id', $request->input('ids'))->update(['active' => $request->boolean('active')]);

        return redirect()->route('panel.quips.index')->with('success', 'Quips updated.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'variant' => 'required|in:qa,statement',
            'question' => 'nullable|required_if:variant,qa|string',
            'punchline' => 'required|string',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = $request->boolean('active');

        if ($data['variant'] === 'statement') {
            $data['question'] = null;
        }

        return $data;
    }
}
