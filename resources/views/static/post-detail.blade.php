@extends('static.layouts.site')

@section('title', 'Building a Privacy-First CMS with Laravel and Anthropic Claude — Dav/Devs')

@push('head')
<style>
    /* ── PROGRESS BAR ─────────────────────────────────────────────── */
    .progress-bar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 200;
        height: 2px; background: var(--progress-bg);
    }
    .progress-fill {
        height: 100%; width: 38%;
        background: var(--progress-fill);
        transition: width 0.1s linear;
    }

    /* ── NAV ─────────────────────────────────────────────────────── */
    .nav {
        position: fixed; top: 2px; left: 0; right: 0; z-index: 100;
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
    }
    .theme-toggle {
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); font-size: 14px; display: flex; align-items: center;
    }
    .theme-toggle:hover { color: var(--accent); }
    @media (max-width: 767px) {
        .nav-links { display: none; }
        .nav-search-btn { display: none; }
    }

    /* ── POST LAYOUT ──────────────────────────────────────────────── */
    .post-wrapper {
        margin-top: calc(var(--nav-height) + 2px);
        max-width: var(--page-max); margin-left: auto; margin-right: auto;
        padding: 0 var(--page-padding-x);
        width: 100%;
        display: grid;
        grid-template-columns: 1fr var(--prose-max) var(--rail-width);
        gap: 48px;
        padding-top: 40px; padding-bottom: 80px;
    }
    @media (max-width: 1100px) {
        .post-wrapper {
            grid-template-columns: 1fr;
            padding: 24px var(--page-padding-x-md) 60px;
        }
        .post-back-col, .post-rail { display: none; }
    }
    @media (max-width: 767px) {
        .post-wrapper { padding: 20px var(--page-padding-x-sm) 80px; }
    }

    /* Back column (desktop) */
    .post-back-col {
        display: flex; justify-content: flex-end; padding-top: 4px;
    }
    .back-link {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-sm);
        display: flex; align-items: center; gap: 4px;
        transition: color var(--duration-fast);
        white-space: nowrap;
    }
    .back-link:hover { color: var(--accent); }

    /* ── POST HEADER ──────────────────────────────────────────────── */
    .post-header { margin-bottom: 36px; }
    .post-breadcrumb {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
        margin-bottom: 16px;
    }
    .post-breadcrumb span { color: var(--accent); }
    .post-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 14px; }
    .tag {
        font-family: var(--font-mono); font-size: 8px;
        padding: 1px 5px; border-radius: var(--radius-tag);
        letter-spacing: var(--tracking-mono-xs); border: 0.5px solid;
    }
    .tag-amber { color: var(--accent); background: var(--accent-tint); border-color: var(--accent-border); }
    .tag-teal  { color: var(--secondary); background: var(--secondary-tint); border-color: var(--secondary-border); }
    .tag-coral { color: var(--tertiary); background: var(--tertiary-tint); border-color: var(--tertiary-border); }
    .post-title {
        font-family: var(--font-display); font-weight: 800;
        font-size: clamp(20px, 3vw, 26px);
        color: var(--text-primary);
        line-height: var(--leading-display);
        letter-spacing: var(--tracking-display);
        margin-bottom: 16px;
    }
    .post-meta {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding-bottom: 20px;
        border-bottom: 0.5px solid var(--border-default);
    }
    .post-meta-text {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
    }
    .post-meta-dot { color: var(--text-faint); font-size: 6px; }
    .post-datebox {
        background: var(--datebox-bg);
        border: 0.5px solid var(--datebox-border);
        border-radius: var(--radius-datebox);
        padding: 4px 8px;
        display: flex; align-items: baseline; gap: 4px;
    }
    .datebox-day {
        font-family: var(--font-display); font-weight: 800; font-size: 16px;
        color: var(--datebox-day); line-height: 1;
    }
    .datebox-month {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--datebox-month); text-transform: uppercase; letter-spacing: 0.05em;
    }
    .like-btn {
        margin-left: auto;
        background: none; border: none; cursor: pointer;
        display: flex; align-items: center; gap: 4px;
        font-family: var(--font-mono); font-size: 9px;
        color: var(--like-icon);
        transition: color var(--duration-fast);
    }
    .like-btn:hover { color: var(--like-icon-liked); }
    .like-btn svg { width: 12px; height: 12px; }

    /* ── PROSE CONTENT ────────────────────────────────────────────── */
    .post-content {
        font-family: var(--font-prose);
        font-size: 14px;
        line-height: var(--leading-prose);
        color: var(--text-body);
    }
    .post-content p { margin-bottom: 1.4em; }
    .post-content h2 {
        font-family: var(--font-display); font-weight: 700; font-size: 18px;
        color: var(--text-primary); line-height: var(--leading-heading);
        letter-spacing: var(--tracking-display);
        margin: 2em 0 0.6em;
    }
    .post-content h3 {
        font-family: var(--font-display); font-weight: 700; font-size: 15px;
        color: var(--text-primary); line-height: var(--leading-heading);
        letter-spacing: var(--tracking-display);
        margin: 1.6em 0 0.5em;
    }
    .post-content a { color: var(--accent); border-bottom: 0.5px solid var(--accent-border); }
    .post-content a:hover { color: var(--accent-hover); }
    .post-content strong { font-weight: 400; color: var(--text-primary); font-style: normal; }
    .post-content em { color: var(--text-secondary); }
    .post-content code {
        font-family: var(--font-mono); font-size: 12px;
        background: var(--bg-surface-2); color: var(--accent);
        padding: 1px 5px; border-radius: 3px;
    }
    .post-content pre {
        background: var(--bg-surface-2);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-code, 6px);
        padding: 16px 18px; margin: 1.4em 0;
        overflow-x: auto;
    }
    .post-content pre code {
        background: none; color: var(--text-body);
        font-size: 12px; padding: 0;
    }
    .post-content blockquote {
        border-left: 2px solid var(--accent);
        padding-left: 16px; margin: 1.4em 0;
        font-style: italic; color: var(--text-secondary);
    }
    .post-content ul, .post-content ol {
        padding-left: 20px; margin-bottom: 1.4em;
    }
    .post-content li { margin-bottom: 0.4em; }

    /* ── SHARE + LIKE ROW ─────────────────────────────────────────── */
    .post-footer-row {
        display: flex; align-items: center; gap: 12px;
        margin-top: 48px; padding-top: 24px;
        border-top: 0.5px solid var(--border-default);
        flex-wrap: wrap;
    }
    .share-label {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
    }
    .share-btn {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-secondary);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn); padding: 4px 10px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: border-color var(--duration-fast), color var(--duration-fast);
    }
    .share-btn:hover { border-color: var(--border-strong); color: var(--text-primary); }
    .like-btn-lg {
        margin-left: auto;
        display: flex; align-items: center; gap: 6px;
        font-family: var(--font-mono); font-size: 10px;
        color: var(--like-icon);
        background: none; border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn); padding: 6px 14px; cursor: pointer;
        transition: color var(--duration-fast), border-color var(--duration-fast);
    }
    .like-btn-lg:hover { color: var(--like-icon-liked); border-color: var(--tertiary-border); }
    .like-btn-lg svg { width: 12px; height: 12px; }

    /* ── RIGHT RAIL ───────────────────────────────────────────────── */
    .post-rail {
        position: sticky; top: calc(var(--nav-height) + 24px);
        display: flex; flex-direction: column; gap: 20px;
    }
    .rail-section { }
    .rail-heading {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .rail-heading::after { content: ''; flex: 1; height: 0.5px; background: var(--border-default); }
    .rail-heading .slash { color: var(--accent); margin-right: 2px; }
    .toc-list { list-style: none; display: flex; flex-direction: column; gap: 1px; }
    .toc-item a {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-sm);
        display: block; padding: 5px 8px;
        border-radius: 3px; border-left: 1.5px solid transparent;
        transition: color var(--duration-fast), border-color var(--duration-fast), background var(--duration-fast);
    }
    .toc-item a:hover { color: var(--text-secondary); background: var(--bg-surface-1); }
    .toc-item.active a { color: var(--accent); border-left-color: var(--accent); background: var(--accent-tint); }
    .rail-stat {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; border-bottom: 0.5px solid var(--border-default);
    }
    .rail-stat:last-child { border-bottom: none; }
    .rail-stat-label { font-family: var(--font-mono); font-size: 9px; color: var(--text-muted); letter-spacing: var(--tracking-mono-xs); }
    .rail-stat-value { font-family: var(--font-mono); font-size: 9px; color: var(--text-secondary); }

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
    .tab-item { display: flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; flex: 1; padding: 4px 0; }
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
<!-- PROGRESS BAR -->
<div class="progress-bar"><div class="progress-fill"></div></div>

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

