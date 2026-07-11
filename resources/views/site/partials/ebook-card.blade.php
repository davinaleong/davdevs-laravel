{{-- Shared publication card for the /ebooks listing (both the Bundles and individual E-Books sections) --}}
<a href="{{ route('site.ebooks.show', $pub->slug) }}"
    style="background:var(--bg-page);padding:18px;text-decoration:none;display:block;">
    @if ($pub->coverImage)
        <img src="{{ $pub->coverImage->responsiveUrl(480) }}" style="width:100%;border-radius:6px;margin-bottom:10px;">
    @endif
    <div
        style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">
        {{ $pub->title }}</div>
    <div style="font-family:'Inter',sans-serif;font-size:11px;color:var(--text-secondary);">{{ $pub->tagline }}</div>
    @php $displayPrice = $pub->contentType ? $pub->contentType->show_price : $showPrice; @endphp
    @if ($displayPrice && $pub->store)
        <div style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);margin-top:6px;">
            {{ $pub->store->price_display }}</div>
    @endif
</a>
