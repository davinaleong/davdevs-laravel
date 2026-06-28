@extends('static.layouts.panel')

@section('title', 'Settings — CMS')
@section('nav-settings', 'active')

@push('head')
<style>
    .settings-layout {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 700px) { .settings-layout { grid-template-columns: 1fr; } }

    .settings-nav {
        position: sticky; top: calc(40px + 24px);
        display: flex; flex-direction: column; gap: 1px;
    }
    .settings-nav-item {
        font-size: 13px; color: var(--cms-text-muted);
        padding: 8px 12px; border-radius: var(--cms-radius-btn);
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: background var(--dur-fast), color var(--dur-fast);
    }
    .settings-nav-item:hover { background: var(--cms-bg-hover); color: var(--cms-text-primary); }
    .settings-nav-item.active { background: var(--cms-accent-tint); color: var(--cms-accent); font-weight: 500; }

    .settings-main { display: flex; flex-direction: column; gap: 20px; }

    .settings-section {
        background: var(--cms-bg-surface);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-card);
        overflow: hidden;
    }
    .settings-section-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--cms-border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .settings-section-title { font-size: 14px; font-weight: 600; color: var(--cms-text-primary); }
    .settings-section-sub   { font-size: 12px; color: var(--cms-text-muted); margin-top: 1px; }
    .settings-section-body  { padding: 20px; display: flex; flex-direction: column; gap: 18px; }

    .setting-row {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 20px;
    }
    .setting-info { flex: 1; }
    .setting-label { font-size: 13px; font-weight: 500; color: var(--cms-text-primary); margin-bottom: 2px; }
    .setting-desc  { font-size: 11px; color: var(--cms-text-muted); line-height: 1.5; }
    .setting-control { flex-shrink: 0; min-width: 200px; }

    .toggle-switch {
        width: 36px; height: 20px; border-radius: 10px;
        background: var(--cms-border-strong); border: none; cursor: pointer;
        position: relative; transition: background var(--dur-base);
        flex-shrink: 0;
    }
    .toggle-switch.on { background: var(--cms-accent); }
    .toggle-switch::after {
        content: ''; position: absolute; top: 2px; left: 2px;
        width: 16px; height: 16px; border-radius: 50%;
        background: #fff; transition: transform var(--dur-base);
    }
    .toggle-switch.on::after { transform: translateX(16px); }

    .toggle-row {
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
    }
    .setting-sep { height: 0.5px; background: var(--cms-border); }

    .img-picker-preview {
        display: flex; align-items: center; gap: 10px;
    }
    .img-picker-thumb {
        width: 48px; height: 48px; border-radius: 6px;
        background: var(--cms-bg-surface-2); border: 1px solid var(--cms-border);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; color: var(--cms-text-faint);
    }

    .lh-toggle-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .lh-toggle-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 12px;
        background: var(--cms-bg-surface-2);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-btn);
    }
    .lh-toggle-label { font-size: 12px; color: var(--cms-text-secondary); }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">Settings</h1>
        <p class="cms-page-sub">Site configuration, display preferences, and CMS options.</p>
    </div>
    <div class="cms-actions">
        <button class="btn btn-secondary">↻ Flush Cache</button>
        <button class="btn btn-primary">Save All Changes</button>
    </div>
</div>

