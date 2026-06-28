@extends('static.layouts.site')

@section('title', 'Designing for Privacy: A Field Guide — Dav/Devs')

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

    /* ── EBOOK TOKENS ─────────────────────────────────────────────── */
    :root {
        --ebook-coral:        #C4553A;
        --ebook-coral-tint:   rgba(196,85,58,0.08);
        --ebook-coral-border: rgba(196,85,58,0.22);
        --ebook-coral-hover:  rgba(196,85,58,0.14);
    }

    /* ── PAGE WRAPPER ─────────────────────────────────────────────── */
    .ebook-wrapper {
        margin-top: var(--nav-height);
        max-width: var(--page-max);
        margin-left: auto; margin-right: auto;
        padding: 0 var(--page-padding-x) 80px;
    }

    /* ── HERO BANNER ──────────────────────────────────────────────── */
    .ebook-hero {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 40px;
        align-items: start;
        padding: 48px 0 40px;
        border-bottom: 0.5px solid var(--border-default);
    }
    @media (max-width: 767px) {
        .ebook-hero {
            grid-template-columns: 1fr;
            gap: 24px;
            padding: 28px 0 28px;
        }
    }

    /* Cover mock */
    .ebook-cover {
        aspect-ratio: 3/4;
        background: var(--bg-surface-1);
        border: 0.5px solid var(--border-default);
        border-radius: 6px;
        display: flex; flex-direction: column;
        overflow: hidden;
        box-shadow: 4px 6px 24px rgba(0,0,0,0.35);
        position: relative;
    }
    .cover-spine {
        width: 8px;
        background: var(--ebook-coral);
        position: absolute; left: 0; top: 0; bottom: 0;
    }
    .cover-body {
        flex: 1; padding: 24px 18px 18px 26px;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .cover-type-badge {
        font-family: var(--font-mono); font-size: 7px;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--ebook-coral);
        background: var(--ebook-coral-tint);
        border: 0.5px solid var(--ebook-coral-border);
        border-radius: var(--radius-tag);
        padding: 2px 6px; width: fit-content;
        margin-bottom: 12px;
    }
    .cover-title {
        font-family: var(--font-display); font-weight: 800;
        font-size: 15px; color: var(--text-primary);
        line-height: 1.3; letter-spacing: var(--tracking-display);
    }
    .cover-subtitle {
        font-family: var(--font-prose); font-style: italic;
        font-size: 10px; color: var(--text-muted);
        margin-top: 6px; line-height: 1.4;
    }
    .cover-author {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
        margin-top: auto; padding-top: 16px;
        border-top: 0.5px solid var(--border-default);
    }
    .cover-edition {
        font-family: var(--font-mono); font-size: 7px;
        color: var(--ebook-coral); margin-top: 3px;
    }

    /* Hero meta */
    .ebook-meta-col {
        display: flex; flex-direction: column; gap: 20px;
        padding-top: 4px;
    }
    .ebook-breadcrumb {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-sm);
    }
    .ebook-breadcrumb span { color: var(--accent); }
    .ebook-title {
        font-family: var(--font-display); font-weight: 800;
        font-size: clamp(22px, 3.5vw, 32px);
        color: var(--text-primary);
        line-height: var(--leading-display);
        letter-spacing: var(--tracking-display);
    }
    .ebook-subtitle {
        font-family: var(--font-prose); font-style: italic;
        font-size: 14px; color: var(--text-muted); margin-top: -12px;
    }
    .ebook-tags { display: flex; gap: 4px; flex-wrap: wrap; }
    .tag {
        font-family: var(--font-mono); font-size: 8px;
        padding: 1px 5px; border-radius: var(--radius-tag);
        letter-spacing: var(--tracking-mono-xs); border: 0.5px solid;
    }
    .tag-amber { color: var(--accent); background: var(--accent-tint); border-color: var(--accent-border); }
    .tag-coral { color: var(--ebook-coral); background: var(--ebook-coral-tint); border-color: var(--ebook-coral-border); }
    .tag-teal  { color: var(--secondary); background: var(--secondary-tint); border-color: var(--secondary-border); }

    .ebook-stats {
        display: flex; gap: 20px; flex-wrap: wrap;
    }
    .ebook-stat {
        display: flex; flex-direction: column; gap: 2px;
    }
    .ebook-stat-value {
        font-family: var(--font-mono); font-size: 18px; font-weight: 500;
        color: var(--text-primary); line-height: 1;
    }
    .ebook-stat-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase;
    }
    .ebook-stat-sep {
        width: 0.5px; background: var(--border-default);
        align-self: stretch; margin: 0 4px;
    }

    /* Download CTA */
    .ebook-cta-row {
        display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
    }
    .btn-download {
        font-family: var(--font-mono); font-size: 11px;
        background: var(--ebook-coral); color: #fff;
        border: none; border-radius: var(--radius-btn);
        padding: 10px 20px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: opacity var(--duration-fast);
    }
    .btn-download:hover { opacity: 0.88; }
    .btn-preview {
        font-family: var(--font-mono); font-size: 11px;
        background: none; color: var(--text-secondary);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 10px 18px; cursor: pointer;
        letter-spacing: var(--tracking-mono-sm);
        transition: border-color var(--duration-fast), color var(--duration-fast);
    }
    .btn-preview:hover { border-color: var(--border-strong); color: var(--text-primary); }
    .ebook-format-note {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
    }

    /* Like */
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

    /* ── BODY GRID ────────────────────────────────────────────────── */
    .ebook-body {
        display: grid;
        grid-template-columns: var(--prose-max) var(--rail-width);
        gap: 48px;
        padding-top: 40px;
    }
    @media (max-width: 1100px) {
        .ebook-body { grid-template-columns: 1fr; }
        .ebook-rail { display: none; }
    }

    /* ── ABOUT SECTION ────────────────────────────────────────────── */
    .section-heading {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 14px;
        display: flex; align-items: center; gap: 8px;
    }
    .section-heading::after { content: ''; flex: 1; height: 0.5px; background: var(--border-default); }
    .section-heading .slash { color: var(--ebook-coral); margin-right: 2px; }

    .ebook-about {
        font-family: var(--font-prose); font-size: 14px;
        line-height: var(--leading-prose); color: var(--text-body);
        margin-bottom: 40px;
    }
    .ebook-about p { margin-bottom: 1.2em; }
    .ebook-about strong { font-weight: 400; color: var(--text-primary); }

    /* ── CHAPTERS ─────────────────────────────────────────────────── */
    .chapter-list {
        display: flex; flex-direction: column; gap: 2px;
        margin-bottom: 40px;
    }
    .chapter-item {
        display: grid;
        grid-template-columns: 28px 1fr auto;
        gap: 12px; align-items: baseline;
        padding: 10px 12px;
        border: 0.5px solid transparent;
        border-radius: var(--radius-card);
        transition: background var(--duration-fast), border-color var(--duration-fast);
        cursor: pointer;
    }
    .chapter-item:hover {
        background: var(--bg-surface-1);
        border-color: var(--border-default);
    }
    .chapter-num {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--ebook-coral); letter-spacing: var(--tracking-mono-xs);
    }
    .chapter-title {
        font-family: var(--font-display); font-weight: 700;
        font-size: 13px; color: var(--text-primary);
        line-height: 1.35; letter-spacing: var(--tracking-display);
    }
    .chapter-sub {
        font-family: var(--font-prose); font-size: 11px;
        color: var(--text-muted); margin-top: 2px;
        font-style: italic;
    }
    .chapter-pages {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); white-space: nowrap;
        letter-spacing: var(--tracking-mono-xs);
    }
    .chapter-divider {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase;
        padding: 14px 12px 4px;
    }

    /* ── READING NOTES ────────────────────────────────────────────── */
    .notes-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        margin-bottom: 40px;
    }
    @media (max-width: 600px) { .notes-grid { grid-template-columns: 1fr; } }
    .note-card {
        background: var(--bg-surface-1);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-card);
        padding: 14px 16px;
    }
    .note-card.featured {
        border-color: var(--ebook-coral-border);
        background: var(--ebook-coral-tint);
        grid-column: 1 / -1;
    }
    .note-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 6px;
    }
    .note-card.featured .note-label { color: var(--ebook-coral); }
    .note-text {
        font-family: var(--font-prose); font-style: italic;
        font-size: 13px; color: var(--text-secondary);
        line-height: 1.5;
    }
    .note-card.featured .note-text { color: var(--text-primary); font-size: 14px; }
    .note-source {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); margin-top: 8px;
        letter-spacing: var(--tracking-mono-xs);
    }

    /* ── SHARE ROW ────────────────────────────────────────────────── */
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
    .like-btn-lg:hover { color: var(--ebook-coral); border-color: var(--ebook-coral-border); }
    .like-btn-lg svg { width: 12px; height: 12px; }

    /* ── RIGHT RAIL ───────────────────────────────────────────────── */
    .ebook-rail {
        position: sticky; top: calc(var(--nav-height) + 24px);
        display: flex; flex-direction: column; gap: 20px;
        align-self: start;
    }
    .rail-heading {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .rail-heading::after { content: ''; flex: 1; height: 0.5px; background: var(--border-default); }
    .rail-heading .slash { color: var(--ebook-coral); margin-right: 2px; }
    .rail-stat {
        display: flex; justify-content: space-between; align-items: center;
        padding: 6px 0; border-bottom: 0.5px solid var(--border-default);
    }
    .rail-stat:last-child { border-bottom: none; }
    .rail-stat-label { font-family: var(--font-mono); font-size: 9px; color: var(--text-muted); letter-spacing: var(--tracking-mono-xs); }
    .rail-stat-value { font-family: var(--font-mono); font-size: 9px; color: var(--text-secondary); }

    /* Download box in rail */
    .rail-download-box {
        background: var(--ebook-coral-tint);
        border: 0.5px solid var(--ebook-coral-border);
        border-radius: var(--radius-card);
        padding: 16px;
        display: flex; flex-direction: column; gap: 10px;
    }
    .rail-download-title {
        font-family: var(--font-display); font-weight: 700;
        font-size: 13px; color: var(--text-primary);
        letter-spacing: var(--tracking-display);
    }
    .rail-download-note {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
    }

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

<!-- NAV -->
<nav class="nav">
    <div class="nav-inner">
        <a href="/" class="nav-logo">dav<span>/</span>devs</a>
        <ul class="nav-links">
            <li><a href="#">home</a></li>
            <li><a href="#">articles</a></li>
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

<div class="ebook-wrapper">

    <!-- ── HERO ─────────────────────────────────────────────────────── -->
    <div class="ebook-hero">

        <!-- Cover art -->
        <div class="ebook-cover">
            <div class="cover-spine"></div>
            <div class="cover-body">
                <div>
                    <div class="cover-type-badge">E-Book</div>
                    <div class="cover-title">Designing for Privacy</div>
                    <div class="cover-subtitle">A Field Guide for Developers Who Actually Care</div>
                </div>
                <div>
                    <div class="cover-author">Davina Leong</div>
                    <div class="cover-edition">v1.2 · 2026</div>
                </div>
            </div>
        </div>

        <!-- Meta -->
        <div class="ebook-meta-col">
            <p class="ebook-breadcrumb">dav<span>/</span>devs <span style="color:var(--text-faint)">›</span> e-books</p>

            <div>
                <h1 class="ebook-title">Designing for Privacy</h1>
                <p class="ebook-subtitle">A Field Guide for Developers Who Actually Care</p>
            </div>

            <div class="ebook-tags">
                <span class="tag tag-coral">Privacy</span>
                <span class="tag tag-amber">Security</span>
                <span class="tag tag-teal">Architecture</span>
                <span class="tag tag-coral">E-Book</span>
            </div>

            <div class="ebook-stats">
                <div class="ebook-stat">
                    <span class="ebook-stat-value">148</span>
                    <span class="ebook-stat-label">pages</span>
                </div>
                <div class="ebook-stat-sep"></div>
                <div class="ebook-stat">
                    <span class="ebook-stat-value">9</span>
                    <span class="ebook-stat-label">chapters</span>
                </div>
                <div class="ebook-stat-sep"></div>
                <div class="ebook-stat">
                    <span class="ebook-stat-value">~4h</span>
                    <span class="ebook-stat-label">read time</span>
                </div>
                <div class="ebook-stat-sep"></div>
                <div class="ebook-stat">
                    <span class="ebook-stat-value">v1.2</span>
                    <span class="ebook-stat-label">edition</span>
                </div>
            </div>

            <div class="ebook-cta-row">
                <button class="btn-download">↓ Download PDF — free</button>
                <button class="btn-preview">Preview first chapter →</button>
                <span class="ebook-format-note">PDF · EPUB · 2.4 MB</span>
                <button class="like-btn" aria-label="Like">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                    </svg>
                    57
                </button>
            </div>

            <p style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);letter-spacing:var(--tracking-mono-xs);">
                Published 15 Mar 2026 · Updated 10 Jun 2026
            </p>
        </div>
    </div>

    <!-- ── BODY GRID ──────────────────────────────────────────────── -->
    <div class="ebook-body">

        <div>
            <!-- ABOUT -->
            <div class="ebook-about" style="margin-bottom:32px;">
                <div class="section-heading"><span class="slash">/</span> about this book</div>
                <p>
                    Privacy isn't a compliance checkbox — it's a design constraint that shapes every
                    decision from schema to UI. This book is the guide I wish I had when I started
                    building systems that handle real people's data.
                </p>
                <p>
                    It covers threat modelling for small teams, data minimisation in practice, anonymous
                    interaction patterns, consent flows that aren't dark patterns, and how to audit
                    what you've already shipped. Code examples are in <strong>PHP/Laravel</strong> and
                    <strong>TypeScript</strong>, but the principles translate anywhere.
                </p>
                <p>
                    You don't need to be a security engineer. You do need to care enough to read 148 pages.
                </p>
            </div>

            <!-- CHAPTERS -->
            <div class="section-heading" style="margin-bottom:0;"><span class="slash">/</span> table of contents</div>
            <div class="chapter-list">

                <div class="chapter-divider">Part I — Foundations</div>

                <div class="chapter-item">
                    <span class="chapter-num">01</span>
                    <div>
                        <div class="chapter-title">Why Privacy is a Design Problem</div>
                        <div class="chapter-sub">Moving beyond compliance into craft</div>
                    </div>
                    <span class="chapter-pages">pp. 1–14</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">02</span>
                    <div>
                        <div class="chapter-title">Threat Modelling for Small Teams</div>
                        <div class="chapter-sub">STRIDE without the enterprise overhead</div>
                    </div>
                    <span class="chapter-pages">pp. 15–32</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">03</span>
                    <div>
                        <div class="chapter-title">Data Minimisation in Practice</div>
                        <div class="chapter-sub">Only collect what you'd be comfortable losing</div>
                    </div>
                    <span class="chapter-pages">pp. 33–52</span>
                </div>

                <div class="chapter-divider">Part II — Implementation Patterns</div>

                <div class="chapter-item">
                    <span class="chapter-num">04</span>
                    <div>
                        <div class="chapter-title">Anonymous Interaction Systems</div>
                        <div class="chapter-sub">Likes, views, and votes without user accounts</div>
                    </div>
                    <span class="chapter-pages">pp. 53–74</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">05</span>
                    <div>
                        <div class="chapter-title">Consent Flows That Don't Lie</div>
                        <div class="chapter-sub">Designing opt-in without dark patterns</div>
                    </div>
                    <span class="chapter-pages">pp. 75–96</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">06</span>
                    <div>
                        <div class="chapter-title">Hashing, Tokenisation, and Pseudonymisation</div>
                        <div class="chapter-sub">When to use each, and when they're not enough</div>
                    </div>
                    <span class="chapter-pages">pp. 97–114</span>
                </div>

                <div class="chapter-divider">Part III — Audit & Maintenance</div>

                <div class="chapter-item">
                    <span class="chapter-num">07</span>
                    <div>
                        <div class="chapter-title">Auditing What You've Already Shipped</div>
                        <div class="chapter-sub">Finding the leaks in a running system</div>
                    </div>
                    <span class="chapter-pages">pp. 115–128</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">08</span>
                    <div>
                        <div class="chapter-title">Third-Party Scripts and the Trust Chain</div>
                        <div class="chapter-sub">Analytics, fonts, and what they take with them</div>
                    </div>
                    <span class="chapter-pages">pp. 129–140</span>
                </div>

                <div class="chapter-item">
                    <span class="chapter-num">09</span>
                    <div>
                        <div class="chapter-title">Building a Privacy Culture on a One-Person Team</div>
                        <div class="chapter-sub">Sustainable habits, not heroics</div>
                    </div>
                    <span class="chapter-pages">pp. 141–148</span>
                </div>
            </div>

            <!-- READING NOTES -->
            <div class="section-heading"><span class="slash">/</span> reading notes</div>
            <div class="notes-grid">
                <div class="note-card featured">
                    <div class="note-label">Key takeaway</div>
                    <div class="note-text">"Privacy by design means the cost of adding tracking later should be higher than the cost of leaving it out now. Build the friction in deliberately."</div>
                    <div class="note-source">— Chapter 1, p. 9</div>
                </div>
                <div class="note-card">
                    <div class="note-label">From Chapter 4</div>
                    <div class="note-text">"A hashed fingerprint with a TTL isn't perfect anonymity — it's proportionate anonymity. Know the difference."</div>
                    <div class="note-source">p. 61</div>
                </div>
                <div class="note-card">
                    <div class="note-label">From Chapter 5</div>
                    <div class="note-text">"If your consent UI is designed to be dismissed, it's not consent. It's a legal shield with a UX problem."</div>
                    <div class="note-source">p. 82</div>
                </div>
            </div>

            <!-- CHANGELOG -->
            <div class="section-heading"><span class="slash">/</span> changelog</div>
            <div style="display:flex;flex-direction:column;gap:0;">
                @php
                $changes = [
                    ['v1.2', '10 Jun 2026', 'Added Chapter 9; revised hashing examples in Ch. 6 for PHP 8.3'],
                    ['v1.1', '01 Apr 2026', 'Corrected STRIDE table in Chapter 2; fixed EPUB formatting'],
                    ['v1.0', '15 Mar 2026', 'Initial release'],
                ];
                @endphp
                @foreach($changes as $c)
                <div style="display:grid;grid-template-columns:40px 90px 1fr;gap:12px;padding:10px 0;border-bottom:0.5px solid var(--border-default);align-items:baseline;">
                    <span style="font-family:var(--font-mono);font-size:9px;color:var(--ebook-coral);">{{ $c[0] }}</span>
                    <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);">{{ $c[1] }}</span>
                    <span style="font-family:var(--font-prose);font-size:12px;color:var(--text-muted);">{{ $c[2] }}</span>
                </div>
                @endforeach
            </div>

            <!-- SHARE ROW -->
            <div class="post-footer-row">
                <span class="share-label">share</span>
                <button class="share-btn">LinkedIn</button>
                <button class="share-btn">Threads</button>
                <button class="share-btn">copy link</button>
                <button class="like-btn-lg" aria-label="Like">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 13.5S1.5 9.5 1.5 5.5a3 3 0 0 1 6-0.8 3 3 0 0 1 6 .8c0 4-6.5 8-6.5 8z"/>
                    </svg>
                    57
                </button>
            </div>
        </div>

        <!-- ── RIGHT RAIL ─────────────────────────────────────────── -->
        <aside class="ebook-rail">

            <!-- Download box -->
            <div class="rail-download-box">
                <div class="rail-download-title">Get the book</div>
                <div class="rail-download-note">Free forever. No email required.</div>
                <button class="btn-download" style="width:100%;text-align:center;">↓ Download PDF</button>
                <button class="btn-preview" style="width:100%;text-align:center;">Preview Ch. 1</button>
                <div style="font-family:var(--font-mono);font-size:8px;color:var(--text-faint);letter-spacing:var(--tracking-mono-xs);">PDF · EPUB · 2.4 MB</div>
            </div>

            <!-- Book info -->
            <div>
                <div class="rail-heading"><span class="slash">/</span> book info</div>
                <div class="rail-stat">
                    <span class="rail-stat-label">published</span>
                    <span class="rail-stat-value">15 Mar 2026</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">updated</span>
                    <span class="rail-stat-value">10 Jun 2026</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">edition</span>
                    <span class="rail-stat-value">v1.2</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">pages</span>
                    <span class="rail-stat-value">148</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">chapters</span>
                    <span class="rail-stat-value">9 across 3 parts</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">formats</span>
                    <span class="rail-stat-value">PDF, EPUB</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">licence</span>
                    <span class="rail-stat-value">CC BY-NC 4.0</span>
                </div>
                <div class="rail-stat">
                    <span class="rail-stat-label">likes</span>
                    <span class="rail-stat-value">57</span>
                </div>
            </div>

            <!-- TOC mini -->
            <div>
                <div class="rail-heading"><span class="slash">/</span> chapters</div>
                @php
                $toc = [
                    '01 — Why Privacy is a Design Problem',
                    '02 — Threat Modelling for Small Teams',
                    '03 — Data Minimisation in Practice',
                    '04 — Anonymous Interaction Systems',
                    '05 — Consent Flows That Don\'t Lie',
                    '06 — Hashing, Tokenisation…',
                    '07 — Auditing What You\'ve Shipped',
                    '08 — Third-Party Scripts',
                    '09 — Building a Privacy Culture',
                ];
                @endphp
                <ul style="list-style:none;display:flex;flex-direction:column;gap:1px;">
                    @foreach($toc as $i => $t)
                    <li>
                        <a href="#" style="
                            font-family:var(--font-mono);font-size:9px;
                            color:{{ $i === 0 ? 'var(--ebook-coral)' : 'var(--text-muted)' }};
                            letter-spacing:var(--tracking-mono-sm);
                            display:block;padding:5px 8px;
                            border-radius:3px;
                            border-left:1.5px solid {{ $i === 0 ? 'var(--ebook-coral)' : 'transparent' }};
                            background:{{ $i === 0 ? 'var(--ebook-coral-tint)' : 'none' }};
                            text-decoration:none;
                        ">{{ $t }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-logo">dav<span>/</span>devs</div>
        <ul class="footer-links">
            <li><a href="#">articles</a></li>
            <li><a href="#">projects</a></li>
            <li><a href="#">tools</a></li>
            <li><a href="#">about</a></li>
            <li><a href="#">rss</a></li>
        </ul>
        <div class="lh-badges">
            <div class="lh-badge"><span class="lh-num">99</span><span class="lh-label">Perf</span></div>
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">A11y</span></div>
            <div class="lh-badge"><span class="lh-num">100</span><span class="lh-label">SEO</span></div>
        </div>
    </div>
</footer>

<!-- MOBILE TAB BAR -->
<nav class="tab-bar">
    <div class="tab-bar-inner">
        <div class="tab-item"><span class="tab-icon">⌂</span><span class="tab-label">home</span></div>
        <div class="tab-item"><span class="tab-icon">☰</span><span class="tab-label">posts</span></div>
        <div class="tab-item active"><span class="tab-icon">📖</span><span class="tab-label">books</span></div>
        <div class="tab-item"><span class="tab-icon">◎</span><span class="tab-label">about</span></div>
    </div>
</nav>

@endsection
