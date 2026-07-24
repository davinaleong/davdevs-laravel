<x-site-layout title="Home">
    <section style="max-width:1280px;margin:0 auto;padding:80px 36px 60px;text-align:center;">
        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(32px,5vw,48px);font-weight:800;letter-spacing:-0.02em;color:var(--text-primary);margin:0;">
            ~/dav/<span style="color:var(--accent);">devs</span> <span style="animation:cursor-blink 1.1s step-end infinite;">_</span>
        </h1>
        <p style="font-family:'Inter',sans-serif;font-size:14px;color:var(--text-secondary);margin-top:16px;max-width:520px;margin-left:auto;margin-right:auto;">
            Davina Develops — projects, articles, tools, and everything in between.
        </p>
    </section>

    @if($publications->isNotEmpty())
    <section style="max-width:1280px;margin:0 auto;padding:32px 36px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h2 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--text-primary);margin:0;">E-Books</h2>
            @php $pubType = $types->firstWhere('table_target', 'publications'); @endphp
            <a href="{{ $pubType ? route('site.listing', $pubType->slug) : '#' }}" style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);text-decoration:none;">view all →</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);">
            @foreach($publications as $pub)
                @include('site.partials.ebook-card', ['pub' => $pub])
            @endforeach
        </div>
    </section>
    @endif

    @foreach($types as $type)
        @php $entries = $sections[$type->slug] ?? collect(); @endphp
        @if($entries->isNotEmpty())
        <section style="max-width:1280px;margin:0 auto;padding:32px 36px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--text-primary);margin:0;">{{ $type->name }}</h2>
                <a href="{{ route('site.listing', $type->slug) }}" style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);text-decoration:none;">view all →</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);">
                @foreach($entries as $entry)
                <a href="{{ route('site.show', [$type->slug, $entry->slug]) }}" style="background:var(--bg-page);padding:16px 18px;text-decoration:none;display:block;transition:background var(--duration-base) var(--easing-default);">
                    <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">{{ $entry->title }}</div>
                    <div style="font-family:'Inter',sans-serif;font-size:12px;color:var(--text-secondary);margin-bottom:8px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $entry->excerpt }}</div>
                    <div style="font-family:'JetBrains Mono',monospace;font-size:8px;color:var(--text-faint);">{{ $entry->published_at?->format('M j, Y') }} · {{ $entry->read_time }} min</div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach
</x-site-layout>
