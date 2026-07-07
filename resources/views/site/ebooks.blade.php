<x-site-layout title="E-Books">
    <section style="max-width:1280px;margin:0 auto;padding:48px 36px;">
        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,32px);font-weight:800;color:var(--text-primary);margin:0 0 24px;">E-Books</h1>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);">
            @forelse($publications as $pub)
            <a href="{{ route('site.ebooks.show', $pub->slug) }}" style="background:var(--bg-page);padding:18px;text-decoration:none;display:block;">
                @if($pub->coverImage)<img src="{{ $pub->coverImage->responsiveUrl(480) }}" style="width:100%;border-radius:6px;margin-bottom:10px;">@endif
                <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">{{ $pub->title }}</div>
                <div style="font-family:'Inter',sans-serif;font-size:11px;color:var(--text-secondary);">{{ $pub->tagline }}</div>
                @if($showPrice && $pub->store)<div style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);margin-top:6px;">{{ $pub->store->price_display }}</div>@endif
            </a>
            @empty
            <p style="grid-column:1/-1;color:var(--text-muted);font-size:13px;padding:40px;text-align:center;">No e-books yet.</p>
            @endforelse
        </div>
        <div style="margin-top:24px;">{{ $publications->links() }}</div>
    </section>
</x-site-layout>
