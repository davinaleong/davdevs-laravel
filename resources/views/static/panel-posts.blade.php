@extends('static.layouts.panel')

@section('title', 'All Entries — CMS')
@section('nav-posts', 'active')

@push('head')
<style>
    .filter-bar {
        display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
    }
    .filter-bar .cms-input  { max-width: 220px; }
    .filter-bar .cms-select { max-width: 150px; }
    .filter-spacer { flex: 1; }

    .col-title { min-width: 260px; }
    .col-type  { width: 110px; }
    .col-status{ width: 100px; }
    .col-date  { width: 100px; }
    .col-likes { width: 60px; text-align:right; }
    .col-act   { width: 90px; }

    .post-title-cell { display: flex; flex-direction: column; gap: 2px; }
    .post-title-text {
        font-size: 13px; color: var(--cms-text-primary); font-weight: 500;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;
    }
    .post-slug {
        font-family: var(--cms-font-mono); font-size: 10px;
        color: var(--cms-text-faint);
    }
    .likes-val {
        font-family: var(--cms-font-mono); font-size: 12px;
        color: var(--cms-text-muted); text-align: right;
    }

    .pagination {
        display: flex; align-items: center; gap: 6px; justify-content: space-between;
        flex-wrap: wrap;
    }
    .pagination-info { font-size: 12px; color: var(--cms-text-muted); }
    .pagination-btns { display: flex; gap: 4px; }
    .pg-btn {
        font-family: var(--cms-font-mono); font-size: 11px;
        padding: 5px 10px; border-radius: var(--cms-radius-btn);
        background: var(--cms-bg-surface);
        border: 1px solid var(--cms-border);
        color: var(--cms-text-secondary); cursor: pointer;
        transition: border-color var(--dur-fast), background var(--dur-fast);
    }
    .pg-btn:hover { border-color: var(--cms-border-strong); background: var(--cms-bg-hover); }
    .pg-btn.active { background: var(--cms-accent); color: var(--cms-text-on-accent); border-color: var(--cms-accent); }
    .pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">All Entries</h1>
        <p class="cms-page-sub">90 entries across all content types</p>
    </div>
    <div class="cms-actions">
        <a href="/static/panel-post-new"><button class="btn btn-primary">＋ New Entry</button></a>
    </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
    <input type="search" class="cms-input" placeholder="Search entries…" value="">
    <select class="cms-input cms-select">
        <option>All types</option>
        <option>Article</option>
        <option>Project</option>
        <option>Tool</option>
        <option>Notebook</option>
        <option>Sermon</option>
        <option>Page</option>
        <option>Frontend Mentor</option>
        <option>Knowledge Sharing</option>
    </select>
    <select class="cms-input cms-select">
        <option>All statuses</option>
        <option>Published</option>
        <option>Draft</option>
        <option>Private</option>
        <option>Archived</option>
    </select>
    <div class="filter-spacer"></div>
    <select class="cms-input cms-select" style="max-width:130px;">
        <option>Newest first</option>
        <option>Oldest first</option>
        <option>Most reactions</option>
    </select>
</div>

<!-- TABLE -->
<div class="cms-card">
    <div class="cms-table-wrap">
        <table class="cms-table">
            <thead>
                <tr>
                    <th class="col-title">Title</th>
                    <th class="col-type">Type</th>
                    <th class="col-status">Status</th>
                    <th class="col-date">Published</th>
                    <th class="col-likes">Likes</th>
                    <th class="col-act"></th>
                </tr>
            </thead>
            <tbody>
                @php
                $posts = [
                    ['Building a Privacy-First CMS with Laravel and Anthropic Claude','building-a-privacy-first-cms','article','published','28 Jun 2026',24],
                    ['Vite 8 Plugin API: What Actually Changed','vite-8-plugin-api','article','published','25 Jun 2026',6],
                    ['On Rest as a Developer Practice','on-rest-as-a-developer-practice','article','draft','22 Jun 2026',0],
                    ['Azure B2C Custom Policies: A Survival Guide','azure-b2c-custom-policies','article','published','18 Jun 2026',33],
                    ['Pest 3 + Laravel 12: Writing Tests That Actually Catch Bugs','pest-3-laravel-12','article','published','15 Jun 2026',11],
                    ['JSON Schema Validator — v2','json-schema-validator-v2','tool','published','12 Jun 2026',7],
                    ['Dav/Devs Laravel CMS Rebuild','davdevs-laravel-cms','project','private','01 Jun 2026',0],
                    ['TOTP 2FA in Laravel Without Breeze Magic','totp-2fa-laravel','article','published','01 Jun 2026',8],
                    ['Structuring Outputs from LLMs Without Losing Your Mind','llm-structured-outputs','article','published','20 May 2026',15],
                    ['Prompt Injection is the New SQL Injection','prompt-injection','article','archived','14 Nov 2025',41],
                ];
                @endphp
                @foreach($posts as $p)
                <tr>
                    <td class="col-title">
                        <div class="post-title-cell">
                            <span class="post-title-text">{{ $p[0] }}</span>
                            <span class="post-slug">/{{ $p[1] }}</span>
                        </div>
                    </td>
                    <td class="col-type"><span class="type-badge type-{{ $p[2] }}">{{ ucfirst($p[2]) }}</span></td>
                    <td class="col-status"><span class="status-chip status-{{ $p[3] }}">{{ $p[3] }}</span></td>
                    <td class="col-date" style="font-family:var(--cms-font-mono);font-size:11px;color:var(--cms-text-muted);">{{ $p[4] }}</td>
                    <td class="col-likes"><span class="likes-val">{{ $p[5] }}</span></td>
                    <td class="col-act">
                        <div class="table-actions">
                            <a href="/static/panel-post-edit" class="tbl-link">Edit</a>
                            <span style="color:var(--cms-text-faint)">·</span>
                            <span class="tbl-link">↗</span>
                            <span style="color:var(--cms-text-faint)">·</span>
                            <span class="tbl-link danger">✕</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINATION -->
<div class="pagination">
    <span class="pagination-info">Showing 1–10 of 90 entries</span>
    <div class="pagination-btns">
        <button class="pg-btn" disabled>←</button>
        <button class="pg-btn active">1</button>
        <button class="pg-btn">2</button>
        <button class="pg-btn">3</button>
        <span style="font-family:var(--cms-font-mono);font-size:11px;color:var(--cms-text-faint);padding:0 4px;">…</span>
        <button class="pg-btn">10</button>
        <button class="pg-btn">→</button>
    </div>
</div>
@endsection
