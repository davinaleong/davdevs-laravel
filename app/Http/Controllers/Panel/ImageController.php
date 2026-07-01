<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index(Request $request)
    {
        $query = Image::query()->orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('alt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('qr_code', $request->type === 'qr');
        }

        $images = $query->cursorPaginate(24)->withQueryString();

        return view('panel.images', compact('images'));
    }

    public function create()
    {
        return view('panel.images-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'   => 'required|image|max:10240|mimes:jpg,jpeg,png,webp,gif',
            'title'  => 'nullable|string|max:500',
            'alt'    => 'nullable|string|max:500',
            'caption'=> 'nullable|string',
            'credit' => 'nullable|string|max:300',
            'qr_code'=> 'nullable|boolean',
        ]);

        $uploaded = $this->cloudinary->upload($request->file('file'));

        Image::create(array_merge($uploaded, [
            'title'   => $request->title,
            'alt'     => $request->alt,
            'caption' => $request->caption,
            'credit'  => $request->credit,
            'qr_code' => $request->boolean('qr_code'),
        ]));

        return redirect()->route('panel.images.index')->with('success', 'Image uploaded.');
    }

    public function edit(Image $image)
    {
        $usageEntries = $image->entries()->select('entries.id', 'entries.title', 'entries.slug')->get();
        $usagePublications = $image->publications()->select('publications.id', 'publications.title', 'publications.slug')->get();

        return view('panel.images-edit', compact('image', 'usageEntries', 'usagePublications'));
    }

    public function update(Request $request, Image $image)
    {
        $request->validate([
            'file'   => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp,gif',
            'title'  => 'nullable|string|max:500',
            'alt'    => 'nullable|string|max:500',
            'caption'=> 'nullable|string',
            'credit' => 'nullable|string|max:300',
            'qr_code'=> 'nullable|boolean',
        ]);

        $data = [
            'title'   => $request->title,
            'alt'     => $request->alt,
            'caption' => $request->caption,
            'credit'  => $request->credit,
            'qr_code' => $request->boolean('qr_code'),
        ];

        if ($request->hasFile('file')) {
            $this->cloudinary->destroy($image->cloudinary_id);
            $uploaded = $this->cloudinary->upload($request->file('file'));
            $data = array_merge($data, $uploaded);
        }

        $image->update($data);

        return redirect()->route('panel.images.index')->with('success', 'Image updated.');
    }

    public function destroy(Image $image)
    {
        $inUse = $image->entries()->exists() || $image->publications()->exists();

        if ($inUse) {
            return back()->with('error', 'Cannot delete: image is used in one or more posts.');
        }

        $this->cloudinary->destroy($image->cloudinary_id);
        $image->delete();

        return redirect()->route('panel.images.index')->with('success', 'Image deleted.');
    }
}
