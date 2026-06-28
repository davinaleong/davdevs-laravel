@extends('static.layouts.site')

@section('title', 'AI Chat — Dav/Devs')

@push('head')
<style>
    /* ── NAV (reused from site) ─────────────────────────────────────── */
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
    .nav-links a:hover { color: var(--nav-link-active); }
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
    @media (max-width: 767px) {
        .nav-links { display: none; }
        .nav-search-btn { display: none; }
    }

    /* ── CHATBOT TOKENS ─────────────────────────────────────────────── */
    :root {
        --chat-bg:              var(--bg-base);
        --chat-surface:         var(--bg-surface-1);
        --chat-surface-2:       var(--bg-surface-2);
        --chat-border:          var(--border-default);
        --chat-border-strong:   var(--border-strong);

        --chat-bubble-user-bg:  var(--accent);
        --chat-bubble-user-text: #0D0C0A;
        --chat-bubble-ai-bg:    var(--bg-surface-1);
        --chat-bubble-ai-text:  var(--text-body);
        --chat-bubble-ai-border: var(--border-default);

        --chat-input-bg:        var(--bg-surface-1);
        --chat-input-border:    var(--border-default);
        --chat-input-focus:     var(--accent-border);

        --chat-fab-bg:          var(--accent);
        --chat-fab-shadow:      rgba(212, 167, 87, 0.35);

        --chat-widget-w:        380px;
        --chat-widget-h:        520px;
        --chat-widget-radius:   12px;
    }

    /* ── PAGE BODY ──────────────────────────────────────────────────── */
    .page-body {
        margin-top: var(--nav-height);
        max-width: var(--page-max);
        margin-left: auto; margin-right: auto;
        padding: 48px var(--page-padding-x) 100px;
    }
    .page-hero {
        max-width: 560px;
        margin-bottom: 48px;
    }
    .page-eyebrow {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--accent); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 12px;
    }
    .page-eyebrow .slash { margin-right: 2px; }
    .page-title {
        font-family: var(--font-display); font-weight: 800;
        font-size: clamp(22px, 3vw, 30px);
        color: var(--text-primary);
        line-height: var(--leading-display);
        letter-spacing: var(--tracking-display);
        margin-bottom: 14px;
    }
    .page-sub {
        font-family: var(--font-prose); font-size: 14px;
        color: var(--text-muted); line-height: 1.65;
    }

    /* ── DEMO AREA ──────────────────────────────────────────────────── */
    .demo-area {
        display: grid;
        grid-template-columns: 1fr var(--chat-widget-w);
        gap: 40px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .demo-area { grid-template-columns: 1fr; }
        .demo-left { display: none; }
    }

    /* Annotations column */
    .annotation-list {
        display: flex; flex-direction: column; gap: 20px;
        padding-top: 8px;
    }
    .annotation {
        display: flex; gap: 14px; align-items: start;
    }
    .annotation-num {
        font-family: var(--font-mono); font-size: 10px;
        color: var(--accent); border: 0.5px solid var(--accent-border);
        background: var(--accent-tint); border-radius: 50%;
        width: 20px; height: 20px; min-width: 20px;
        display: flex; align-items: center; justify-content: center;
        letter-spacing: 0;
    }
    .annotation-body {}
    .annotation-title {
        font-family: var(--font-display); font-weight: 700;
        font-size: 13px; color: var(--text-primary);
        letter-spacing: var(--tracking-display);
        margin-bottom: 3px;
    }
    .annotation-text {
        font-family: var(--font-prose); font-size: 12px;
        color: var(--text-muted); line-height: 1.55;
    }
    .annotation-divider {
        height: 0.5px; background: var(--border-default);
        margin: 4px 0;
    }

    /* ── WIDGET CONTAINER ───────────────────────────────────────────── */
    .widget-demo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .widget-label {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-align: center;
    }

    /* ── CHAT WIDGET (OPEN STATE) ────────────────────────────────────── */
    .chat-widget {
        width: var(--chat-widget-w);
        height: var(--chat-widget-h);
        background: var(--chat-bg);
        border: 0.5px solid var(--chat-border);
        border-radius: var(--chat-widget-radius);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }

    /* Header */
    .chat-header {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px;
        background: var(--chat-surface);
        border-bottom: 0.5px solid var(--chat-border);
        flex-shrink: 0;
    }
    .chat-avatar {
        width: 28px; height: 28px;
        background: var(--accent);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-mono); font-size: 11px;
        color: #0D0C0A; font-weight: 700;
        flex-shrink: 0;
    }
    .chat-header-text {}
    .chat-header-name {
        font-family: var(--font-mono); font-size: 11px;
        color: var(--text-primary); letter-spacing: var(--tracking-mono-sm);
    }
    .chat-header-status {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        display: flex; align-items: center; gap: 4px;
    }
    .status-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: #4CAF50; display: inline-block;
    }
    .chat-header-actions {
        margin-left: auto;
        display: flex; gap: 6px;
    }
    .chat-hdr-btn {
        background: none; border: none; cursor: pointer;
        color: var(--text-faint); font-size: 14px;
        display: flex; align-items: center;
        padding: 4px;
        border-radius: 4px;
        transition: color var(--duration-fast), background var(--duration-fast);
    }
    .chat-hdr-btn:hover { color: var(--text-secondary); background: var(--bg-surface-2); }

    /* Suggested prompts (shown when no messages yet) */
    .chat-suggestions {
        padding: 12px 14px 0;
        display: flex; flex-direction: column; gap: 6px;
        flex-shrink: 0;
    }
    .suggestions-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 4px;
    }
    .suggestion-chips {
        display: flex; flex-wrap: wrap; gap: 5px;
    }
    .suggestion-chip {
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-secondary); letter-spacing: var(--tracking-mono-xs);
        background: var(--chat-surface);
        border: 0.5px solid var(--chat-border);
        border-radius: 20px; padding: 4px 10px;
        cursor: pointer; transition: border-color var(--duration-fast), color var(--duration-fast);
        white-space: nowrap;
    }
    .suggestion-chip:hover { border-color: var(--accent-border); color: var(--accent); }

    /* Messages area */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scroll-behavior: smooth;
    }
    .chat-messages::-webkit-scrollbar { width: 3px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: var(--border-default); border-radius: 3px; }

    /* Message row */
    .msg-row {
        display: flex; gap: 8px;
        align-items: flex-end;
    }
    .msg-row.user { flex-direction: row-reverse; }

    .msg-avatar {
        width: 22px; height: 22px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-mono); font-size: 8px;
        flex-shrink: 0;
        align-self: flex-start;
        margin-top: 2px;
    }
    .msg-avatar.ai  { background: var(--accent); color: #0D0C0A; }
    .msg-avatar.usr { background: var(--bg-surface-2); color: var(--text-muted); border: 0.5px solid var(--border-default); }

    /* Bubble */
    .msg-bubble {
        max-width: 78%;
        padding: 9px 12px;
        border-radius: 10px;
        font-family: var(--font-prose);
        font-size: 12px;
        line-height: 1.6;
    }
    .msg-row.ai .msg-bubble {
        background: var(--chat-bubble-ai-bg);
        color: var(--chat-bubble-ai-text);
        border: 0.5px solid var(--chat-bubble-ai-border);
        border-bottom-left-radius: 3px;
    }
    .msg-row.user .msg-bubble {
        background: var(--chat-bubble-user-bg);
        color: var(--chat-bubble-user-text);
        border-bottom-right-radius: 3px;
    }
    .msg-bubble strong { font-weight: 600; }
    .msg-bubble a {
        color: var(--accent);
        border-bottom: 0.5px solid var(--accent-border);
        text-decoration: none;
    }
    .msg-row.user .msg-bubble a { color: #0D0C0A; border-bottom-color: rgba(13,12,10,0.4); }

    /* Inline result cards inside AI bubble */
    .result-cards {
        display: flex; flex-direction: column; gap: 5px;
        margin-top: 8px;
    }
    .result-card {
        background: var(--bg-surface-2);
        border: 0.5px solid var(--border-default);
        border-radius: 6px;
        padding: 8px 10px;
        cursor: pointer;
        transition: border-color var(--duration-fast);
    }
    .result-card:hover { border-color: var(--accent-border); }
    .result-card-type {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-transform: uppercase; margin-bottom: 3px;
    }
    .result-card-title {
        font-family: var(--font-display); font-weight: 700;
        font-size: 11px; color: var(--text-primary);
        letter-spacing: var(--tracking-display);
        line-height: 1.3;
    }
    .result-card-meta {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); margin-top: 3px;
        letter-spacing: var(--tracking-mono-xs);
    }
    .result-card-arrow {
        float: right; color: var(--accent); font-size: 10px; margin-top: -16px;
    }

    /* Typing indicator */
    .typing-indicator {
        display: flex; gap: 4px; align-items: center;
        padding: 10px 12px;
        background: var(--chat-bubble-ai-bg);
        border: 0.5px solid var(--chat-bubble-ai-border);
        border-radius: 10px; border-bottom-left-radius: 3px;
        width: fit-content;
    }
    .typing-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--text-faint);
    }
    .typing-dot:nth-child(1) { opacity: 0.4; }
    .typing-dot:nth-child(2) { opacity: 0.7; }
    .typing-dot:nth-child(3) { opacity: 1; }

    /* Timestamp */
    .msg-ts {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-align: center; margin: 4px 0 0;
        align-self: center;
    }

    /* Input area */
    .chat-input-area {
        padding: 12px 14px;
        background: var(--chat-surface);
        border-top: 0.5px solid var(--chat-border);
        display: flex; gap: 8px; align-items: flex-end;
        flex-shrink: 0;
    }
    .chat-input-wrap {
        flex: 1;
        background: var(--chat-input-bg);
        border: 0.5px solid var(--chat-input-border);
        border-radius: 8px;
        display: flex; align-items: flex-end;
        padding: 8px 10px;
        transition: border-color var(--duration-fast);
    }
    .chat-input-wrap:focus-within { border-color: var(--chat-input-focus); }
    .chat-input {
        flex: 1; background: none; border: none; outline: none;
        font-family: var(--font-prose); font-size: 12px;
        color: var(--text-primary); resize: none;
        line-height: 1.5; max-height: 96px; min-height: 18px;
    }
    .chat-input::placeholder { color: var(--text-faint); }
    .chat-send-btn {
        width: 32px; height: 32px;
        background: var(--accent);
        border: none; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; flex-shrink: 0;
        transition: opacity var(--duration-fast);
        color: #0D0C0A; font-size: 14px;
    }
    .chat-send-btn:hover { opacity: 0.85; }
    .chat-input-footer {
        display: flex; align-items: center; justify-content: center;
        padding: 4px 14px 8px;
        background: var(--chat-surface);
    }
    .chat-input-footer-text {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-align: center;
    }

    /* ── FAB (CLOSED STATE) ─────────────────────────────────────────── */
    .fab-demo {
        display: flex; flex-direction: column; align-items: center; gap: 12px;
        margin-top: 8px;
    }
    .fab-states {
        display: flex; gap: 24px; align-items: flex-end;
    }
    .fab-state-label {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        text-align: center; margin-bottom: 6px;
    }
    .chat-fab {
        width: 52px; height: 52px;
        background: var(--chat-fab-bg);
        border-radius: 50%; border: none;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 22px;
        box-shadow: 0 4px 16px var(--chat-fab-shadow);
        transition: transform var(--duration-fast), box-shadow var(--duration-fast);
        position: relative;
    }
    .chat-fab:hover {
        transform: scale(1.06);
        box-shadow: 0 6px 24px var(--chat-fab-shadow);
    }
    .fab-badge {
        position: absolute; top: -2px; right: -2px;
        width: 16px; height: 16px; border-radius: 50%;
        background: var(--tertiary); border: 2px solid var(--bg-base);
        font-family: var(--font-mono); font-size: 8px; color: #fff;
        display: flex; align-items: center; justify-content: center;
    }
    .fab-tooltip {
        background: var(--bg-surface-1);
        border: 0.5px solid var(--border-default);
        border-radius: 6px; padding: 6px 10px;
        font-family: var(--font-mono); font-size: 9px;
        color: var(--text-secondary); letter-spacing: var(--tracking-mono-xs);
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }

    /* ── FOOTER ─────────────────────────────────────────────────────── */
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

    /* ── MOBILE TAB BAR ─────────────────────────────────────────────── */
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

