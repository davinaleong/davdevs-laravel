@extends('static.layouts.site')

@section('title', 'Articles — Dav/Devs')

@push('head')
<style>
    /* ── NAV (same as home) ───────────────────────────────────────── */
    .nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        height: var(--nav-height);
        background: var(--nav-bg);
        border-bottom: 0.5px solid var(--nav-border);
        backdrop-filter: blur(8px);
        display: flex; align-items: center;
    }
    .nav-inner {
        max-width: var(--page-max); width: 100%; margin: 0 auto;
        padding: 0 var(--page-padding-x);
        display: flex; align-items: center; justify-content: space-between;
    }
    .nav-logo { font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); }
    .nav-logo span { color: var(--accent); }
    .nav-links { display: flex; align-items: center; gap: 24px; list-style: none; }
    .nav-links a {
        font-family: var(--font-mono); font-size: 11px; color: var(--nav-link);
        letter-spacing: var(--tracking-mono-sm);
        transition: color var(--duration-fast);
    }
    .nav-links a:hover, .nav-links a.active { color: var(--nav-link-active); }
    .nav-right { display: flex; align-items: center; gap: 16px; }
    .nav-search-btn {
        font-family: var(--font-mono); font-size: 11px; color: var(--text-muted);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn); padding: 4px 10px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: border-color var(--duration-fast), color var(--duration-fast);
    }
    .nav-search-btn:hover { border-color: var(--border-strong); color: var(--text-secondary); }
    .theme-toggle {
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); font-size: 14px;
        display: flex; align-items: center;
        transition: color var(--duration-fast);
    }
    .theme-toggle:hover { color: var(--accent); }
    @media (max-width: 767px) {
        .nav-links { display: none; }
        .nav-search-btn { display: none; }
    }

    /* ── PAGE HEADER ──────────────────────────────────────────────── */
    .page-header {
        max-width: var(--page-max); margin: 0 auto;
        padding: 40px var(--page-padding-x) 28px;
        width: 100%;
    }
    .breadcrumb {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
        margin-bottom: 12px;
    }
    .breadcrumb span { color: var(--accent); }
    .page-title {
        font-family: var(--font-display); font-size: clamp(24px, 4vw, 32px);
        font-weight: 800; color: var(--text-primary);
        line-height: var(--leading-display);
        letter-spacing: var(--tracking-display);
    }
    .page-count {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); margin-top: 6px;
        letter-spacing: var(--tracking-mono-xs);
    }

    @media (max-width: 767px) {
        .page-header { padding: 28px var(--page-padding-x-sm) 20px; }
    }

    /* ── LAYOUT ───────────────────────────────────────────────────── */
    .listing-layout {
        max-width: var(--page-max); margin: 0 auto;
        padding: 0 var(--page-padding-x) 64px;
        width: 100%;
        display: grid;
        grid-template-columns: var(--sidebar-width) 1fr;
        gap: 32px;
        align-items: start;
    }
    @media (max-width: 1023px) {
        .listing-layout { grid-template-columns: 1fr; padding: 0 var(--page-padding-x-md) 48px; }
        .sidebar { display: none; }
    }
    @media (max-width: 767px) {
        .listing-layout { padding: 0 var(--page-padding-x-sm) 40px; }
    }

    /* ── SIDEBAR ──────────────────────────────────────────────────── */
    .sidebar {
        position: sticky; top: calc(var(--nav-height) + 20px);
        display: flex; flex-direction: column; gap: 24px;
    }
    .sidebar-section { }
    .sidebar-heading {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .sidebar-heading::after {
        content: ''; flex: 1; height: 0.5px; background: var(--border-default);
    }
    .sidebar-heading .slash { color: var(--accent); margin-right: 2px; }
    .filter-list { list-style: none; display: flex; flex-direction: column; gap: 1px; }
    .filter-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 7px 10px;
        border-radius: 4px; cursor: pointer;
        transition: background var(--duration-fast);
    }
    .filter-item:hover { background: var(--bg-surface-1); }
    .filter-item.active { background: var(--accent-tint); }
    .filter-name {
        font-family: var(--font-sans); font-size: 11px;
        color: var(--text-secondary);
    }
    .filter-item.active .filter-name { color: var(--accent); }
    .filter-count {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint);
    }
    .filter-item.active .filter-count { color: var(--accent); }

    .tag-cloud { display: flex; flex-wrap: wrap; gap: 4px; }
    .tag {
        font-family: var(--font-mono); font-size: 8px;
        padding: 1px 5px; border-radius: var(--radius-tag);
        letter-spacing: var(--tracking-mono-xs); border: 0.5px solid;
        cursor: pointer; transition: opacity var(--duration-fast);
    }
    .tag:hover { opacity: 0.75; }
    .tag-amber { color: var(--accent); background: var(--accent-tint); border-color: var(--accent-border); }
    .tag-teal  { color: var(--secondary); background: var(--secondary-tint); border-color: var(--secondary-border); }
    .tag-coral { color: var(--tertiary); background: var(--tertiary-tint); border-color: var(--tertiary-border); }

    /* ── POST LIST ────────────────────────────────────────────────── */
    .post-list { display: flex; flex-direction: column; }

    /* Year divider */
    .year-divider {
        display: flex; align-items: center; gap: 10px;
        margin: 20px 0 10px;
    }
    .year-divider:first-child { margin-top: 0; }
    .year-divider-line { flex: 1; height: 0.5px; background: var(--divider-line); }
    .year-divider-label {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--divider-label); letter-spacing: 0.08em;
    }

    /* Post row */
    .post-row {
        display: grid;
        grid-template-columns: 52px 1fr;
        gap: 14px; align-items: start;
        padding: 14px 0;
        border-bottom: 0.5px solid var(--border-default);
        cursor: pointer;
        transition: background var(--duration-base) var(--easing-default);
        border-radius: 4px;
        margin: 0 -8px; padding-left: 8px; padding-right: 8px;
    }
    .post-row:hover { background: var(--bg-surface-1); }
    .post-row:last-child { border-bottom: none; }

    /* Date box */
    .datebox {
        background: var(--datebox-bg);
        border: 0.5px solid var(--datebox-border);
        border-radius: var(--radius-datebox);
        width: 52px; min-width: 52px;
        padding: 6px 0;
        display: flex; flex-direction: column; align-items: center; gap: 1px;
    }
    .datebox-day {
        font-family: var(--font-display); font-weight: 800; font-size: 20px;
        color: var(--datebox-day); line-height: 1;
        letter-spacing: var(--tracking-display);
    }
    .datebox-month {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--datebox-month); text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .post-row-body { display: flex; flex-direction: column; gap: 6px; }
    .post-row-tags { display: flex; gap: 4px; flex-wrap: wrap; }
    .post-row-title {
        font-family: var(--font-display); font-weight: 700; font-size: 14px;
        color: var(--text-primary); line-height: var(--leading-heading);
        letter-spacing: var(--tracking-display);
    }
    .post-row-excerpt {
        font-family: var(--font-sans); font-size: 12px;
        color: var(--text-secondary); line-height: var(--leading-body);
        display: -webkit-box; -webkit-box-orient: vertical;
        -webkit-line-clamp: 2; overflow: hidden;
    }
    .post-row-meta {
        display: flex; align-items: center; gap: 8px;
    }
    .post-row-meta-text {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
    }
    .post-row-meta-dot { color: var(--text-faint); font-size: 6px; }
    .like-inline {
        margin-left: auto;
        font-family: var(--font-mono); font-size: 8px;
        color: var(--like-icon); display: flex; align-items: center; gap: 3px;
    }
    .like-inline svg { width: 10px; height: 10px; }

    @media (max-width: 767px) {
        .post-row {
            grid-template-columns: 34px 1fr;
            gap: 10px;
        }
        .datebox {
            width: 34px; min-width: 34px; padding: 4px 0;
        }
        .datebox-day { font-size: 13px; }
        .datebox-month { font-size: 7px; }
    }

    /* ── LOAD MORE ────────────────────────────────────────────────── */
    .load-more {
        text-align: center; margin-top: 32px;
    }
    .btn-ghost {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--text-secondary);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 8px 24px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: border-color var(--duration-fast), color var(--duration-fast);
    }
    .btn-ghost:hover { border-color: var(--border-strong); color: var(--text-primary); }

    /* ── FOOTER ───────────────────────────────────────────────────── */
    .footer {
        background: var(--footer-bg);
        border-top: 0.5px solid var(--footer-border);
        padding: 24px var(--page-padding-x);
    }
    .footer-inner {
        max-width: var(--page-max); margin: 0 auto;
        display: grid; grid-template-columns: 1fr auto 1fr;
        align-items: center; gap: 24px;
    }
    .footer-logo { font-family: var(--font-mono); font-size: 11px; color: var(--footer-text); }
    .footer-logo span { color: var(--accent); }
    .footer-links { display: flex; gap: 20px; list-style: none; justify-content: center; }
    .footer-links a {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
        transition: color var(--duration-fast);
    }
    .footer-links a:hover { color: var(--text-secondary); }
    .lh-badges { display: flex; gap: 6px; justify-content: flex-end; }
    .lh-badge {
        background: var(--lh-bg); border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn); padding: 6px 10px;
        display: flex; flex-direction: column; align-items: center; gap: 2px; min-width: 44px;
    }
    .lh-num { font-family: var(--font-mono); font-weight: 500; font-size: 13px; color: var(--lh-num); }
    .lh-label { font-family: var(--font-mono); font-size: 7px; color: var(--lh-label); letter-spacing: var(--tracking-mono-xs); text-transform: uppercase; }
    @media (max-width: 767px) {
        .footer { padding: 20px var(--page-padding-x-sm); }
        .footer-inner { grid-template-columns: 1fr; gap: 16px; text-align: center; }
        .lh-badges { justify-content: center; }
    }

    /* ── MOBILE TAB BAR ───────────────────────────────────────────── */
    .tab-bar {
        display: none;
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
        background: var(--tab-bg);
        border-top: 0.5px solid var(--border-default);
        padding: 8px 0 max(8px, env(safe-area-inset-bottom));
    }
    .tab-bar-inner { display: flex; justify-content: space-around; }
    .tab-item {
        display: flex; flex-direction: column; align-items: center; gap: 3px;
        cursor: pointer; flex: 1; padding: 4px 0;
    }
    .tab-icon { font-size: 18px; color: var(--tab-icon); }
    .tab-label { font-family: var(--font-mono); font-size: 8px; color: var(--tab-label); letter-spacing: var(--tracking-mono-xs); }
    .tab-item.active .tab-icon  { color: var(--tab-icon-active); }
    .tab-item.active .tab-label { color: var(--tab-label-active); }
    @media (max-width: 767px) {
        .tab-bar { display: block; }
        body { padding-bottom: 60px; }
    }
