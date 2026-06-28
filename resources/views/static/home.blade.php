@extends('static.layouts.site')

@section('title', 'Dav/Devs — Davina Develops')

@push('head')
<style>
    /* ── NAV ─────────────────────────────────────────────────────── */
    .nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        height: var(--nav-height);
        background: var(--nav-bg);
        border-bottom: 0.5px solid var(--nav-border);
        backdrop-filter: blur(8px);
        display: flex; align-items: center;
    }
    .nav-inner {
        max-width: var(--page-max);
        width: 100%; margin: 0 auto;
        padding: 0 var(--page-padding-x);
        display: flex; align-items: center; justify-content: space-between;
    }
    .nav-logo {
        font-family: var(--font-mono); font-size: 13px;
        color: var(--text-muted);
    }
    .nav-logo span { color: var(--accent); }
    .nav-links {
        display: flex; align-items: center; gap: 24px;
        list-style: none;
    }
    .nav-links a {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--nav-link);
        letter-spacing: var(--tracking-mono-sm);
        transition: color var(--duration-fast);
    }
    .nav-links a:hover, .nav-links a.active { color: var(--nav-link-active); }
    .nav-right { display: flex; align-items: center; gap: 16px; }
    .nav-search-btn {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--text-muted);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 4px 10px; cursor: pointer;
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

    /* hide nav links on mobile, show tab bar instead */
    @media (max-width: 767px) {
        .nav-links { display: none; }
        .nav-search-btn { display: none; }
    }

    /* ── HERO ─────────────────────────────────────────────────────── */
    .hero {
        margin-top: var(--nav-height);
        padding: 72px var(--page-padding-x) 56px;
        max-width: var(--page-max); margin-left: auto; margin-right: auto;
        width: 100%;
    }
    .hero-label {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 16px;
    }
    .hero-wordmark {
        font-family: var(--font-display); font-size: clamp(32px, 5vw, 48px);
        font-weight: 800; color: var(--text-primary);
        line-height: var(--leading-display);
        letter-spacing: var(--tracking-display);
        margin-bottom: 4px;
    }
    .hero-wordmark .slash { color: var(--accent); }
    .hero-wordmark .cursor {
        display: inline-block; width: 3px; height: 0.9em;
        background: var(--accent); margin-left: 2px;
        vertical-align: middle;
        animation: cursor-blink 1.1s step-end infinite;
    }
    @keyframes cursor-blink {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0; }
    }
    .hero-sub {
        font-family: var(--font-sans); font-size: 13px;
        color: var(--text-secondary); line-height: var(--leading-body);
        max-width: 480px; margin-top: 16px;
    }
    .hero-actions {
        display: flex; gap: 10px; margin-top: 28px; flex-wrap: wrap;
    }
    .btn-primary {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--bg-page); background: var(--accent);
        border: none; border-radius: var(--radius-btn);
        padding: 8px 18px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: background var(--duration-fast);
    }
    .btn-primary:hover { background: var(--accent-hover); }
    .btn-ghost {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--text-secondary);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 8px 18px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: border-color var(--duration-fast), color var(--duration-fast);
    }
    .btn-ghost:hover { border-color: var(--border-strong); color: var(--text-primary); }

    @media (max-width: 767px) {
        .hero { padding: 48px var(--page-padding-x-sm) 36px; margin-top: var(--nav-height); }
    }

    /* ── SECTION LABEL ────────────────────────────────────────────── */
    .section-label {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase;
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 20px;
    }
    .section-label::after {
        content: ''; flex: 1; height: 0.5px;
        background: var(--border-default);
    }
    .section-label .accent-slash { color: var(--accent); margin-right: 2px; }

    /* ── FEATURED GRID ────────────────────────────────────────────── */
    .section {
        max-width: var(--page-max); margin: 0 auto;
        padding: 0 var(--page-padding-x) 56px;
        width: 100%;
    }
    .post-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1px;
        background: var(--border-default);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-card);
        overflow: hidden;
    }
    .post-card {
        background: var(--bg-page);
        padding: 16px 18px;
        display: flex; flex-direction: column; gap: 10px;
        transition: background var(--duration-base) var(--easing-default);
        cursor: pointer;
    }
    .post-card:hover { background: var(--bg-surface-1); }
    .post-card.featured {
        grid-column: span 2;
        background: var(--bg-surface-1);
    }
    .post-card.featured:hover { background: var(--bg-surface-2); }
    .card-tags { display: flex; gap: 4px; flex-wrap: wrap; }
    .tag {
        font-family: var(--font-mono); font-size: 8px;
        padding: 1px 5px; border-radius: var(--radius-tag);
        letter-spacing: var(--tracking-mono-xs);
        border: 0.5px solid;
    }
    .tag-amber { color: var(--accent); background: var(--accent-tint); border-color: var(--accent-border); }
    .tag-teal  { color: var(--secondary); background: var(--secondary-tint); border-color: var(--secondary-border); }
    .tag-coral { color: var(--tertiary); background: var(--tertiary-tint); border-color: var(--tertiary-border); }
    .card-title {
        font-family: var(--font-display); font-weight: 700;
        color: var(--text-primary);
        line-height: var(--leading-heading);
        letter-spacing: var(--tracking-display);
    }
    .card-title-featured { font-size: 16px; }
    .card-title-std      { font-size: 14px; }
    .card-excerpt {
        font-family: var(--font-sans); font-size: 12px;
        color: var(--text-secondary); line-height: var(--leading-body);
        display: -webkit-box; -webkit-box-orient: vertical;
        -webkit-line-clamp: 2; overflow: hidden;
    }
    .card-meta {
        display: flex; align-items: center; gap: 8px;
        margin-top: auto;
    }
    .card-meta-text {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
    }
    .card-meta-dot { color: var(--text-faint); font-size: 6px; }
    .like-inline {
        margin-left: auto;
        font-family: var(--font-mono); font-size: 8px;
        color: var(--like-icon); display: flex; align-items: center; gap: 3px;
    }
    .like-inline svg { width: 10px; height: 10px; }

    @media (max-width: 767px) {
        .section { padding: 0 var(--page-padding-x-sm) 40px; }
        .post-grid { grid-template-columns: 1fr; }
        .post-card.featured { grid-column: span 1; }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
        .section { padding: 0 var(--page-padding-x-md) 48px; }
        .post-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ── CATEGORY NAV STRIP ───────────────────────────────────────── */
    .cat-strip {
        display: flex; gap: 1px;
        background: var(--border-default);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-card);
        overflow: hidden; margin-bottom: 24px;
    }
    .cat-item {
        flex: 1; background: var(--bg-page);
        padding: 14px 12px;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        cursor: pointer;
        transition: background var(--duration-base) var(--easing-default);
    }
    .cat-item:hover { background: var(--bg-surface-1); }
    .cat-item.active { background: var(--bg-surface-1); }
    .cat-icon {
        font-size: 16px;
    }
    .cat-name {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-align: center;
    }
    .cat-count {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint);
    }
    .cat-item.active .cat-name { color: var(--accent); }
    .cat-item.active .cat-count { color: var(--accent); }

    @media (max-width: 767px) {
        .cat-strip { overflow-x: auto; flex-wrap: nowrap; }
        .cat-item { min-width: 70px; }
    }

    /* ── STATS BAR ────────────────────────────────────────────────── */
    .stats-bar {
        display: flex; gap: 1px;
        background: var(--border-default);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-card);
        overflow: hidden; margin-bottom: 56px;
    }
    .stat-item {
        flex: 1; background: var(--bg-surface-2);
        padding: 18px 16px;
        display: flex; flex-direction: column; gap: 4px; align-items: center;
    }
    .stat-num {
        font-family: var(--font-display); font-weight: 800; font-size: 20px;
        color: var(--text-primary); letter-spacing: var(--tracking-display);
    }
    .stat-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; text-align: center;
    }

    /* ── FOOTER ───────────────────────────────────────────────────── */
    .footer {
        margin-top: auto;
        background: var(--footer-bg);
        border-top: 0.5px solid var(--footer-border);
        padding: 24px var(--page-padding-x);
    }
    .footer-inner {
        max-width: var(--page-max); margin: 0 auto;
        display: grid; grid-template-columns: 1fr auto 1fr;
        align-items: center; gap: 24px;
    }
    .footer-logo {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--footer-text);
    }
    .footer-logo span { color: var(--accent); }
    .footer-links {
        display: flex; gap: 20px; list-style: none; justify-content: center;
    }
    .footer-links a {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
        transition: color var(--duration-fast);
    }
    .footer-links a:hover { color: var(--text-secondary); }
    .lh-badges {
        display: flex; gap: 6px; justify-content: flex-end;
    }
    .lh-badge {
        background: var(--lh-bg);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 6px 10px;
        display: flex; flex-direction: column; align-items: center; gap: 2px;
        min-width: 44px;
    }
    .lh-num {
        font-family: var(--font-mono); font-weight: 500; font-size: 13px;
        color: var(--lh-num);
    }
    .lh-label {
        font-family: var(--font-mono); font-size: 7px;
        color: var(--lh-label); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase;
    }

    @media (max-width: 767px) {
        .footer { padding: 20px var(--page-padding-x-sm); }
        .footer-inner { grid-template-columns: 1fr; gap: 16px; text-align: center; }
        .footer-links { flex-wrap: wrap; }
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
    .tab-bar-inner {
        display: flex; justify-content: space-around;
    }
    .tab-item {
        display: flex; flex-direction: column; align-items: center; gap: 3px;
        cursor: pointer; flex: 1; padding: 4px 0;
    }
    .tab-icon { font-size: 18px; color: var(--tab-icon); }
    .tab-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--tab-label); letter-spacing: var(--tracking-mono-xs);
    }
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
            <li><a href="#" class="active">home</a></li>
            <li><a href="#">articles</a></li>
            <li><a href="#">projects</a></li>
            <li><a href="#">tools</a></li>
            <li><a href="#">about</a></li>
        </ul>
        <div class="nav-right">
            <button class="nav-search-btn">⌘ search</button>
            <button class="theme-toggle" title="Toggle theme">◐</button>
        </div>
    </div>
