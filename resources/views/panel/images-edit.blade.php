<x-cms-layout title="Edit Image">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">Edit Image</h1>

    <div style="display:flex;gap:24px;max-width:900px;">
        <div style="flex:0 0 240px;">
            <img src="{{ $image->url }}" alt="{{ $image->alt }}" style="width:100%;border-radius:8px;border:1px solid var(--cms-border);">
            @if($usageEntries->count() || $usagePublications->count())
            <div style="margin-top:12px;font-size:12px;color:var(--cms-text-muted);">
                Used in {{ $usageEntries->count() + $usagePublications->count() }} post(s):
                <ul style="margin:6px 0 0;padding-left:16px;">
                    @foreach($usageEntries as $e)<li><a href="{{ route('panel.entries.edit', $e) }}" style="color:var(--cms-accent);">{{ $e->title }}</a></li>@endforeach
                    @foreach($usagePublications as $p)<li><a href="{{ route('panel.publications.edit', $p) }}" style="color:var(--cms-accent);">{{ $p->title }}</a></li>@endforeach
                </ul>
            </div>
            @else
            <p style="margin-top:12px;font-size:12px;color:var(--cms-text-muted);">Not used in any post.</p>
            @endif
        </div>

        <form method="POST" action="{{ route('panel.images.update', $image) }}" enctype="multipart/form-data" style="flex:1;">
            @csrf @method('PUT')
            <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Replace file (optional)</label>
                    <input type="file" name="file" accept="image/*,.svg" style="width:100%;font-size:13px;">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Title</label>
                    <input type="text" name="title" value="{{ $image->title }}" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Alt text</label>
                    <input type="text" name="alt" value="{{ $image->alt }}" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Caption</label>
                    <textarea name="caption" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ $image->caption }}</textarea>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Credit</label>
                    <input type="text" name="credit" value="{{ $image->credit }}" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                </div>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="qr_code" value="1" {{ $image->qr_code ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                    <span style="font-size:12px;color:var(--cms-text-muted);">This is a QR code</span>
                </label>
            </div>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Save Changes</button>
                <a href="{{ route('panel.images.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('panel.images.destroy', $image) }}" style="margin-top:24px;" onsubmit="return confirm('Delete this image permanently?')">
        @csrf @method('DELETE')
        <button type="submit" style="background:var(--cms-btn-danger-bg);color:var(--cms-btn-danger-text);border:none;border-radius:5px;padding:7px 14px;font-size:12px;cursor:pointer;">Delete Image</button>
    </form>
</x-cms-layout>
