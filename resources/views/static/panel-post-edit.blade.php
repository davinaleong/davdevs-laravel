@extends('static.layouts.panel')

@section('title', 'Edit Entry — CMS')
@section('nav-posts', 'active')

@push('head')
<style>
    .editor-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 16px;
        align-items: start;
    }
    @media (max-width: 900px) { .editor-layout { grid-template-columns: 1fr; } }
    .editor-main { display: flex; flex-direction: column; gap: 14px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: var(--cms-form-gap); }
    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
    .form-group { display: flex; flex-direction: column; gap: var(--cms-field-label-gap); }
    .md-editor { border: 1px solid var(--cms-editor-border); border-radius: var(--cms-radius-card); overflow: hidden; }
    .md-toolbar { background: var(--cms-editor-toolbar); border-bottom: 1px solid var(--cms-editor-border); padding: 6px 10px; display: flex; gap: 2px; align-items: center; flex-wrap: wrap; }
    .md-tool-btn { font-family: var(--cms-font-mono); font-size: 11px; color: var(--cms-text-muted); background: none; border: none; cursor: pointer; padding: 4px 7px; border-radius: 3px; transition: background var(--dur-fast), color var(--dur-fast); }
    .md-tool-btn:hover { background: var(--cms-bg-hover); color: var(--cms-text-primary); }
    .md-tool-sep { width: 0.5px; height: 16px; background: var(--cms-border); margin: 0 4px; }
    .md-tool-view { margin-left: auto; display: flex; gap: 2px; }
    .md-panes { display: grid; grid-template-columns: 1fr 1fr; }
    .md-pane-editor { background: var(--cms-editor-bg); border-right: 0.5px solid var(--cms-editor-border); padding: 14px; }
    .md-pane-preview { background: var(--cms-editor-preview); padding: 14px; }
    .md-textarea { width: 100%; min-height: 380px; resize: vertical; background: none; border: none; outline: none; font-family: var(--cms-font-mono); font-size: 13px; color: var(--cms-editor-text); line-height: 1.7; }
    .md-preview-content { font-family: 'Georgia', serif; font-size: 14px; color: var(--cms-text-secondary); line-height: 1.8; min-height: 380px; }
    .md-preview-content h2 { font-size: 17px; color: var(--cms-text-primary); margin: 1.2em 0 0.4em; font-family: var(--cms-font-ui); }
    .md-preview-content p { margin-bottom: 1em; }
    .md-preview-content code { font-family: var(--cms-font-mono); font-size: 12px; background: var(--cms-bg-surface-2); padding: 1px 4px; border-radius: 2px; }
    .editor-sidebar { display: flex; flex-direction: column; gap: 12px; }
    .sidebar-panel { background: var(--cms-bg-surface); border: 1px solid var(--cms-border); border-radius: var(--cms-radius-card); overflow: hidden; }
    .sidebar-panel-header { padding: 10px 14px; border-bottom: 1px solid var(--cms-border); font-size: 12px; font-weight: 600; color: var(--cms-text-secondary); display: flex; align-items: center; justify-content: space-between; }
    .sidebar-panel-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; }
    .meta-row { display: flex; flex-direction: column; gap: var(--cms-field-label-gap); }
    .tag-input-wrap { display: flex; flex-wrap: wrap; gap: 4px; background: var(--cms-input-bg); border: 1px solid var(--cms-input-border); border-radius: var(--cms-radius-input); padding: 6px 8px; min-height: 38px; }
    .tag-chip { font-family: var(--cms-font-mono); font-size: 11px; padding: 2px 6px; border-radius: var(--cms-radius-tag); display: flex; align-items: center; gap: 4px; }
    .tag-chip-amber { color: #8A5A0A; background: #FFF6E8; }
    .tag-chip-teal { color: #1A6B4A; background: #EAF5F0; }
    .tag-chip-coral { color: #A03020; background: #FAEEE9; }
    .tag-chip-remove { background: none; border: none; cursor: pointer; color: inherit; opacity: 0.5; font-size: 10px; line-height: 1; }
    .tag-input-field { border: none; outline: none; background: none; font-size: 12px; color: var(--cms-input-text); min-width: 80px; flex: 1; }
    .ai-btn { width: 100%; padding: 8px; background: var(--cms-accent-tint); border: 1px solid var(--cms-accent-border); border-radius: var(--cms-radius-btn); color: var(--cms-accent); font-size: 12px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .ai-btn:hover { background: rgba(212,167,87,0.18); }
    .danger-zone { border-color: var(--cms-error); }
    .danger-zone .sidebar-panel-header { color: var(--cms-error); border-bottom-color: rgba(196,85,58,0.20); }

    /* AI Audit result panel */
    .audit-result {
        background: var(--cms-success-bg);
        border: 1px solid var(--cms-success);
        border-radius: var(--cms-radius-btn);
        padding: 10px 12px;
        display: flex; flex-direction: column; gap: 6px;
    }
    .audit-score-row { display: flex; justify-content: space-between; align-items: center; }
    .audit-score-label { font-size: 11px; color: var(--cms-text-secondary); }
    .audit-score-val { font-family: var(--cms-font-mono); font-size: 11px; color: var(--cms-success); }
    .audit-sep { height: 0.5px; background: var(--cms-border); }
    .audit-suggestion { font-size: 11px; color: var(--cms-text-muted); }
    .audit-suggestion::before { content: '· '; color: var(--cms-warning); }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">Edit Post</h1>
        <p class="cms-page-sub">
            <span class="type-badge type-article">Article</span>
            &ensp;<span style="font-family:var(--cms-font-mono);font-size:11px;color:var(--cms-text-muted);">/building-a-privacy-first-cms</span>
            &ensp;<span class="status-chip status-published">published</span>
        </p>
    </div>
    <div class="cms-actions">
        <a href="/static/panel-posts"><button class="btn btn-ghost">← Back</button></a>
        <button class="btn btn-secondary">↗ View Post</button>
        <button class="btn btn-primary">Save Changes</button>
    </div>
</div>

<div class="editor-layout">
    <div class="editor-main">

        <div class="cms-card" style="padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" class="cms-input" style="font-size:16px;font-weight:500;" value="Building a Privacy-First CMS with Laravel and Anthropic Claude">
            </div>
            <div class="form-group">
                <label class="form-label">Slug</label>
                <input type="text" class="cms-input" value="building-a-privacy-first-cms" style="font-family:var(--cms-font-mono);font-size:12px;">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Content Type</label>
                    <select class="cms-input cms-select"><option selected>Article</option><option>Project</option><option>Tool</option></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="cms-input cms-select"><option>AI</option><option>Security</option><option>Laravel</option></select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Excerpt</label>
                <textarea class="cms-input" rows="2" style="resize:vertical;">How I migrated my Next.js portfolio to a full Laravel web application — model design, auth, 2FA, and wiring up Claude for AI-assisted content audits.</textarea>
            </div>
        </div>

        <div class="md-editor">
            <div class="md-toolbar">
                <button class="md-tool-btn">B</button>
                <button class="md-tool-btn"><em>I</em></button>
                <div class="md-tool-sep"></div>
                <button class="md-tool-btn">H2</button>
                <button class="md-tool-btn">H3</button>
                <div class="md-tool-sep"></div>
                <button class="md-tool-btn">⌶</button>
                <button class="md-tool-btn">❝</button>
                <button class="md-tool-btn">&lt;/&gt;</button>
                <div class="md-tool-sep"></div>
                <button class="md-tool-btn">⊟</button>
                <button class="md-tool-btn">🔗</button>
                <div class="md-tool-view">
                    <button class="md-tool-btn">Edit</button>
                    <button class="md-tool-btn" style="background:var(--cms-bg-hover);">Split</button>
                    <button class="md-tool-btn">Preview</button>
                </div>
            </div>
            <div class="md-panes">
                <div class="md-pane-editor">
                    <textarea class="md-textarea">## Why Laravel

Moving a Next.js portfolio to Laravel isn't just a framework swap — it's a rethink of every architectural decision you made when speed-to-ship was the only goal.

Laravel gives me Eloquent, Horizon, and first-class Blade templating. Tailwind v4 + Alpine keeps the frontend light.

## The Privacy-First Constraint

Privacy-first for me means: no third-party tracking scripts, no comment system, and an anonymous like system that can't be reverse-engineered.

> The like system stores a hashed fingerprint — browser + IP + post ID — with a 24-hour TTL.

## Where Claude Comes In

I wired up Claude (`claude-sonnet-4-6`) for two CMS features: draft generation from a title + outline, and post auditing.</textarea>
                </div>
                <div class="md-pane-preview">
                    <div class="md-preview-content">
                        <h2>Why Laravel</h2>
                        <p>Moving a Next.js portfolio to Laravel isn't just a framework swap — it's a rethink of every architectural decision you made when speed-to-ship was the only goal.</p>
                        <p>Laravel gives me Eloquent, Horizon, and first-class Blade templating. Tailwind v4 + Alpine keeps the frontend light.</p>
                        <h2>The Privacy-First Constraint</h2>
                        <p>Privacy-first for me means: no third-party tracking scripts, no comment system, and an anonymous like system that can't be reverse-engineered.</p>
                        <blockquote style="border-left:2px solid var(--cms-accent);padding-left:12px;color:var(--cms-text-muted);font-style:italic;">The like system stores a hashed fingerprint — browser + IP + post ID — with a 24-hour TTL.</blockquote>
                    </div>
                </div>
            </div>
        </div>

        <div class="cms-card" style="padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div style="font-size:12px;font-weight:600;color:var(--cms-text-secondary);">SEO & Open Graph</div>
            <div class="form-group">
                <label class="form-label">Meta Title</label>
                <input type="text" class="cms-input" value="Building a Privacy-First CMS with Laravel and Claude">
            </div>
            <div class="form-group">
                <label class="form-label">Meta Description</label>
                <textarea class="cms-input" rows="2" style="resize:vertical;">How I migrated my Next.js portfolio to a full Laravel web application with 2FA, Cloudinary, and Claude AI integration.</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">OG Image</label>
                <div style="display:flex;gap:8px;align-items:center;">
                    <div style="width:80px;height:48px;background:var(--cms-bg-surface-2);border:1px solid var(--cms-border);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--cms-text-faint);">⊟</div>
                    <button class="btn btn-secondary btn-sm">Change Image</button>
                    <button class="btn btn-ghost btn-sm" style="color:var(--cms-error);">Remove</button>
                </div>
            </div>
        </div>

    </div>

    <div class="editor-sidebar">

        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Publish</div>
            <div class="sidebar-panel-body">
                <div class="meta-row">
                    <label class="form-label">Status</label>
                    <select class="cms-input cms-select"><option>Published</option><option>Draft</option><option>Private</option><option>Archived</option></select>
                </div>
                <div class="meta-row">
                    <label class="form-label">Published At</label>
                    <input type="datetime-local" class="cms-input" value="2026-06-28T09:00" style="font-family:var(--cms-font-mono);font-size:12px;">
                </div>
                <button class="btn btn-primary" style="width:100%;justify-content:center;">Save Changes</button>
                <button class="btn btn-secondary" style="width:100%;justify-content:center;">↗ View Live</button>
            </div>
        </div>

        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Tags</div>
            <div class="sidebar-panel-body">
                <div class="tag-input-wrap">
                    <span class="tag-chip tag-chip-amber">AI <button class="tag-chip-remove">×</button></span>
                    <span class="tag-chip tag-chip-teal">Laravel <button class="tag-chip-remove">×</button></span>
                    <span class="tag-chip tag-chip-amber">Security <button class="tag-chip-remove">×</button></span>
                    <input class="tag-input-field" placeholder="Add tag…">
                </div>
            </div>
        </div>

        <div class="sidebar-panel">
            <div class="sidebar-panel-header">AI Tools</div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <!-- Showing a post-audit result -->
                <div class="audit-result">
                    <div style="font-size:11px;font-weight:500;color:var(--cms-success);margin-bottom:2px;">✓ Audit complete</div>
                    <div class="audit-sep"></div>
                    <div class="audit-score-row"><span class="audit-score-label">Readability</span><span class="audit-score-val">8/10</span></div>
                    <div class="audit-score-row"><span class="audit-score-label">SEO signals</span><span class="audit-score-val">7/10</span></div>
                    <div class="audit-score-row"><span class="audit-score-label">Tag match</span><span class="audit-score-val">9/10</span></div>
                    <div class="audit-sep"></div>
                    <div class="audit-suggestion">Consider adding a summary paragraph at the end.</div>
                    <div class="audit-suggestion">Meta description could be more specific about the tech stack used.</div>
                </div>
                <button class="ai-btn">✦ Re-audit</button>
                <button class="ai-btn">✦ Regenerate Excerpt</button>
            </div>
        </div>

        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Post Info</div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <div style="display:flex;justify-content:space-between;"><span style="font-size:12px;color:var(--cms-text-muted);">Read time</span><span style="font-family:var(--cms-font-mono);font-size:12px;color:var(--cms-text-secondary);">8 min</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="font-size:12px;color:var(--cms-text-muted);">Word count</span><span style="font-family:var(--cms-font-mono);font-size:12px;color:var(--cms-text-secondary);">1,612</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="font-size:12px;color:var(--cms-text-muted);">Likes</span><span style="font-family:var(--cms-font-mono);font-size:12px;color:var(--cms-text-secondary);">24</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="font-size:12px;color:var(--cms-text-muted);">Created</span><span style="font-family:var(--cms-font-mono);font-size:11px;color:var(--cms-text-muted);">27 Jun 2026</span></div>
            </div>
        </div>

        <div class="sidebar-panel danger-zone">
            <div class="sidebar-panel-header">Danger Zone</div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <button class="btn btn-danger" style="width:100%;justify-content:center;">Delete Post</button>
                <p class="form-hint" style="text-align:center;">This cannot be undone.</p>
            </div>
        </div>

    </div>
</div>
@endsection