</nav>

<!-- HERO -->
<div style="margin-top: var(--nav-height);">
    <div class="hero">
        <p class="hero-label"><span class="accent-slash">/</span> hello, world</p>
        <h1 class="hero-wordmark">Dav<span class="slash">/</span>Devs<span class="cursor"></span></h1>
        <p class="hero-sub">
            A personal dev journal by Davina Leong — articles, projects, tools, notebooks,
            and the occasional sermon or e-book. Whatever I build, I write about one.
        </p>
        <div class="hero-actions">
            <button class="btn-primary">read latest →</button>
            <button class="btn-ghost">browse all</button>
        </div>
    </div>

    <!-- STATS -->
    <div class="section">
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-num">48</span>
                <span class="stat-label">Articles</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">12</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">24</span>
                <span class="stat-label">Tools</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">7</span>
                <span class="stat-label">E-Books</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">3</span>
                <span class="stat-label">Sermons</span>
            </div>
        </div>
    </div>

    <!-- FEATURED POSTS -->
    <div class="section">
        <p class="section-label"><span class="accent-slash">/</span> featured</p>
        <div class="post-grid">
            <!-- Featured card (spans 2 cols) -->
            <div class="post-card featured">
                <div class="card-tags">
                    <span class="tag tag-amber">AI</span>
                    <span class="tag tag-amber">Laravel</span>
                </div>
                <h2 class="card-title card-title-featured">
                    Building a Privacy-First CMS with Laravel and Anthropic Claude
                </h2>
                <p class="card-excerpt">
                    Migrated my Next.js portfolio to full Laravel — model design, auth, 2FA, and Claude
                    for AI content audits. Quite a journey, not gonna lie.
                </p>
                <div class="card-meta">
                    <span class="card-meta-text">28 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">8 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        24
                    </span>
                </div>
            </div>
            <!-- Standard card -->
            <div class="post-card">
                <div class="card-tags">
                    <span class="tag tag-teal">Laravel</span>
                </div>
                <h3 class="card-title card-title-std">
                    Pest 3 + Laravel 12: Writing Tests That Actually Catch Bugs
                </h3>
                <p class="card-excerpt">
                    Practical patterns for architecture testing, mutation coverage, and keeping your test suite honest.
                </p>
                <div class="card-meta">
                    <span class="card-meta-text">15 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">5 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        11
                    </span>
                </div>
            </div>
            <!-- Standard card -->
            <div class="post-card">
                <div class="card-tags">
                    <span class="tag tag-amber">Security</span>
                </div>
                <h3 class="card-title card-title-std">
                    TOTP 2FA in Laravel Without Breeze Magic
                </h3>
                <p class="card-excerpt">
                    Implementing QR-code 2FA from scratch with pragmarx/google2fa — setup, challenge, recovery. Quite shiok once it all clicks.
                </p>
                <div class="card-meta">
                    <span class="card-meta-text">01 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">6 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        8
                    </span>
                </div>
            </div>
            <!-- Standard card -->
            <div class="post-card">
                <div class="card-tags">
                    <span class="tag tag-teal">Python</span>
                    <span class="tag tag-amber">AI</span>
                </div>
                <h3 class="card-title card-title-std">
                    Structuring Outputs from LLMs Without Losing Your Mind
                </h3>
                <p class="card-excerpt">
                    Validation patterns, retries, and schema pinning when you can't trust the model to behave.
                </p>
                <div class="card-meta">
                    <span class="card-meta-text">20 May 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">7 min read</span>
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

    <!-- BROWSE BY TYPE -->
    <div class="section">
        <p class="section-label"><span class="accent-slash">/</span> browse by type</p>
        <div class="cat-strip">
            <div class="cat-item active">
                <span class="cat-icon">✦</span>
                <span class="cat-name">All</span>
                <span class="cat-count">94</span>
            </div>
            <div class="cat-item">
                <span class="cat-icon">📝</span>
                <span class="cat-name">Articles</span>
                <span class="cat-count">48</span>
            </div>
            <div class="cat-item">
                <span class="cat-icon">🛠</span>
                <span class="cat-name">Projects</span>
                <span class="cat-count">12</span>
            </div>
            <div class="cat-item">
                <span class="cat-icon">⚙️</span>
                <span class="cat-name">Tools</span>
                <span class="cat-count">24</span>
            </div>
            <div class="cat-item">
                <span class="cat-icon">📓</span>
                <span class="cat-name">Notebooks</span>
                <span class="cat-count">6</span>
            </div>
            <div class="cat-item">
                <span class="cat-icon">📖</span>
                <span class="cat-name">E-Books</span>
                <span class="cat-count">4</span>
            </div>
        </div>
    </div>

    <!-- RECENT POSTS -->
    <div class="section">
        <p class="section-label"><span class="accent-slash">/</span> recent posts</p>
        <div class="post-grid">
            <div class="post-card">
                <div class="card-tags"><span class="tag tag-teal">JS</span></div>
                <h3 class="card-title card-title-std">Vite 8 Plugin API: What Actually Changed</h3>
                <div class="card-meta">
                    <span class="card-meta-text">25 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">4 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        6
                    </span>
                </div>
            </div>
            <div class="post-card">
                <div class="card-tags"><span class="tag tag-coral">Faith</span></div>
                <h3 class="card-title card-title-std">On Rest as a Developer Practice</h3>
                <div class="card-meta">
                    <span class="card-meta-text">22 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">3 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        19
                    </span>
                </div>
            </div>
            <div class="post-card">
                <div class="card-tags"><span class="tag tag-amber">Azure</span></div>
                <h3 class="card-title card-title-std">Azure B2C Custom Policies: A Survival Guide</h3>
                <div class="card-meta">
                    <span class="card-meta-text">18 Jun 2026</span>
                    <span class="card-meta-dot">·</span>
                    <span class="card-meta-text">9 min read</span>
                    <span class="like-inline">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                        </svg>
                        33
                    </span>
                </div>
            </div>
        </div>
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
        <div class="tab-item active">
            <span class="tab-icon">⌂</span>
            <span class="tab-label">home</span>
        </div>
        <div class="tab-item">
            <span class="tab-icon">☰</span>
            <span class="tab-label">browse</span>
        </div>
        <div class="tab-item">
            <span class="tab-icon">⌕</span>
            <span class="tab-label">search</span>
        </div>
        <div class="tab-item">
            <span class="tab-icon">◐</span>
            <span class="tab-label">theme</span>
        </div>
    </div>
</div>
@endsection
