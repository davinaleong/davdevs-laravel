<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ $settings['brand_name'] ?? 'Dav/Devs' }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/png" href="/favicon.png">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $title }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    @if ($description)
        <meta name="twitter:description" content="{{ $description }}">
    @endif
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    @if ($jsonLd)
        <script type="application/ld+json">{!! $jsonLd !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ theme: localStorage.getItem('theme') || 'dark', searchOpen: false }" x-init="document.documentElement.setAttribute('data-theme', theme)" :data-theme="theme"
    style="background:var(--bg-page);color:var(--text-primary);font-family:'Inter',sans-serif;min-height:100vh;display:flex;flex-direction:column;margin:0;"
    @keydown.window="if(($event.metaKey || $event.ctrlKey) && $event.key === 'k') { $event.preventDefault(); searchOpen = true }">

    {{-- Reading progress bar (post detail pages set this via JS) --}}
    <div id="reading-progress"
        style="position:fixed;top:0;left:0;height:2px;background:var(--progress-fill);width:0%;z-index:60;transition:width 100ms;">
    </div>

    {{-- Nav --}}
    <header
        style="background:var(--nav-bg);backdrop-filter:blur(8px);border-bottom:0.5px solid var(--border-default);height:48px;position:sticky;top:0;z-index:50;">
        <div
            style="max-width:1280px;margin:0 auto;padding:0 36px;height:100%;display:flex;align-items:center;justify-content:space-between;">
            <a href="{{ route('home') }}"
                style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--text-primary);text-decoration:none;">
                ~/dav/<span style="color:var(--accent);">devs</span>
            </a>
            <nav style="display:flex;align-items:center;gap:20px;">
                @foreach (\App\Models\ContentType::where('listed', true)->orderBy('name')->get() as $ct)
                    <a href="{{ route('site.listing', $ct->slug) }}"
                        style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--nav-link);text-decoration:none;">{{ $ct->name }}</a>
                @endforeach
                <a href="{{ route('site.tutor') }}"
                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--nav-link);text-decoration:none;">
                    Tutor
                </a>
                <a href="{{ route('site.funny') }}"
                    style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--nav-link);text-decoration:none;">Funny</a>
                <button @click="searchOpen = true"
                    style="background:none;border:none;color:var(--nav-link);cursor:pointer;font-family:'JetBrains Mono',monospace;font-size:11px;">⌘K</button>
                <button @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme)"
                    style="background:none;border:none;color:var(--nav-link);cursor:pointer;">
                    <span x-text="theme === 'dark' ? '☀' : '☾'"></span>
                </button>
            </nav>
        </div>
    </header>

    {{-- Search modal --}}
    <div x-show="searchOpen" x-cloak @keydown.escape.window="searchOpen = false"
        style="position:fixed;inset:0;background:var(--search-overlay);backdrop-filter:blur(4px);z-index:100;display:flex;align-items:flex-start;justify-content:center;padding-top:100px;"
        @click.self="searchOpen = false" x-data="{ q: '', results: { entries: [], publications: [] } }">
        <div
            style="background:var(--search-bg);border-radius:10px;width:100%;max-width:560px;box-shadow:var(--shadow-search);">
            <input type="text" x-model="q"
                @input.debounce.300ms="
                if (q.length < 2) { results = { entries: [], publications: [] }; return }
                fetch('{{ route('api.search') }}?q=' + encodeURIComponent(q)).then(r => r.json()).then(d => results = d)
            "
                placeholder="Search..." autofocus
                style="width:100%;box-sizing:border-box;background:transparent;border:none;border-bottom:0.5px solid var(--search-border);padding:16px 20px;font-family:'JetBrains Mono',monospace;font-size:14px;color:var(--text-primary);outline:none;">
            <div style="max-height:400px;overflow-y:auto;padding:8px;">
                <template x-for="item in [...results.entries, ...results.publications]">
                    <a :href="item.url"
                        style="display:block;padding:10px 12px;text-decoration:none;border-radius:6px;">
                        <div style="font-size:13px;color:var(--text-primary);" x-text="item.title"></div>
                        <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-muted);"
                            x-text="item.type"></div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <main style="flex:1;">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer style="background:var(--footer-bg);border-top:0.5px solid var(--footer-border);padding:24px 36px;">
        <div
            style="max-width:1280px;margin:0 auto;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:16px;">
            <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--footer-text);">~/dav/<span
                    style="color:var(--accent);">devs</span></span>
            <div style="display:flex;gap:14px;">
                @foreach (\App\Models\Link::where('category', 'social')->where('active', true)->orderBy('sort_order')->get() as $link)
                    <a href="{{ $link->url }}" target="{{ $link->target }}" rel="{{ $link->rel }}"
                        style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-faint);text-decoration:none;">{{ $link->label }}</a>
                @endforeach
            </div>
            <div style="text-align:right;">
                @if ($settings['lh_show_performance'] ?? false)
                    <span style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--footer-text);">
                        perf {{ $settings['lh_performance'] ?? '—' }} · a11y
                        {{ $settings['lh_accessibility'] ?? '—' }} · seo {{ $settings['lh_seo'] ?? '—' }}
                    </span>
                @endif
            </div>
        </div>
        <div
            style="text-align:center;margin-top:12px;font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--footer-text);">
            {{ str_replace('{year}', date('Y'), $settings['copyright_text'] ?? '© {year}') }}
            · <a href="{{ route('site.privacy') }}" style="color:var(--footer-text);">Privacy</a>
        </div>
    </footer>

    {{-- Cookie consent banner (essential cookies only, no tracking) --}}
    <div x-data="{ show: !localStorage.getItem('cookie_consent') }" x-show="show" x-cloak
        style="position:fixed;bottom:0;left:0;right:0;background:var(--bg-surface-1);border-top:1px solid var(--border-default);padding:14px 20px;z-index:90;display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;">
        <span style="font-family:'Inter',sans-serif;font-size:12px;color:var(--text-secondary);">
            We use only essential cookies (session + anonymous like token) — no tracking, no analytics.
            <a href="{{ route('site.privacy') }}" style="color:var(--accent);">Learn more</a>
        </span>
        <button @click="localStorage.setItem('cookie_consent', '1'); show = false"
            style="background:var(--accent);color:var(--bg-page);border:none;border-radius:5px;padding:6px 16px;font-family:'Inter',sans-serif;font-size:12px;font-weight:500;cursor:pointer;">Got
            it</button>
    </div>
</body>

</html>
