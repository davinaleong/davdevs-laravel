@extends('static.layouts.panel')

@section('title', 'New Post — CMS')
@section('nav-new-post', 'active')

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

    /* MARKDOWN EDITOR */
    .md-editor {
        border: 1px solid var(--cms-editor-border);
        border-radius: var(--cms-radius-card);
        overflow: hidden;
    }
    .md-toolbar {
        background: var(--cms-editor-toolbar);
        border-bottom: 1px solid var(--cms-editor-border);
        padding: 6px 10px;
        display: flex; gap: 2px; align-items: center; flex-wrap: wrap;
    }
    .md-tool-btn {
        font-family: var(--cms-font-mono); font-size: 11px;
        color: var(--cms-text-muted);
        background: none; border: none; cursor: pointer;
        padding: 4px 7px; border-radius: 3px;
        transition: background var(--dur-fast), color var(--dur-fast);
    }
    .md-tool-btn:hover { background: var(--cms-bg-hover); color: var(--cms-text-primary); }
    .md-tool-sep { width: 0.5px; height: 16px; background: var(--cms-border); margin: 0 4px; }
    .md-tool-view {
        margin-left: auto;
        display: flex; gap: 2px;
    }
    .md-panes {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .md-pane-editor {
        background: var(--cms-editor-bg);
        border-right: 0.5px solid var(--cms-editor-border);
        padding: 14px;
    }
    .md-pane-preview {
        background: var(--cms-editor-preview);
        padding: 14px;
    }
    .md-textarea {
        width: 100%; min-height: 380px; resize: vertical;
        background: none; border: none; outline: none;
        font-family: var(--cms-font-mono); font-size: 13px;
        color: var(--cms-editor-text); line-height: 1.7;
    }
    .md-preview-content {
        font-family: 'Georgia', serif; font-size: 14px;
        color: var(--cms-text-secondary); line-height: 1.8;
        min-height: 380px;
    }
    .md-preview-content h2 { font-size: 17px; color: var(--cms-text-primary); margin: 1.2em 0 0.4em; font-family: var(--cms-font-ui); }
    .md-preview-content p  { margin-bottom: 1em; }
    .md-preview-content code { font-family: var(--cms-font-mono); font-size: 12px; background: var(--cms-bg-surface-2); padding: 1px 4px; border-radius: 2px; }

    /* SIDEBAR PANELS */
    .editor-sidebar { display: flex; flex-direction: column; gap: 12px; }
    .sidebar-panel {
        background: var(--cms-bg-surface);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-card);
        overflow: hidden;
    }
    .sidebar-panel-header {
        padding: 10px 14px;
        border-bottom: 1px solid var(--cms-border);
        font-size: 12px; font-weight: 600;
        color: var(--cms-text-secondary);
        display: flex; align-items: center; justify-content: space-between;
    }
    .sidebar-panel-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; }

    .meta-row { display: flex; flex-direction: column; gap: var(--cms-field-label-gap); }

    .tag-input-wrap {
        display: flex; flex-wrap: wrap; gap: 4px;
        background: var(--cms-input-bg);
        border: 1px solid var(--cms-input-border);
        border-radius: var(--cms-radius-input);
        padding: 6px 8px; min-height: 38px;
        cursor: text;
    }
    .tag-chip {
        font-family: var(--cms-font-mono); font-size: 11px;
        padding: 2px 6px; border-radius: var(--cms-radius-tag);
        display: flex; align-items: center; gap: 4px;
    }
    .tag-chip-amber { color: #8A5A0A; background: #FFF6E8; }
    .tag-chip-teal  { color: #1A6B4A; background: #EAF5F0; }
    .tag-chip-remove {
        background: none; border: none; cursor: pointer;
        color: inherit; opacity: 0.5; font-size: 10px; line-height: 1;
    }
    .tag-chip-remove:hover { opacity: 1; }
    .tag-input-field { border: none; outline: none; background: none; font-size: 12px; color: var(--cms-input-text); min-width: 80px; flex: 1; }

    .ai-btn {
        width: 100%; padding: 8px;
        background: var(--cms-accent-tint);
        border: 1px solid var(--cms-accent-border);
        border-radius: var(--cms-radius-btn);
        color: var(--cms-accent); font-size: 12px; font-weight: 500;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: background var(--dur-fast);
    }
    .ai-btn:hover { background: rgba(212,167,87,0.18); }

    .publish-actions {
        display: flex; flex-direction: column; gap: 6px;
    }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">New Post</h1>
        <p class="cms-page-sub">Fill in the details below, then publish or save as draft.</p>
    </div>
    <div class="cms-actions">
        <button class="btn btn-ghost">Discard</button>
        <button class="btn btn-secondary">Save Draft</button>
        <button class="btn btn-primary">Publish →</button>
    </div>
</div>

<div class="editor-layout">

    <!-- MAIN COLUMN -->
    <div class="editor-main">

        <!-- TITLE + SLUG -->
        <div class="cms-card" style="padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" class="cms-input" placeholder="Post title…" style="font-size:16px;font-weight:500;" value="">
            </div>
            <div class="form-group">
                <label class="form-label">Slug</label>
                <input type="text" class="cms-input" placeholder="auto-generated-from-title" style="font-family:var(--cms-font-mono);font-size:12px;">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Post Type</label>
                    <select class="cms-input cms-select">
                        <option>Article</option>
                        <option>Project</option>
                        <option>Tool</option>
                        <option>Notebook</option>
                        <option>E-Book</option>
                        <option>Sermon</option>
                        <option>Page</option>
                        <option>Frontend Mentor</option>
                        <option>Knowledge Sharing</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="cms-input cms-select">
                        <option>— select —</option>
                        <option>AI</option>
                        <option>Security</option>
                        <option>Laravel</option>
                        <option>JavaScript</option>
                        <option>Faith</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Excerpt</label>
                <textarea class="cms-input" rows="2" placeholder="A short summary shown in post cards and meta descriptions…" style="resize:vertical;"></textarea>
            </div>
        </div>

        <!-- MARKDOWN EDITOR -->
        <div class="md-editor">
            <div class="md-toolbar">
                <button class="md-tool-btn">B</button>
                <button class="md-tool-btn"><em>I</em></button>
                <button class="md-tool-btn" style="text-decoration:underline;">U</button>
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
                    <button class="md-tool-btn" style="background:var(--cms-bg-hover);">Edit</button>
                    <button class="md-tool-btn">Split</button>
                    <button class="md-tool-btn">Preview</button>
                </div>
            </div>
            <div class="md-panes">
                <div class="md-pane-editor">
                    <textarea class="md-textarea" placeholder="Start writing in Markdown…"></textarea>
                </div>
                <div class="md-pane-preview">
                    <div class="md-preview-content">
                        <p style="color:var(--cms-text-faint);font-family:var(--cms-font-mono);font-size:11px;">Preview will appear here as you type…</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO / OG META -->
        <div class="cms-card" style="padding:16px;display:flex;flex-direction:column;gap:12px;">
            <div style="font-size:12px;font-weight:600;color:var(--cms-text-secondary);">SEO & Open Graph</div>
            <div class="form-group">
                <label class="form-label">Meta Title <span style="font-weight:400;color:var(--cms-text-faint);">(defaults to post title)</span></label>
                <input type="text" class="cms-input" placeholder="Override meta title…">
            </div>
            <div class="form-group">
                <label class="form-label">Meta Description</label>
                <textarea class="cms-input" rows="2" placeholder="Override meta description…" style="resize:vertical;"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">OG Image</label>
                <div style="display:flex;gap:8px;align-items:center;">
                    <div style="width:80px;height:48px;background:var(--cms-bg-surface-2);border:1px dashed var(--cms-border);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--cms-text-faint);">⊟</div>
                    <button class="btn btn-secondary btn-sm">Choose Image</button>
                </div>
            </div>
        </div>

    </div>

    <!-- SIDEBAR -->
    <div class="editor-sidebar">

        <!-- STATUS -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Publish</div>
            <div class="sidebar-panel-body">
                <div class="meta-row">
                    <label class="form-label">Status</label>
                    <select class="cms-input cms-select">
                        <option>Draft</option>
                        <option>Published</option>
                        <option>Private</option>
                        <option>Archived</option>
                    </select>
                </div>
                <div class="meta-row">
                    <label class="form-label">Published At</label>
                    <input type="datetime-local" class="cms-input" style="font-family:var(--cms-font-mono);font-size:12px;">
                </div>
                <div class="publish-actions">
                    <button class="btn btn-primary" style="width:100%;justify-content:center;">Publish →</button>
                    <button class="btn btn-secondary" style="width:100%;justify-content:center;">Save Draft</button>
                </div>
            </div>
        </div>

        <!-- TAGS -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Tags</div>
            <div class="sidebar-panel-body">
                <div class="tag-input-wrap">
                    <span class="tag-chip tag-chip-amber">AI <button class="tag-chip-remove">×</button></span>
                    <span class="tag-chip tag-chip-teal">Laravel <button class="tag-chip-remove">×</button></span>
                    <input class="tag-input-field" placeholder="Add tag…">
                </div>
                <p class="form-hint">Press Enter or comma to add a tag.</p>
            </div>
        </div>

        <!-- IMAGES -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header">
                Images
                <button class="btn btn-ghost btn-sm" style="padding:2px 6px;">Add</button>
            </div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                    <div style="aspect-ratio:4/3;background:var(--cms-bg-surface-2);border:1px solid var(--cms-border);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--cms-text-faint);">⊟</div>
                    <div style="aspect-ratio:4/3;background:var(--cms-bg-surface-2);border:1px dashed var(--cms-border);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--cms-text-faint);cursor:pointer;">＋</div>
                </div>
            </div>
        </div>

        <!-- AI TOOLS -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header">AI Tools</div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <button class="ai-btn">✦ Audit Content</button>
                <button class="ai-btn">✦ Generate Excerpt</button>
                <p class="form-hint" style="text-align:center;">Powered by Claude</p>
            </div>
        </div>

        <!-- READ TIME -->
        <div class="sidebar-panel">
            <div class="sidebar-panel-header">Post Info</div>
            <div class="sidebar-panel-body" style="gap:8px;">
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:12px;color:var(--cms-text-muted);">Read time</span>
                    <span style="font-family:var(--cms-font-mono);font-size:12px;color:var(--cms-text-secondary);">— min</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:12px;color:var(--cms-text-muted);">Word count</span>
                    <span style="font-family:var(--cms-font-mono);font-size:12px;color:var(--cms-text-secondary);">0</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