<!-- POST WRAPPER: back col | prose | right rail -->
<div class="post-wrapper" style="margin-top: calc(var(--nav-height) + 2px);">

    <!-- BACK COL -->
    <div class="post-back-col">
        <a href="#" class="back-link">← articles</a>
    </div>

    <!-- PROSE COLUMN -->
    <article>
        <header class="post-header">
            <p class="post-breadcrumb">dav<span>/</span>devs <span style="color:var(--text-faint)">›</span> articles</p>
            <div class="post-tags">
                <span class="tag tag-amber">AI</span>
                <span class="tag tag-teal">Laravel</span>
                <span class="tag tag-amber">Security</span>
            </div>
            <h1 class="post-title">Building a Privacy-First CMS with Laravel and Anthropic Claude</h1>
            <div class="post-meta">
                <div class="post-datebox">
                    <span class="datebox-day">28</span>
                    <span class="datebox-month">Jun 2026</span>
                </div>
                <span class="post-meta-dot">·</span>
                <span class="post-meta-text">8 min read</span>
                <span class="post-meta-dot">·</span>
                <span class="post-meta-text">Article</span>
                <button class="like-btn" aria-label="Like this post">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                    </svg>
                    24
                </button>
            </div>
        </header>

        <div class="post-content">
            <p>
                Moving a Next.js portfolio to Laravel isn't just a framework swap — it's a rethink of every
                architectural decision you made when speed-to-ship was the only goal. This post documents
                what I built, why I made each call, and where Claude fit into the workflow.
            </p>

            <h2>Why Laravel</h2>
            <p>
                The short answer: I wanted a <strong>web application</strong>, not a static site with a CMS bolted on.
                Next.js is excellent at what it does, but after three years I kept reaching for things it wasn't
                designed for — a proper database layer, queues, background jobs, typed model relationships.
            </p>
            <p>
                Laravel gives me all of that with <code>Eloquent</code>, <code>Horizon</code>, and
                first-class Blade templating. Tailwind v4 + Alpine keeps the frontend light. No bundler
                complexity, no hydration mismatches.
            </p>

            <h2>The Privacy-First Constraint</h2>
            <p>
                Privacy-first for me means: no third-party tracking scripts, no comment system
                (and the data liability that comes with it), and an anonymous like system that can't be
                reverse-engineered to identify visitors.
            </p>
            <blockquote>
                The like system stores a hashed fingerprint — browser + IP + post ID — with a 24-hour TTL.
                No user account, no cookie, no persistent identifier.
            </blockquote>
            <p>
                For authentication on the CMS side I went with Laravel Breeze, disabled public registration,
                and added TOTP 2FA via <code>pragmarx/google2fa-laravel</code>.
            </p>

            <h2>Database Schema Overview</h2>
            <p>
                The schema is wider than a typical blog CMS because it needs to handle nine post types:
                Page, Project, Article, Tool, Notebook, Knowledge Sharing, Frontend Mentor, Sermon, and E-Book.
            </p>
            <p>
                Rather than a separate table per type, I went with a single <code>posts</code> table plus
                a polymorphic <code>post_meta</code> table for type-specific fields. Read time is
                calculated programmatically on every save.
            </p>

            <pre><code>// Post model — reading time observer