<div class="settings-layout">

    <!-- SETTINGS NAV -->
    <nav class="settings-nav">
        <div class="settings-nav-item active">⊞ Display</div>
        <div class="settings-nav-item">⌖ Header</div>
        <div class="settings-nav-item">⊟ Footer</div>
        <div class="settings-nav-item">◎ Lighthouse</div>
        <div class="settings-nav-item">⚙ CMS</div>
        <div class="settings-nav-item" style="margin-top:8px;color:var(--cms-error);">⚠ Danger</div>
    </nav>

    <!-- SETTINGS PANELS -->
    <div class="settings-main">

        <!-- DISPLAY -->
        <div class="settings-section">
            <div class="settings-section-header">
                <div>
                    <div class="settings-section-title">Display</div>
                    <div class="settings-section-sub">Date formats and theme preferences</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Frontend date format</div>
                        <div class="setting-desc">How dates appear on the public site — post cards, detail pages, listing rows.</div>
                    </div>
                    <div class="setting-control">
                        <select class="cms-input cms-select">
                            <option>d M Y  (28 Jun 2026)</option>
                            <option>D, d M Y  (Sun, 28 Jun 2026)</option>
                            <option>Y-m-d  (2026-06-28)</option>
                            <option>m/d/Y  (06/28/2026)</option>
                        </select>
                        <p class="form-hint">Preview: <strong>28 Jun 2026</strong></p>
                    </div>
                </div>
                <div class="setting-sep"></div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">CMS date format</div>
                        <div class="setting-desc">How dates appear inside the CMS — tables, logs, post editor.</div>
                    </div>
                    <div class="setting-control">
                        <select class="cms-input cms-select">
                            <option>d M Y H:i  (28 Jun 2026 09:15)</option>
                            <option>Y-m-d H:i:s</option>
                            <option>d/m/Y H:i</option>
                        </select>
                    </div>
                </div>
                <div class="setting-sep"></div>
                <div class="toggle-row">
                    <div class="setting-info">
                        <div class="setting-label">Default theme</div>
                        <div class="setting-desc">What theme visitors see on first load before their preference is saved.</div>
                    </div>
                    <select class="cms-input cms-select" style="max-width:140px;">
                        <option>Dark (default)</option>
                        <option>Light</option>
                        <option>System</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- HEADER -->
        <div class="settings-section">
            <div class="settings-section-header">
                <div>
                    <div class="settings-section-title">Header</div>
                    <div class="settings-section-sub">Nav bar brand name and logo</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Brand name</div>
                        <div class="setting-desc">Displayed in the nav bar wordmark and browser title suffix.</div>
                    </div>
                    <div class="setting-control">
                        <input type="text" class="cms-input" value="Dav/Devs">
                    </div>
                </div>
                <div class="setting-sep"></div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Brand image</div>
                        <div class="setting-desc">Optional logo displayed alongside or instead of the brand name.</div>
                    </div>
                    <div class="setting-control">
                        <div class="img-picker-preview">
                            <div class="img-picker-thumb">⊟</div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <button class="btn btn-secondary btn-sm">Choose Image</button>
                                <button class="btn btn-ghost btn-sm" style="color:var(--cms-error);">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="settings-section">
            <div class="settings-section-header">
                <div>
                    <div class="settings-section-title">Footer</div>
                    <div class="settings-section-sub">Copyright text and footer links</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Copyright text</div>
                        <div class="setting-desc">Use <code style="font-family:var(--cms-font-mono);font-size:11px;background:var(--cms-bg-surface-2);padding:1px 4px;border-radius:3px;">{year}</code> to insert the current year automatically.</div>
                    </div>
                    <div class="setting-control">
                        <input type="text" class="cms-input" value="© {year} Davina Leong">
                        <p class="form-hint">Preview: © 2026 Davina Leong</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIGHTHOUSE -->
        <div class="settings-section">
            <div class="settings-section-header">
                <div>
                    <div class="settings-section-title">Lighthouse Badges</div>
                    <div class="settings-section-sub">Toggle which metrics are shown in the site footer</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="lh-toggle-grid">
                    <div class="lh-toggle-item">
                        <span class="lh-toggle-label">Performance</span>
                        <button class="toggle-switch on"></button>
                    </div>
                    <div class="lh-toggle-item">
                        <span class="lh-toggle-label">Accessibility</span>
                        <button class="toggle-switch on"></button>
                    </div>
                    <div class="lh-toggle-item">
                        <span class="lh-toggle-label">SEO</span>
                        <button class="toggle-switch on"></button>
                    </div>
                    <div class="lh-toggle-item">
                        <span class="lh-toggle-label">Best Practices</span>
                        <button class="toggle-switch"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CMS PREFERENCES -->
        <div class="settings-section">
            <div class="settings-section-header">
                <div>
                    <div class="settings-section-title">CMS Preferences</div>
                    <div class="settings-section-sub">Editor and workflow options</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="toggle-row">
                    <div class="setting-info">
                        <div class="setting-label">Headless CMS API</div>
                        <div class="setting-desc">Expose a public read-only JSON API at <code style="font-family:var(--cms-font-mono);font-size:11px;background:var(--cms-bg-surface-2);padding:1px 4px;border-radius:3px;">/api/v1/posts</code>.</div>
                    </div>
                    <button class="toggle-switch"></button>
                </div>
                <div class="setting-sep"></div>
                <div class="toggle-row">
                    <div class="setting-info">
                        <div class="setting-label">AI content tools</div>
                        <div class="setting-desc">Enable Claude-powered audit and excerpt generation in the post editor.</div>
                    </div>
                    <button class="toggle-switch on"></button>
                </div>
                <div class="setting-sep"></div>
                <div class="toggle-row">
                    <div class="setting-info">
                        <div class="setting-label">Markdown split-pane by default</div>
                        <div class="setting-desc">Open the post editor in split edit/preview mode instead of edit-only.</div>
                    </div>
                    <button class="toggle-switch on"></button>
                </div>
            </div>
        </div>

        <!-- DANGER ZONE -->
        <div class="settings-section" style="border-color:rgba(196,85,58,0.30);">
            <div class="settings-section-header" style="border-bottom-color:rgba(196,85,58,0.20);">
                <div>
                    <div class="settings-section-title" style="color:var(--cms-error);">Danger Zone</div>
                    <div class="settings-section-sub">Irreversible operations — proceed with caution.</div>
                </div>
            </div>
            <div class="settings-section-body">
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Export all data</div>
                        <div class="setting-desc">Download a full JSON export of all posts, images, links, and settings.</div>
                    </div>
                    <button class="btn btn-secondary">↓ Export</button>
                </div>
                <div class="setting-sep"></div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-label">Flush all logs</div>
                        <div class="setting-desc">Permanently delete all log entries. Cannot be undone.</div>
                    </div>
                    <button class="btn btn-danger">Flush Logs</button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