<div class="page-body">

    <!-- PAGE HERO -->
    <div class="page-hero">
        <p class="page-eyebrow"><span class="slash">/</span> ai assistant</p>
        <h1 class="page-title">Ask anything about this site</h1>
        <p class="page-sub">
            The chat widget lives bottom-right on every page. Ask about specific posts,
            topics, tools, or e-books — it searches the content and links you directly.
            It knows what's been published here and nothing more.
        </p>
    </div>

    <!-- DEMO AREA -->
    <div class="demo-area">

        <!-- ANNOTATIONS -->
        <div class="demo-left">
            <div class="annotation-list">
                <div class="annotation">
                    <div class="annotation-num">1</div>
                    <div class="annotation-body">
                        <div class="annotation-title">Floating widget, always accessible</div>
                        <div class="annotation-text">Sits above the mobile tab bar, below desktop nav. Expands in-place — no full-page navigation, no modal backdrop.</div>
                    </div>
                </div>
                <div class="annotation-divider"></div>
                <div class="annotation">
                    <div class="annotation-num">2</div>
                    <div class="annotation-body">
                        <div class="annotation-title">Suggested prompts on open</div>
                        <div class="annotation-text">Four curated chips surface common entry points — posts by topic, e-books, tools, and the about blurb.</div>
                    </div>
                </div>
                <div class="annotation-divider"></div>
                <div class="annotation">
                    <div class="annotation-num">3</div>
                    <div class="annotation-body">
                        <div class="annotation-title">Inline result cards</div>
                        <div class="annotation-text">Matching posts surface as tappable cards inside the AI reply — type badge, title, and date. One tap opens the post.</div>
                    </div>
                </div>
                <div class="annotation-divider"></div>
                <div class="annotation">
                    <div class="annotation-num">4</div>
                    <div class="annotation-body">
                        <div class="annotation-title">Site-scoped knowledge only</div>
                        <div class="annotation-text">The system prompt constrains the model to content published on this site. It won't answer general coding questions or hallucinate posts that don't exist.</div>
                    </div>
                </div>
                <div class="annotation-divider"></div>
                <div class="annotation">
                    <div class="annotation-num">5</div>
                    <div class="annotation-body">
                        <div class="annotation-title">Unread badge on FAB</div>
                        <div class="annotation-text">A coral dot appears when a new automated welcome message has been sent but the widget hasn't been opened yet (first-visit only).</div>
                    </div>
                </div>

                <div style="margin-top:20px;">
                    <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);letter-spacing:var(--tracking-mono-xs);text-transform:uppercase;margin-bottom:10px;">FAB states</div>
                    <div class="fab-states">
                        <div>
                            <div class="fab-state-label">default</div>
                            <div class="chat-fab" style="position:relative;width:52px;height:52px;">✦</div>
                        </div>
                        <div>
                            <div class="fab-state-label">unread</div>
                            <div class="chat-fab" style="position:relative;width:52px;height:52px;">
                                ✦
                                <div class="fab-badge">1</div>
                            </div>
                        </div>
                        <div>
                            <div class="fab-state-label">hover tooltip</div>
                            <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                <div class="chat-fab" style="position:relative;width:52px;height:52px;">✦</div>
                                <div class="fab-tooltip">Ask about this site ↑</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- WIDGET OPEN STATE -->
        <div class="widget-demo">
            <div class="widget-label">↓ open state mockup (380 × 520px)</div>

            <div class="chat-widget">

                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-avatar">✦</div>
                    <div class="chat-header-text">
                        <div class="chat-header-name">dav/devs assistant</div>
                        <div class="chat-header-status">
                            <span class="status-dot"></span>
                            online · site-scoped
                        </div>
                    </div>
                    <div class="chat-header-actions">
                        <button class="chat-hdr-btn" title="Clear chat">↺</button>
                        <button class="chat-hdr-btn" title="Close">✕</button>
                    </div>
                </div>

                <!-- Suggested prompts -->
                <div class="chat-suggestions">
                    <div class="suggestions-label">try asking</div>
                    <div class="suggestion-chips">
                        <span class="suggestion-chip">What articles exist on Laravel?</span>
                        <span class="suggestion-chip">Do you have any e-books?</span>
                        <span class="suggestion-chip">Show me your tools</span>
                        <span class="suggestion-chip">Who made this site?</span>
                    </div>
                </div>

                <!-- Messages -->
                <div class="chat-messages">

                    <!-- AI welcome -->
                    <div class="msg-row ai">
                        <div class="msg-avatar ai">✦</div>
                        <div>
                            <div class="msg-bubble">
                                Hi! I'm the Dav/Devs assistant. I know everything published on this site — articles, projects, tools, notebooks, and e-books. What are you looking for?
                            </div>
                        </div>
                    </div>

                    <div class="msg-ts">09:14</div>

                    <!-- User message -->
                    <div class="msg-row user">
                        <div class="msg-avatar usr">D</div>
                        <div class="msg-bubble">
                            Do you have anything on privacy or TOTP 2FA?
                        </div>
                    </div>

                    <!-- AI reply with result cards -->
                    <div class="msg-row ai">
                        <div class="msg-avatar ai">✦</div>
                        <div>
                            <div class="msg-bubble">
                                Yes — there are a few posts on both topics:
                                <div class="result-cards">
                                    <div class="result-card">
                                        <div class="result-card-type">Article</div>
                                        <div class="result-card-title">Building a Privacy-First CMS with Laravel and Anthropic Claude</div>
                                        <div class="result-card-meta">28 Jun 2026 · 8 min</div>
                                        <div class="result-card-arrow">↗</div>
                                    </div>
                                    <div class="result-card">
                                        <div class="result-card-type">Article</div>
                                        <div class="result-card-title">TOTP 2FA in Laravel Without Breeze Magic</div>
                                        <div class="result-card-meta">01 Jun 2026 · 6 min</div>
                                        <div class="result-card-arrow">↗</div>
                                    </div>
                                    <div class="result-card">
                                        <div class="result-card-type">E-Book · SGD 9.90</div>
                                        <div class="result-card-title">Designing for Privacy</div>
                                        <div class="result-card-meta">148 pages · 9 chapters</div>
                                        <div class="result-card-arrow">↗</div>
                                    </div>
                                </div>
                                There's also a full e-book on privacy design if you want something more in-depth.
                            </div>
                        </div>
                    </div>

                    <div class="msg-ts">09:14</div>

                    <!-- User -->
                    <div class="msg-row user">
                        <div class="msg-avatar usr">D</div>
                        <div class="msg-bubble">
                            What's the e-book about?
                        </div>
                    </div>

                    <!-- AI typing -->
                    <div class="msg-row ai">
                        <div class="msg-avatar ai">✦</div>
                        <div class="typing-indicator">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>

                </div><!-- /chat-messages -->

                <!-- Input -->
                <div class="chat-input-area">
                    <div class="chat-input-wrap">
                        <textarea class="chat-input" rows="1" placeholder="Ask about posts, tools, or e-books…"></textarea>
                    </div>
                    <button class="chat-send-btn" aria-label="Send">↑</button>
                </div>
                <div class="chat-input-footer">
                    <span class="chat-input-footer-text">Site-scoped · powered by Claude · <a href="#" style="color:var(--text-faint);">privacy</a></span>
                </div>

            </div><!-- /chat-widget -->

            <!-- FAB below widget (shows placement) -->
            <div class="widget-label" style="margin-top:8px;">↓ closed state FAB (bottom-right corner)</div>
            <div style="display:flex;justify-content:flex-end;width:100%;padding-right:4px;">
                <div class="chat-fab">✦</div>
            </div>

        </div><!-- /widget-demo -->
    </div><!-- /demo-area -->

    <!-- EMPTY / FIRST OPEN STATE -->
    <div style="margin-top:60px;">
        <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);letter-spacing:var(--tracking-mono-xs);text-transform:uppercase;margin-bottom:20px;">
            <span style="color:var(--accent);">/</span> empty state — first open
        </div>

        <div class="chat-widget" style="max-width:var(--chat-widget-w);height:400px;">
            <div class="chat-header">
                <div class="chat-avatar">✦</div>
                <div class="chat-header-text">
                    <div class="chat-header-name">dav/devs assistant</div>
                    <div class="chat-header-status"><span class="status-dot"></span> online · site-scoped</div>
                </div>
                <div class="chat-header-actions">
                    <button class="chat-hdr-btn">↺</button>
                    <button class="chat-hdr-btn">✕</button>
                </div>
            </div>

            <!-- Empty state body -->
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;gap:20px;text-align:center;">
                <div style="width:48px;height:48px;background:var(--accent-tint);border:0.5px solid var(--accent-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--accent);">✦</div>
                <div>
                    <div style="font-family:var(--font-display);font-weight:700;font-size:14px;color:var(--text-primary);letter-spacing:var(--tracking-display);margin-bottom:6px;">What can I help you find?</div>
                    <div style="font-family:var(--font-prose);font-size:12px;color:var(--text-muted);line-height:1.55;">I know all the articles, projects, tools, notebooks, sermons, and e-books published on this site.</div>
                </div>
                <div class="suggestion-chips" style="justify-content:center;">
                    <span class="suggestion-chip">Latest articles</span>
                    <span class="suggestion-chip">Laravel posts</span>
                    <span class="suggestion-chip">E-books</span>
                    <span class="suggestion-chip">About Davina</span>
                    <span class="suggestion-chip">AI / LLM content</span>
                    <span class="suggestion-chip">Privacy posts</span>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="chat-input-wrap">
                    <textarea class="chat-input" rows="1" placeholder="Ask about posts, tools, or e-books…"></textarea>
                </div>
                <button class="chat-send-btn">↑</button>
            </div>
            <div class="chat-input-footer">
                <span class="chat-input-footer-text">Site-scoped · powered by Claude · <a href="#" style="color:var(--text-faint);">privacy</a></span>
            </div>
        </div>
    </div>

    <!-- ERROR / RATE-LIMIT STATE -->
    <div style="margin-top:48px;">
        <div style="font-family:var(--font-mono);font-size:9px;color:var(--text-faint);letter-spacing:var(--tracking-mono-xs);text-transform:uppercase;margin-bottom:20px;">
            <span style="color:var(--accent);">/</span> error state
        </div>

        <div class="chat-widget" style="max-width:var(--chat-widget-w);height:auto;">
            <div class="chat-header">
                <div class="chat-avatar">✦</div>
                <div class="chat-header-text">
                    <div class="chat-header-name">dav/devs assistant</div>
                    <div class="chat-header-status" style="color:var(--tertiary);">● unavailable</div>
                </div>
                <div class="chat-header-actions">
                    <button class="chat-hdr-btn">✕</button>
                </div>
            </div>
            <div class="chat-messages" style="min-height:120px;max-height:160px;">
                <div class="msg-row ai">
                    <div class="msg-avatar ai" style="background:var(--bg-surface-2);color:var(--text-faint);border:0.5px solid var(--border-default);">✕</div>
                    <div class="msg-bubble" style="background:rgba(196,85,58,0.08);border-color:rgba(196,85,58,0.25);color:var(--tertiary);">
                        Something went wrong — I couldn't reach the AI service. Try again in a moment, or <a href="/articles" style="color:var(--tertiary);border-bottom-color:rgba(196,85,58,0.4);">browse articles directly</a>.
                    </div>
                </div>
            </div>
            <div class="chat-input-area" style="opacity:0.5;pointer-events:none;">
                <div class="chat-input-wrap">
                    <textarea class="chat-input" rows="1" placeholder="Ask about posts, tools, or e-books…" disabled></textarea>
                </div>
                <button class="chat-send-btn" disabled>↑</button>
            </div>
            <div class="chat-input-footer">
                <span class="chat-input-footer-text">Site-scoped · powered by Claude · <a href="#" style="color:var(--text-faint);">privacy</a></span>
            </div>
        </div>
    </div>

</div><!-- /page-body -->

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
        <div class="tab-item active"><span class="tab-icon">✦</span><span class="tab-label">chat</span></div>
        <div class="tab-item"><span class="tab-icon">◎</span><span class="tab-label">about</span></div>
    </div>
</nav>

@endsection
