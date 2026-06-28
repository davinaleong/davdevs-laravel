@extends('static.layouts.panel')

@section('title', 'Dashboard — CMS')
@section('nav-dashboard', 'active')

@push('head')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 900px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .stat-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--cms-bg-surface);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-card);
        padding: var(--cms-card-pad);
        display: flex; flex-direction: column; gap: 6px;
    }
    .stat-label {
        font-size: 11px; color: var(--cms-text-muted); font-weight: 500;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .stat-value {
        font-family: var(--cms-font-mono); font-size: 28px; font-weight: 500;
        color: var(--cms-text-primary); line-height: 1;
    }
    .stat-delta {
        font-size: 11px; color: var(--cms-text-muted);
    }
    .stat-delta .up   { color: var(--cms-success); }
    .stat-delta .down { color: var(--cms-error); }

    .panel-row {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 12px;
    }
    @media (max-width: 900px) { .panel-row { grid-template-columns: 1fr; } }

    .cms-card-header {
        padding: 14px 16px;
        border-bottom: 1px solid var(--cms-border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .cms-card-title {
        font-size: 13px; font-weight: 600; color: var(--cms-text-primary);
    }
    .cms-card-body { padding: 0; }

    .recent-row {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 16px;
        border-bottom: 0.5px solid var(--cms-border);
        transition: background var(--dur-fast);
        cursor: pointer;
    }
    .recent-row:last-child { border-bottom: none; }
    .recent-row:hover { background: var(--cms-bg-hover); }
    .recent-title {
        font-size: 13px; color: var(--cms-text-primary);
        flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .recent-meta {
        font-family: var(--cms-font-mono); font-size: 10px;
        color: var(--cms-text-faint); white-space: nowrap;
    }

    .quick-actions {
        display: flex; flex-direction: column; gap: 6px;
        padding: 16px;
    }
    .quick-action-btn {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px;
        background: var(--cms-bg-surface-2);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-btn);
        cursor: pointer; font-size: 13px;
        color: var(--cms-text-secondary);
        transition: background var(--dur-fast), border-color var(--dur-fast);
        text-align: left;
    }
    .quick-action-btn:hover { background: var(--cms-bg-hover); border-color: var(--cms-border-strong); }
    .quick-action-icon { font-size: 14px; width: 18px; text-align: center; }
    .quick-action-label { font-size: 13px; color: var(--cms-text-primary); }
    .quick-action-sub { font-size: 11px; color: var(--cms-text-muted); }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">Dashboard</h1>
        <p class="cms-page-sub">Welcome back, Davina. Here's what's happening.</p>
    </div>
    <div class="cms-actions">
        <a href="/static/panel-post-new"><button class="btn btn-primary">＋ New Entry</button></a>
    </div>
</div>

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-label">Total Entries</span>
        <span class="stat-value">90</span>
        <span class="stat-delta"><span class="up">↑ 3</span> this month</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Reactions</span>
        <span class="stat-value">312</span>
        <span class="stat-delta"><span class="up">↑ 18</span> this week</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Images</span>
        <span class="stat-value">47</span>
        <span class="stat-delta">5 uploaded this month</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Drafts</span>
        <span class="stat-value">6</span>
        <span class="stat-delta">Awaiting publish</span>
    </div>
</div>

<!-- RECENT + QUICK ACTIONS -->
<div class="panel-row">
    <div class="cms-card">
        <div class="cms-card-header">
            <span class="cms-card-title">Recent Entries</span>
            <a href="/static/panel-posts"><button class="btn btn-ghost btn-sm">view all →</button></a>
        </div>
        <div class="cms-card-body">
            <div class="recent-row">
                <span class="type-badge type-article">Article</span>
                <span class="recent-title">Building a Privacy-First CMS with Laravel and Anthropic Claude</span>
                <span class="status-chip status-published">published</span>
                <span class="recent-meta">28 Jun</span>
            </div>
            <div class="recent-row">
                <span class="type-badge type-article">Article</span>
                <span class="recent-title">Vite 8 Plugin API: What Actually Changed</span>
                <span class="status-chip status-published">published</span>
                <span class="recent-meta">25 Jun</span>
            </div>
            <div class="recent-row">
                <span class="type-badge type-article">Article</span>
                <span class="recent-title">On Rest as a Developer Practice</span>
                <span class="status-chip status-draft">draft</span>
                <span class="recent-meta">22 Jun</span>
            </div>
            <div class="recent-row">
                <span class="type-badge type-tool">Tool</span>
                <span class="recent-title">JSON Schema Validator — v2</span>
                <span class="status-chip status-published">published</span>
                <span class="recent-meta">18 Jun</span>
            </div>
            <div class="recent-row">
                <span class="type-badge type-project">Project</span>
                <span class="recent-title">Dav/Devs Laravel CMS Rebuild</span>
                <span class="status-chip status-private">private</span>
                <span class="recent-meta">15 Jun</span>
            </div>
        </div>
    </div>

    <div class="cms-card">
        <div class="cms-card-header">
            <span class="cms-card-title">Quick Actions</span>
        </div>
        <div class="quick-actions">
            <a href="/static/panel-post-new">
                <div class="quick-action-btn">
                    <span class="quick-action-icon">✦</span>
                    <div>
                        <div class="quick-action-label">New Entry</div>
                        <div class="quick-action-sub">Article, project, tool…</div>
                    </div>
                </div>
            </a>
            <a href="/static/panel-images">
                <div class="quick-action-btn">
                    <span class="quick-action-icon">⊟</span>
                    <div>
                        <div class="quick-action-label">Upload Image</div>
                        <div class="quick-action-sub">Add to media library</div>
                    </div>
                </div>
            </a>
            <a href="/static/panel-settings">
                <div class="quick-action-btn">
                    <span class="quick-action-icon">⚙</span>
                    <div>
                        <div class="quick-action-label">Settings</div>
                        <div class="quick-action-sub">Site config & display</div>
                    </div>
                </div>
            </a>
            <div class="quick-action-btn">
                <span class="quick-action-icon">↻</span>
                <div>
                    <div class="quick-action-label">Flush Cache</div>
                    <div class="quick-action-sub">Clear settings & views</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- POST TYPE BREAKDOWN -->
<div class="cms-card">
    <div class="cms-card-header">
        <span class="cms-card-title">Entries by Content Type</span>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:1px;background:var(--cms-border);border-radius:0 0 var(--cms-radius-card) var(--cms-radius-card);overflow:hidden;">
        @foreach([['Article','article',48],['Project','project',12],['Tool','tool',14],['Notebook','notebook',6],['Sermon','sermon',3],['Page','page',4],['FEM','fem',2],['KS','ks',1]] as $t)
        <div style="flex:1;min-width:100px;background:var(--cms-bg-surface);padding:14px 16px;">
            <div style="margin-bottom:6px;"><span class="type-badge type-{{ $t[1] }}">{{ $t[0] }}</span></div>
            <div style="font-family:var(--cms-font-mono);font-size:22px;color:var(--cms-text-primary);line-height:1;">{{ $t[2] }}</div>
            <div style="font-size:11px;color:var(--cms-text-muted);margin-top:2px;">entries</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
