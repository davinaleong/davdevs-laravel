<x-site-layout title="E-Books">
    <section style="max-width:1280px;margin:0 auto;padding:48px 36px;">
        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,32px);font-weight:800;color:var(--text-primary);margin:0 0 24px;">E-Books</h1>

        @if($bundles->isNotEmpty())
        <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 12px;">Bundles</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);margin-bottom:36px;">
            @foreach($bundles as $pub)
                @include('site.partials.ebook-card', ['pub' => $pub])
            @endforeach
        </div>
        @endif

        <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 12px;">E-Books</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);">
            @forelse($publications as $pub)
                @include('site.partials.ebook-card', ['pub' => $pub])
            @empty
            <p style="grid-column:1/-1;color:var(--text-muted);font-size:13px;padding:40px;text-align:center;">No e-books yet.</p>
            @endforelse
        </div>
        <div style="margin-top:24px;">{{ $publications->links() }}</div>
    </section>
</x-site-layout>
