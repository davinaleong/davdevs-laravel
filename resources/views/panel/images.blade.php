<x-cms-layout title="Image Manager">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 4px;">Image Manager</h1>
            <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">{{ $images->count() }} images loaded</p>
        </div>
        <a href="{{ route('panel.images.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;text-decoration:none;">+ Upload Image</a>
    </div>

    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or alt..."
               style="flex:1;max-width:320px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'Inter',sans-serif;font-size:13px;color:var(--cms-input-text);">
        <select name="type" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'Inter',sans-serif;font-size:13px;color:var(--cms-input-text);">
            <option value="">All types</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images only</option>
            <option value="qr" {{ request('type') === 'qr' ? 'selected' : '' }}>QR codes only</option>
        </select>
        <button type="submit" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);border-radius:5px;padding:10px 20px;font-size:13px;cursor:pointer;">Filter</button>
    </form>

    <div x-data="{ lightboxImage: null }">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
            @forelse($images as $image)
            <div @click="lightboxImage = {{ $image->id }}"
                 style="cursor:pointer;background:var(--cms-bg-surface-2);border-radius:6px;overflow:hidden;aspect-ratio:1;position:relative;border:1px solid var(--cms-border);">
                <img src="{{ $image->url }}" alt="{{ $image->alt }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                @if($image->qr_code)
                <span style="position:absolute;top:4px;right:4px;background:var(--cms-accent);color:var(--cms-text-on-accent);font-size:8px;padding:2px 5px;border-radius:3px;font-family:'JetBrains Mono',monospace;">QR</span>
                @endif
            </div>

            {{-- Lightbox --}}
            <div x-show="lightboxImage === {{ $image->id }}" x-cloak
                 style="position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:100;display:flex;align-items:center;justify-content:center;padding:24px;"
                 @click.self="lightboxImage = null" @keydown.escape.window="lightboxImage = null">
                <div style="background:var(--cms-bg-surface);border-radius:10px;max-width:800px;width:100%;max-height:90vh;overflow-y:auto;">
                    <img src="{{ $image->url }}" alt="{{ $image->alt }}" style="width:100%;display:block;">
                    <div style="padding:20px;">
                        <h3 style="font-size:14px;font-weight:600;margin:0 0 8px;color:var(--cms-text-primary);">{{ $image->title ?: 'Untitled' }}</h3>
                        <dl style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);display:grid;grid-template-columns:auto 1fr;gap:4px 12px;margin-bottom:16px;">
                            <dt>Alt</dt><dd>{{ $image->alt ?: '—' }}</dd>
                            <dt>Caption</dt><dd>{{ $image->caption ?: '—' }}</dd>
                            <dt>Credit</dt><dd>{{ $image->credit ?: '—' }}</dd>
                            <dt>Dimensions</dt><dd>{{ $image->width }}×{{ $image->height }}</dd>
                            <dt>Format</dt><dd>{{ $image->format }}</dd>
                            <dt>Size</dt><dd>{{ number_format($image->bytes / 1024, 1) }} KB</dd>
                        </dl>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('panel.images.edit', $image) }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:6px 12px;border-radius:5px;font-size:12px;text-decoration:none;">Edit</a>
                            <button @click="lightboxImage = null" style="background:none;border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:6px 12px;border-radius:5px;font-size:12px;cursor:pointer;">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p style="grid-column:1/-1;color:var(--cms-text-muted);font-size:13px;padding:40px;text-align:center;">No images found.</p>
            @endforelse
        </div>
    </div>

    <div style="margin-top:20px;">
        {{ $images->links() }}
    </div>
</x-cms-layout>