Post::saving(function (Post $post) {
    $words = str_word_count(strip_tags($post->content));
    $post->read_time_minutes = (int) ceil($words / 200);
});</code></pre>

            <h2>Where Claude Comes In</h2>
            <p>
                I wired up Claude (<code>claude-sonnet-4-6</code>) for two CMS features: draft generation
                from a title + outline, and post auditing — checking reading level, SEO signals, and
                whether the content matches the declared tags.
            </p>
            <p>
                The audit runs on demand, not automatically. I didn't want the AI writing for me — I
                wanted it reading over my shoulder.
            </p>

            <h3>The Audit Prompt</h3>
            <p>
                The system prompt gives Claude the post title, tags, and category. The user turn
                is the raw Markdown content. The response is structured JSON: a score per dimension,
                a list of suggestions, and a one-sentence summary.
            </p>
        </div>

        <!-- SHARE + LIKE -->
        <div class="post-footer-row">
            <span class="share-label">share</span>
            <button class="share-btn">LinkedIn</button>
            <button class="share-btn">Threads</button>
            <button class="share-btn">copy link</button>
            <button class="like-btn-lg" aria-label="Like this post">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                </svg>
                like · 24
            </button>
        </div>
    </article>

    <!-- RIGHT RAIL -->
    <aside class="post-rail">
        <div class="rail-section">
            <p class="rail-heading"><span class="slash">/</span> on this page</p>
            <ul class="toc-list">
                <li class="toc-item"><a href="#">Why Laravel</a></li>
                <li class="toc-item active"><a href="#">The Privacy-First Constraint</a></li>
                <li class="toc-item"><a href="#">Database Schema Overview</a></li>
                <li class="toc-item"><a href="#">Where Claude Comes In</a></li>
                <li class="toc-item"><a href="#">The Audit Prompt</a></li>
            </ul>
        </div>
        <div class="rail-section">
            <p class="rail-heading"><span class="slash">/</span> post info</p>
            <div class="rail-stat">
                <span class="rail-stat-label">published</span>
                <span class="rail-stat-value">28 Jun 2026</span>
            </div>
            <div class="rail-stat">
                <span class="rail-stat-label">read time</span>
                <span class="rail-stat-value">8 min</span>
            </div>
            <div class="rail-stat">
                <span class="rail-stat-label">type</span>
                <span class="rail-stat-value">Article</span>
            </div>
            <div class="rail-stat">
                <span class="rail-stat-label">likes</span>
                <span class="rail-stat-value">24</span>
            </div>
        </div>
    </aside>
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