</style>
@endpush

@section('content')
<!-- NAV -->
<nav class="nav">
    <div class="nav-inner">
        <a href="/" class="nav-logo">dav<span>/</span>devs</a>
        <ul class="nav-links">
            <li><a href="#">home</a></li>
            <li><a href="#" class="active">articles</a></li>
            <li><a href="#">projects</a></li>
            <li><a href="#">tools</a></li>
            <li><a href="#">about</a></li>
        </ul>
        <div class="nav-right">
            <button class="nav-search-btn">⌘ search</button>
            <button class="theme-toggle">◐</button>
        </div>
    </div>
</nav>

<!-- PAGE HEADER -->
<div style="margin-top: var(--nav-height);">
    <div class="page-header">
        <p class="breadcrumb">dav<span>/</span>devs <span style="color:var(--text-faint)">›</span> articles</p>
        <h1 class="page-title">Articles</h1>
        <p class="page-count">48 posts</p>
    </div>

    <!-- LISTING LAYOUT -->
    <div class="listing-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <p class="sidebar-heading"><span class="slash">/</span> category</p>
                <ul class="filter-list">
                    <li class="filter-item active">
                        <span class="filter-name">All</span>
                        <span class="filter-count">48</span>
                    </li>
                    <li class="filter-item">
                        <span class="filter-name">AI</span>
                        <span class="filter-count">14</span>
                    </li>
                    <li class="filter-item">
                        <span class="filter-name">Security</span>
                        <span class="filter-count">9</span>
                    </li>
                    <li class="filter-item">
                        <span class="filter-name">Laravel</span>
                        <span class="filter-count">12</span>
                    </li>
                    <li class="filter-item">
                        <span class="filter-name">JavaScript</span>
                        <span class="filter-count">7</span>
                    </li>
                    <li class="filter-item">
                        <span class="filter-name">Faith</span>
                        <span class="filter-count">6</span>
                    </li>
                </ul>
            </div>
            <div class="sidebar-section">
                <p class="sidebar-heading"><span class="slash">/</span> tags</p>
                <div class="tag-cloud">
                    <span class="tag tag-amber">AI</span>
                    <span class="tag tag-amber">Azure</span>
                    <span class="tag tag-amber">Security</span>
                    <span class="tag tag-teal">Laravel</span>
                    <span class="tag tag-teal">Pest</span>
                    <span class="tag tag-teal">Python</span>
                    <span class="tag tag-teal">JS</span>
                    <span class="tag tag-teal">React</span>
                    <span class="tag tag-coral">Faith</span>
                    <span class="tag tag-coral">E-Book</span>
                    <span class="tag tag-amber">Dev Tools</span>
                    <span class="tag tag-teal">Tailwind</span>
                </div>
            </div>
        </aside>

        <!-- MAIN LIST -->
        <main>
            <div class="post-list">

                <!-- 2026 -->
                <div class="year-divider">
                    <div class="year-divider-line"></div>
                    <span class="year-divider-label">2026</span>
                    <div class="year-divider-line"></div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">28</span>
                        <span class="datebox-month">Jun</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags">
                            <span class="tag tag-amber">AI</span>
                            <span class="tag tag-teal">Laravel</span>
                        </div>
                        <h2 class="post-row-title">Building a Privacy-First CMS with Laravel and Anthropic Claude</h2>
                        <p class="post-row-excerpt">How I migrated my Next.js portfolio to a full Laravel web application — model design, auth, 2FA, and wiring up Claude for AI-assisted content audits.</p>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">8 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                24
                            </span>
                        </div>
                    </div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">25</span>
                        <span class="datebox-month">Jun</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-teal">JS</span></div>
                        <h2 class="post-row-title">Vite 8 Plugin API: What Actually Changed</h2>
                        <p class="post-row-excerpt">A practical rundown of the breaking changes in Vite 8's plugin API and what you need to update.</p>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">4 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                6
                            </span>
                        </div>
                    </div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">22</span>
                        <span class="datebox-month">Jun</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-coral">Faith</span></div>
                        <h2 class="post-row-title">On Rest as a Developer Practice</h2>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">3 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                19
                            </span>
                        </div>
                    </div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">18</span>
                        <span class="datebox-month">Jun</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-amber">Azure</span></div>
                        <h2 class="post-row-title">Azure B2C Custom Policies: A Survival Guide</h2>
                        <p class="post-row-excerpt">Everything I wish I'd known before spending three days debugging XML policy files.</p>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">9 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                33
                            </span>
                        </div>
                    </div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">15</span>
                        <span class="datebox-month">Jun</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-teal">Laravel</span><span class="tag tag-teal">Pest</span></div>
                        <h2 class="post-row-title">Pest 3 + Laravel 12: Writing Tests That Actually Catch Bugs</h2>
                        <p class="post-row-excerpt">Practical patterns for architecture testing, mutation coverage, and keeping your test suite honest.</p>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">5 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                11
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2025 -->
                <div class="year-divider">
                    <div class="year-divider-line"></div>
                    <span class="year-divider-label">2025</span>
                    <div class="year-divider-line"></div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">14</span>
                        <span class="datebox-month">Nov</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-amber">AI</span><span class="tag tag-amber">Security</span></div>
                        <h2 class="post-row-title">Prompt Injection is the New SQL Injection</h2>
                        <p class="post-row-excerpt">Why developers building LLM integrations need to take adversarial prompts as seriously as user-supplied SQL.</p>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">7 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                41
                            </span>
                        </div>
                    </div>
                </div>

                <div class="post-row">
                    <div class="datebox">
                        <span class="datebox-day">02</span>
                        <span class="datebox-month">Aug</span>
                    </div>
                    <div class="post-row-body">
                        <div class="post-row-tags"><span class="tag tag-teal">Python</span></div>
                        <h2 class="post-row-title">Structuring Outputs from LLMs Without Losing Your Mind</h2>
                        <div class="post-row-meta">
                            <span class="post-row-meta-text">7 min read</span>
                            <span class="post-row-meta-dot">·</span>
                            <span class="post-row-meta-text">Article</span>
                            <span class="like-inline">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                                </svg>
                                15
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="load-more">
                <button class="btn-ghost">load more posts</button>
            </div>
        </main>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-logo">dav<span>/</span>devs · © 2026 Davina Leong</div>
        <ul class="footer-links">
            <li><a href="#">rss</a></li>
            <li><a href="#">github</a></li>
            <li><a href="#">linkedin</a></li>
            <li><a href="#">sitemap</a></li>
        </ul>
        <div class="lh-badges">
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">Perf</span></div>
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">A11y</span></div>
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">SEO</span></div>
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">BP</span></div>
        </div>
    </div>
</footer>

<!-- MOBILE TAB BAR -->
<div class="tab-bar">
    <div class="tab-bar-inner">
        <div class="tab-item"><span class="tab-icon">⌂</span><span class="tab-label">home</span></div>
        <div class="tab-item active"><span class="tab-icon">☰</span><span class="tab-label">browse</span></div>
        <div class="tab-item"><span class="tab-icon">⌕</span><span class="tab-label">search</span></div>
        <div class="tab-item"><span class="tab-icon">◐</span><span class="tab-label">theme</span></div>
    </div>
</div>
@endsection
