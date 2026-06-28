@extends('static.layouts.panel')

@section('title', 'Images — CMS')
@section('nav-images', 'active')

@push('head')
<style>
    .img-toolbar {
        display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
    }
    .img-toolbar .cms-input { max-width: 200px; }
    .filter-pill {
        font-family: var(--cms-font-mono); font-size: 11px;
        padding: 5px 12px; border-radius: 20px;
        border: 1px solid var(--cms-border);
        background: var(--cms-bg-surface);
        color: var(--cms-text-muted); cursor: pointer;
        transition: all var(--dur-fast);
    }
    .filter-pill.active {
        background: var(--cms-accent-tint);
        border-color: var(--cms-accent-border);
        color: var(--cms-accent);
    }
    .filter-pill:hover:not(.active) {
        border-color: var(--cms-border-strong);
        color: var(--cms-text-secondary);
    }
    .filter-spacer { flex: 1; }

    .upload-zone {
        border: 2px dashed var(--cms-border);
        border-radius: var(--cms-radius-card);
        padding: 28px;
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        cursor: pointer; transition: border-color var(--dur-fast), background var(--dur-fast);
    }
    .upload-zone:hover { border-color: var(--cms-accent); background: var(--cms-accent-tint); }
    .upload-icon { font-size: 28px; color: var(--cms-text-faint); }
    .upload-label { font-size: 13px; color: var(--cms-text-secondary); }
    .upload-hint  { font-size: 11px; color: var(--cms-text-muted); }

    .img-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 8px;
    }
    .img-cell {
        position: relative;
        aspect-ratio: 1;
        background: var(--cms-bg-surface-2);
        border: 1px solid var(--cms-border);
        border-radius: var(--cms-radius-card);
        overflow: hidden;
        cursor: pointer;
        transition: border-color var(--dur-fast);
    }
    .img-cell:hover { border-color: var(--cms-border-focus); }
    .img-cell.selected { border-color: var(--cms-accent); box-shadow: 0 0 0 2px var(--cms-accent-tint); }
    .img-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; color: var(--cms-text-faint);
    }
    .img-overlay {
        position: absolute; inset: 0;
        background: rgba(0,0,0,0.55);
        opacity: 0; transition: opacity var(--dur-fast);
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .img-cell:hover .img-overlay { opacity: 1; }
    .img-overlay-btn {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 4px; padding: 5px 8px;
        color: #fff; font-size: 11px; cursor: pointer;
    }
    .img-check {
        position: absolute; top: 6px; left: 6px;
        width: 18px; height: 18px; border-radius: 50%;
        background: var(--cms-accent); border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: var(--cms-text-on-accent);
    }
    .img-qr-badge {
        position: absolute; bottom: 5px; right: 5px;
        font-family: var(--cms-font-mono); font-size: 8px;
        background: rgba(0,0,0,0.55); color: #fff;
        padding: 1px 4px; border-radius: 2px;
    }

    /* LIGHTBOX */
    .lightbox-overlay {
        position: fixed; inset: 0; z-index: 300;
        background: rgba(0,0,0,0.85);
        display: flex; align-items: center; justify-content: center;
        padding: 24px;
    }
    .lightbox-panel {
        background: var(--cms-bg-surface);
        border-radius: var(--cms-radius-modal);
        max-width: 800px; width: 100%;
        display: grid; grid-template-columns: 1fr 260px;
        overflow: hidden;
        max-height: 90vh;
    }
    .lightbox-img-area {
        background: var(--cms-bg-surface-2);
        display: flex; align-items: center; justify-content: center;
        min-height: 360px; font-size: 60px; color: var(--cms-text-faint);
    }
    .lightbox-meta {
        padding: 20px;
        border-left: 1px solid var(--cms-border);
        display: flex; flex-direction: column; gap: 14px;
        overflow-y: auto;
    }
    .lightbox-title {
        font-size: 14px; font-weight: 600; color: var(--cms-text-primary);
    }
    .lb-field { display: flex; flex-direction: column; gap: 4px; }
    .lb-label { font-size: 11px; color: var(--cms-text-muted); font-weight: 500; }
    .lb-value { font-size: 13px; color: var(--cms-text-secondary); }
    .lb-mono  { font-family: var(--cms-font-mono); font-size: 11px; color: var(--cms-text-muted); }
    .lightbox-close {
        position: absolute; top: 16px; right: 16px;
        background: rgba(255,255,255,0.1); border: none; cursor: pointer;
        color: #fff; font-size: 18px; width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="cms-page-header">
    <div>
        <h1 class="cms-page-title">Images</h1>
        <p class="cms-page-sub">47 images · 3 QR codes</p>
    </div>
</div>

<!-- TOOLBAR -->
<div class="img-toolbar">
    <input type="search" class="cms-input" placeholder="Search by title or alt…">
    <div class="filter-spacer"></div>
    <span class="filter-pill active">All (50)</span>
    <span class="filter-pill">Images (47)</span>
    <span class="filter-pill">QR Codes (3)</span>
    <select class="cms-input" style="max-width:120px;"><option>Newest</option><option>Oldest</option><option>Name A–Z</option></select>
    <button class="btn btn-primary">↑ Upload</button>
</div>

<!-- UPLOAD ZONE -->
<div class="upload-zone">
    <span class="upload-icon">⊟</span>
    <span class="upload-label">Drop images here or click to upload</span>
    <span class="upload-hint">PNG, JPG, WebP, GIF · Max 10 MB each</span>
</div>

<!-- IMAGE GRID -->
<div class="img-grid">
    @php
    $imgs = [
        ['⊟','hero-banner'],['⊟','profile-photo'],['⊟','laravel-diagram'],
        ['⊟','cms-screenshot'],['⊟','code-example'],['⊟','azure-portal'],
        ['⊟','pest-output'],['⊟','qr-setup','qr'],['⊟','lighthouse-score'],
        ['⊟','tailwind-config'],['⊟','db-schema'],['⊟','auth-flow'],
        ['⊟','vite-error'],['⊟','cloudinary-dashboard'],['⊟','qr-2fa','qr'],
        ['⊟','rest-photo'],
    ];
    @endphp
    @foreach($imgs as $i => $img)
    <div class="img-cell {{ $i === 2 ? 'selected' : '' }}">
        <div class="img-placeholder">{{ $img[0] }}</div>
        <div class="img-overlay">
            <button class="img-overlay-btn">View</button>
            <button class="img-overlay-btn">Copy</button>
        </div>
        @if($i === 2)
        <div class="img-check">✓</div>
        @endif
        @if(isset($img[2]) && $img[2] === 'qr')
        <div class="img-qr-badge">QR</div>
        @endif
    </div>
    @endforeach
</div>

<!-- LIGHTBOX (shown open as mockup state) -->
<div class="lightbox-overlay" style="position:relative;margin-top:0;">
    {{-- Shown inline so it's visible in the mockup without JS --}}
</div>

<!-- LIGHTBOX (static preview card below grid) -->
<div style="margin-top:8px;">
    <div style="font-size:11px;color:var(--cms-text-muted);margin-bottom:8px;font-family:var(--cms-font-mono);">← lightbox preview (opens on image click)</div>
    <div style="border:1px solid var(--cms-border);border-radius:var(--cms-radius-modal);overflow:hidden;display:grid;grid-template-columns:1fr 260px;max-width:720px;">
        <div style="background:var(--cms-bg-surface-2);min-height:260px;display:flex;align-items:center;justify-content:center;font-size:60px;color:var(--cms-text-faint);">⊟</div>
        <div style="padding:20px;border-left:1px solid var(--cms-border);display:flex;flex-direction:column;gap:12px;">
            <div style="font-size:14px;font-weight:600;color:var(--cms-text-primary);">laravel-diagram.png</div>
            <div class="lb-field"><span class="lb-label">Alt text</span><input type="text" class="cms-input" value="Laravel architecture diagram showing MVC flow"></div>
            <div class="lb-field"><span class="lb-label">Caption</span><input type="text" class="cms-input" placeholder="Optional caption…"></div>
            <div class="lb-field"><span class="lb-label">Dimensions</span><span class="lb-mono">1280 × 720 · WebP · 48 KB</span></div>
            <div class="lb-field"><span class="lb-label">Cloudinary ID</span><span class="lb-mono">davdevs/laravel-diagram</span></div>
            <div style="display:flex;gap:6px;margin-top:auto;">
                <button class="btn btn-primary btn-sm">Save</button>
                <button class="btn btn-secondary btn-sm">Copy URL</button>
                <button class="btn btn-ghost btn-sm" style="color:var(--cms-error);margin-left:auto;">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection
