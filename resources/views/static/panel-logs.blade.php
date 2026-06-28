@extends('static.layouts.panel')

@section('title', 'Activity Log — CMS')
@section('nav-logs', 'active')

@push('head')
<style>
    .log-toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .log-toolbar .cms-input { max-width: 200px; }
    .filter-spacer { flex: 1; }

    .level-pill {
        font-family: var(--cms-font-mono); font-size: 10px;
        padding: 4px 10px; border-radius: 20px;
        border: 1px solid var(--cms-border);
        background: var(--cms-bg-surface);
        color: var(--cms-text-muted); cursor: pointer;
        transition: all var(--dur-fast);
    }
    .level-pill.debug   { color: var(--cms-log-debug); border-color: var(--cms-log-debug); background: var(--cms-log-debug-bg); }
    .level-pill.info    { color: var(--cms-log-info);  border-color: var(--cms-log-info);  background: var(--cms-log-info-bg); }
    .level-pill.warning { color: var(--cms-log-warning); border-color: var(--cms-log-warning); background: var(--cms-log-warning-bg); }
    .level-pill.error   { color: var(--cms-log-error); border-color: var(--cms-log-error); background: var(--cms-log-error-bg); }
    .level-pill.all { background: var(--cms-accent-tint); border-color: var(--cms-accent-border); color: var(--cms-accent); }

    .log-table-wrap { overflow-x: auto; }
    .log-row { display: grid; grid-template-columns: 80px 80px 90px 1fr 130px; align-items: baseline; }

    .log-cell {
        font-family: var(--cms-font-mono); font-size: 11px;
        padding: 10px 12px;
        border-bottom: 0.5px solid var(--cms-table-border);
        vertical-align: middle;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .log-row:hover .log-cell { background: var(--cms-table-row-hover); }

    .log-level-chip {
        font-family: var(--cms-font-mono); font-size: 10px;
        padding: 1px 6px; border-radius: var(--cms-radius-badge);
        display: inline-block;
    }
    .lvl-debug    { color: var(--cms-log-debug);    background: var(--cms-log-debug-bg); }
    .lvl-info     { color: var(--cms-log-info);     background: var(--cms-log-info-bg); }
    .lvl-warning  { color: var(--cms-log-warning);  background: var(--cms-log-warning-bg); }
    .lvl-error    { color: var(--cms-log-error);    background: var(--cms-log-error-bg); }
    .lvl-critical { color: var(--cms-log-critical); background: var(--cms-log-critical-bg); }

    .log-channel {
        font-family: var(--cms-font-mono); font-size: 10px;
        color: var(--cms-text-muted);
    }
    .log-msg { font-family: var(--cms-font-mono); font-size: 11px; color: var(--cms-text-primary); white-space: nowrap; }
    .log-ts  { font-family: var(--cms-font-mono); font-size: 10px; color: var(--cms-text-faint); text-align: right; }

    .row-warning .log-cell { background: var(--cms-log-warning-bg); }
    .row-error   .log-cell { background: var(--cms-log-error-bg); }
    .row-critical .log-cell { background: var(--cms-log-critical-bg); }

    .log-header-row {
        display: grid; grid-template-columns: 80px 80px 90px 1fr 130px;
        background: var(--cms-table-header-bg);
        border-bottom: 1px solid var(--cms-table-border);
    }
    .log-header-cell {
        padding: 8px 12px;
        font-family: var(--cms-font-mono); font-size: 10px;
        color: var(--cms-text-muted); text-transform: uppercase; letter-spacing: 0.06em;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">Activity Log</h1>
        <p class="cms-page-sub">All model events, auth events, HTTP requests, CMS actions, and AI calls.</p>
    </div>
    <div class="cms-actions">
        <button class="btn btn-secondary">↓ Export CSV</button>
        <button class="btn btn-danger btn-sm">Flush Logs</button>
    </div>
</div>

<!-- FILTERS -->
<div class="log-toolbar">
    <input type="search" class="cms-input" placeholder="Search messages…">
    <select class="cms-input" style="max-width:120px;"><option>All channels</option><option>[http]</option><option>[auth]</option><option>[cms]</option><option>[model]</option><option>[ai]</option><option>[export]</option></select>
    <div class="filter-spacer"></div>
    <span class="level-pill all">All</span>
    <span class="level-pill debug">debug</span>
    <span class="level-pill info">info</span>
    <span class="level-pill warning">warning</span>
    <span class="level-pill error">error</span>
</div>

<!-- LOG TABLE -->
<div class="cms-card">
    <div class="log-header-row">
        <div class="log-header-cell">Level</div>
        <div class="log-header-cell">Channel</div>
        <div class="log-header-cell">Model</div>
        <div class="log-header-cell">Message</div>
        <div class="log-header-cell" style="text-align:right;">Timestamp</div>
    </div>

    @php
    $logs = [
        ['error',    '[auth]',  'User',    'Failed 2FA attempt — invalid TOTP code for user ID 1',        '28 Jun 2026 14:22:11', 'row-error'],
        ['info',     '[model]', 'Entry',   'Entry #48 published: "Building a Privacy-First CMS…"',          '28 Jun 2026 09:15:04', ''],
        ['warning',  '[model]', 'Image',   'Cloudinary upload exceeded 8 MB — resized before store',        '28 Jun 2026 09:14:51', 'row-warning'],
        ['info',     '[auth]',  'User',    'User ID 1 authenticated via 2FA — session started',              '28 Jun 2026 08:47:33', ''],
        ['debug',    '[http]',  '—',       'GET /api/entries?page=2 200 OK — 18ms',                          '28 Jun 2026 08:45:20', ''],
        ['info',     '[model]', 'Entry',   'Entry #47 updated: status changed to published',                 '27 Jun 2026 17:30:09', ''],
        ['info',     '[ai]',    '—',       'Audit complete — entry #47: readability 8/10, SEO 7/10',         '27 Jun 2026 17:30:05', ''],
        ['debug',    '[http]',  '—',       'GET /static/panel-posts 200 OK — 24ms',                          '27 Jun 2026 15:10:08', ''],
        ['error',    '[cms]',   'Entry',   'Markdown parse error on entry #46 — unexpected token',           '27 Jun 2026 14:02:55', 'row-error'],
        ['info',     '[model]', 'Entry',   'Entry #46 saved as draft',                                       '27 Jun 2026 13:58:21', ''],
        ['info',     '[auth]',  'User',    'User ID 1 authenticated via 2FA — session started',              '27 Jun 2026 08:30:00', ''],
        ['debug',    '[http]',  '—',       'POST /reactions — 201 Created — fingerprint hashed OK',          '26 Jun 2026 22:14:09', ''],
        ['warning',  '[model]', 'Entry',   'read_time calculation returned 0 — body may be empty',           '26 Jun 2026 21:55:34', 'row-warning'],
        ['info',     '[cms]',   'Tag',     'Tag "Privacy" created',                                          '26 Jun 2026 10:20:17', ''],
    ];
    @endphp

    @foreach($logs as $l)
    <div class="log-row {{ $l[5] }}">
        <div class="log-cell"><span class="log-level-chip lvl-{{ $l[0] }}">{{ $l[0] }}</span></div>
        <div class="log-cell log-channel">{{ $l[1] }}</div>
        <div class="log-cell" style="color:var(--cms-text-muted);">{{ $l[2] }}</div>
        <div class="log-cell log-msg" style="white-space:nowrap;max-width:480px;overflow:hidden;text-overflow:ellipsis;" title="{{ $l[3] }}">{{ $l[3] }}</div>
        <div class="log-cell log-ts">{{ $l[4] }}</div>
    </div>
    @endforeach
</div>

<!-- PAGINATION -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <span style="font-size:12px;color:var(--cms-text-muted);">Showing 1–14 of 1,204 entries</span>
    <div style="display:flex;gap:4px;">
        <button class="btn btn-secondary btn-sm" disabled>←</button>
        <button class="btn btn-secondary btn-sm" style="background:var(--cms-accent);color:var(--cms-text-on-accent);border-color:var(--cms-accent);">1</button>
        <button class="btn btn-secondary btn-sm">2</button>
        <button class="btn btn-secondary btn-sm">3</button>
        <span style="padding:5px 4px;font-family:var(--cms-font-mono);font-size:11px;color:var(--cms-text-faint);">…</span>
        <button class="btn btn-secondary btn-sm">86</button>
        <button class="btn btn-secondary btn-sm">→</button>
    </div>
</div>
@endsection
