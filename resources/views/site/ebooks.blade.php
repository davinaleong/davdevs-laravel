<x-site-layout title="{{ $type->name ?? 'E-Books' }}">
    <div x-data="{ helpOpen: false }">
        <section style="max-width:1280px;margin:0 auto;padding:48px 36px;">
            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin-bottom:24px;">
                <h1 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,32px);font-weight:800;color:var(--text-primary);margin:0;">{{ $type->name ?? 'E-Books' }}</h1>
                <button @click="helpOpen = true"
                    style="flex-shrink:0;background:none;border:1px solid var(--border-default);border-radius:20px;padding:5px 14px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);cursor:pointer;white-space:nowrap;"
                    title="How to Purchase">
                    ? How to Purchase
                </button>
            </div>

        @if($bundles->isNotEmpty())
        <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 12px;">Bundles</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);margin-bottom:36px;">
            @foreach($bundles as $pub)
                @include('site.partials.ebook-card', ['pub' => $pub])
            @endforeach
        </div>
        @endif

        <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 12px;">{{ $type->name ?? 'E-Books' }}</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1px;background:var(--border-default);">
            @forelse($publications as $pub)
                @include('site.partials.ebook-card', ['pub' => $pub])
            @empty
            <p style="grid-column:1/-1;color:var(--text-muted);font-size:13px;padding:40px;text-align:center;">No e-books yet.</p>
            @endforelse
        </div>
        <div style="margin-top:24px;">{{ $publications->links() }}</div>
        </section>

        @include('site.partials.how-to-purchase-dialog')
    </div>
</x-site-layout>
