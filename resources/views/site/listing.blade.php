<x-site-layout :title="$type->name">
    <section style="max-width:1280px;margin:0 auto;padding:48px 36px;"
             x-data="{
                entries: {{ $entries->map(fn ($e) => ['title' => $e->title, 'excerpt' => $e->excerpt, 'url' => route('site.show', [$type->slug, $e->slug]), 'published_at' => $e->published_at?->format('M j, Y'), 'read_time' => $e->read_time])->values()->toJson() }},
                nextUrl: @js($entries->nextPageUrl()),
                loading: false,
                loadMore() {
                    if (!this.nextUrl || this.loading) return;
                    this.loading = true;
                    fetch(this.nextUrl, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(data => {
                            this.entries = this.entries.concat(data.entries);
                            this.nextUrl = data.next_page_url;
                            this.loading = false;
                        });
                }
             }">
        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,32px);font-weight:800;color:var(--text-primary);margin:0 0 8px;">{{ $type->name }}</h1>

        <form method="GET" style="display:flex;gap:10px;margin:20px 0;flex-wrap:wrap;">
            <select name="category" onchange="this.form.submit()" style="background:var(--bg-surface-1);border:1px solid var(--border-default);border-radius:5px;padding:6px 10px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--text-primary);">
                <option value="">All categories</option>
                @foreach($categories as $c)
                <option value="{{ $c->slug }}" {{ request('category') === $c->slug ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="sort" onchange="this.form.submit()" style="background:var(--bg-surface-1);border:1px solid var(--border-default);border-radius:5px;padding:6px 10px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--text-primary);">
                <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>A–Z</option>
            </select>
        </form>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1px;background:var(--border-default);">
            <template x-if="entries.length === 0">
                <p style="grid-column:1/-1;color:var(--text-muted);font-size:13px;padding:40px;text-align:center;">No entries found.</p>
            </template>
            <template x-for="entry in entries" :key="entry.url">
                <a :href="entry.url" style="background:var(--bg-page);padding:18px;text-decoration:none;display:block;">
                    <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:6px;" x-text="entry.title"></div>
                    <div style="font-family:'Inter',sans-serif;font-size:12px;color:var(--text-secondary);margin-bottom:8px;" x-text="entry.excerpt"></div>
                    <div style="font-family:'JetBrains Mono',monospace;font-size:8px;color:var(--text-faint);" x-text="entry.published_at + ' · ' + entry.read_time + ' min'"></div>
                </a>
            </template>
        </div>

        {{-- Desktop: classic pagination links --}}
        <div class="hide-on-mobile" style="margin-top:24px;">
            {{ $entries->links() }}
        </div>

        {{-- Mobile: infinite scroll via Intersection Observer --}}
        <div class="show-on-mobile" x-show="nextUrl" x-intersect="loadMore()" style="padding:20px;text-align:center;">
            <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--text-faint);" x-text="loading ? 'loading more…' : ''"></span>
        </div>
        <style>
            .show-on-mobile { display: none; }
            @media (max-width: 640px) {
                .hide-on-mobile { display: none; }
                .show-on-mobile { display: block; }
            }
        </style>
    </section>
</x-site-layout>
