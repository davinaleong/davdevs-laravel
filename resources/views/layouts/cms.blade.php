<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-cms data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? 'Dashboard') . ' — ~/dav/devs cms' }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" href="/favicon.png">
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
</head>

<body data-cms data-theme="light" x-data="{ sidebarOpen: true, theme: localStorage.getItem('cms_theme') || 'light' }" x-init="$el.setAttribute('data-theme', theme);
document.documentElement.setAttribute('data-theme', theme)" :data-theme="theme"
    style="background:var(--cms-bg-page);color:var(--cms-text-primary)">

    {{-- Topbar --}}
    <header
        style="background:var(--cms-topbar-bg);color:var(--cms-topbar-text);height:40px;display:flex;align-items:center;padding:0 16px;gap:12px;position:fixed;top:0;left:0;right:0;z-index:50;">
        <button @click="sidebarOpen = !sidebarOpen"
            style="background:none;border:none;cursor:pointer;color:inherit;padding:4px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path d="M3 12h18M3 6h18M3 18h18" />
            </svg>
        </button>
        <span
            style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:500;letter-spacing:0.04em;">~/dav/devs
            cms</span>
        <span
            style="background:var(--cms-topbar-badge-bg);color:var(--cms-topbar-badge-text);font-family:'JetBrains Mono',monospace;font-size:9px;padding:1px 6px;border-radius:3px;font-weight:500;">admin</span>
        <div style="margin-left:auto;display:flex;align-items:center;gap:12px;">
            {{-- Theme toggle --}}
            <button
                @click="theme = theme === 'light' ? 'dark' : 'light'; localStorage.setItem('cms_theme', theme); $el.closest('[data-cms]').setAttribute('data-theme', theme); document.documentElement.setAttribute('data-theme', theme)"
                style="background:none;border:none;cursor:pointer;color:inherit;padding:4px;font-size:14px;font-family:'JetBrains Mono',monospace;">
                <span x-text="theme === 'dark' ? '☀' : '☾'"></span>
            </button>
            <span style="font-family:'Inter',sans-serif;font-size:12px;opacity:0.8;">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    style="background:none;border:none;cursor:pointer;color:inherit;font-family:'Inter',sans-serif;font-size:11px;opacity:0.7;text-decoration:underline;">logout</button>
            </form>
        </div>
    </header>

    <div style="display:flex;padding-top:40px;min-height:100vh;">

        {{-- Sidebar --}}
        <nav x-show="sidebarOpen" x-transition
            style="width:200px;background:var(--cms-sidebar-bg);border-right:0.5px solid var(--cms-sidebar-border);position:fixed;top:40px;left:0;bottom:0;overflow-y:auto;z-index:40;">
            <div style="padding:16px 0;">

                @php
                    $navGroups = [
                        'Content' => [
                            ['label' => 'Dashboard', 'route' => 'panel.dashboard', 'icon' => '⊞'],
                            ['label' => 'Entries', 'route' => 'panel.entries.index', 'icon' => '≡'],
                            ['label' => 'Publications', 'route' => 'panel.publications.index', 'icon' => '📖'],
                            ['label' => 'Quips', 'route' => 'panel.quips.index', 'icon' => '😄'],
                        ],
                        'Media' => [
                            ['label' => 'Images', 'route' => 'panel.images.index', 'icon' => '🖼'],
                            ['label' => 'Links', 'route' => 'panel.links.index', 'icon' => '🔗'],
                            ['label' => 'Video Embeds', 'route' => 'panel.video-embeds.index', 'icon' => '▶'],
                        ],
                        'Taxonomy' => [
                            ['label' => 'Layouts', 'route' => 'panel.layouts.index', 'icon' => '⬚'],
                            ['label' => 'Content Types', 'route' => 'panel.content-types.index', 'icon' => '⊕'],
                            ['label' => 'Categories', 'route' => 'panel.categories.index', 'icon' => '◈'],
                            ['label' => 'Tags', 'route' => 'panel.tags.index', 'icon' => '⊛'],
                        ],
                        'System' => [
                            ['label' => 'Broadcasts', 'route' => 'panel.broadcasts.index', 'icon' => '📡'],
                            ['label' => 'Settings', 'route' => 'panel.settings.index', 'icon' => '⚙'],
                            ['label' => 'Logs', 'route' => 'panel.logs.index', 'icon' => '📋'],
                            ['label' => 'Export', 'route' => 'panel.exports.index', 'icon' => '⬇'],
                        ],
                    ];
                @endphp

                @foreach ($navGroups as $group => $items)
                    <div
                        style="padding:8px 16px 4px;font-family:'Inter',sans-serif;font-size:10px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.08em;">
                        {{ $group }}</div>
                    @foreach ($items as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                            style="display:flex;align-items:center;gap:8px;padding:7px 16px;font-family:'Inter',sans-serif;font-size:13px;text-decoration:none;
                                  color:{{ $active ? 'var(--cms-sidebar-link-active)' : 'var(--cms-sidebar-link)' }};
                                  background:{{ $active ? 'var(--cms-sidebar-active-bg)' : 'transparent' }};
                                  border-left:2px solid {{ $active ? 'var(--cms-sidebar-active-bar)' : 'transparent' }};">
                            <span style="font-size:11px;width:14px;text-align:center;">{{ $item['icon'] }}</span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                    <div style="height:8px;"></div>
                @endforeach
            </div>
        </nav>

        {{-- Main content --}}
        <main :style="sidebarOpen ? 'margin-left:200px;padding:32px;' : 'margin-left:0;padding:32px;'"
            style="flex:1;padding:32px;transition:margin-left 200ms ease;">
            @if (session('success'))
                <div
                    style="background:var(--cms-success-bg);border-left:3px solid var(--cms-success);padding:10px 14px;border-radius:5px;margin-bottom:16px;font-size:13px;color:var(--cms-success);">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    style="background:var(--cms-error-bg);border-left:3px solid var(--cms-error);padding:10px 14px;border-radius:5px;margin-bottom:16px;font-size:13px;color:var(--cms-error);">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>

</html>
