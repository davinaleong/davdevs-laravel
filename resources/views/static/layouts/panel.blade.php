<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-cms data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CMS — Dav/Devs')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cms-font-ui:   'Inter', sans-serif;
            --cms-font-mono: 'JetBrains Mono', monospace;

            --cms-topbar-height:     40px;
            --cms-sidebar-width:     200px;
            --cms-sidebar-collapsed: 52px;
            --cms-content-pad:       24px;
            --cms-content-max:       1100px;
            --cms-form-gap:          16px;
            --cms-field-label-gap:   6px;
            --cms-table-row-height:  44px;
            --cms-table-header-height: 36px;
            --cms-card-pad:          20px;

            --cms-radius-card:   8px;
            --cms-radius-input:  5px;
            --cms-radius-btn:    5px;
            --cms-radius-badge:  3px;
            --cms-radius-tag:    3px;
            --cms-radius-modal:  10px;
            --cms-radius-toast:  6px;

            --dur-fast: 120ms;
            --dur-base: 200ms;
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
        }

        [data-cms][data-theme="light"] {
            --cms-topbar-bg:       #D4A757;
            --cms-topbar-text:     #0E0E10;
            --cms-topbar-badge-bg: rgba(0,0,0,0.12);
            --cms-topbar-badge-text: #0E0E10;

            --cms-sidebar-bg:      #F4F2ED;
            --cms-sidebar-border:  #E5E3DD;
            --cms-sidebar-link:    #5A5850;
            --cms-sidebar-link-active: #0E0E10;
            --cms-sidebar-active-bar:  #D4A757;
            --cms-sidebar-active-bg:   #FFFFFF;

            --cms-bg-page:         #FAFAF8;
            --cms-bg-surface:      #FFFFFF;
            --cms-bg-surface-2:    #F5F3EE;
            --cms-bg-hover:        #F0EDE8;

            --cms-border:          #E5E3DD;
            --cms-border-strong:   #D0CEC8;
            --cms-border-focus:    #D4A757;

            --cms-text-primary:    #1A1916;
            --cms-text-secondary:  #5A5850;
            --cms-text-muted:      #9A9590;
            --cms-text-faint:      #C5C3BC;
            --cms-text-on-accent:  #0E0E10;

            --cms-accent:          #D4A757;
            --cms-accent-hover:    #C49A48;
            --cms-accent-tint:     rgba(212,167,87,0.10);
            --cms-accent-border:   rgba(212,167,87,0.30);

            --cms-status-published:    #2B7A5E;
            --cms-status-draft:        #9A9590;
            --cms-status-private:      #7A5A2A;
            --cms-status-archived:     #5A5850;
            --cms-status-published-bg: rgba(43,122,94,0.10);
            --cms-status-draft-bg:     rgba(154,149,144,0.10);
            --cms-status-private-bg:   rgba(122,90,42,0.10);
            --cms-status-archived-bg:  rgba(90,88,80,0.10);

            --cms-type-article:    #1A6BAA; --cms-type-article-bg:  #EBF5FF;
            --cms-type-project:    #8A5A0A; --cms-type-project-bg:  #FFF6E8;
            --cms-type-tool:       #1A6B4A; --cms-type-tool-bg:     #EAF5F0;
            --cms-type-notebook:   #6A3A9A; --cms-type-notebook-bg: #F3EEFF;
            --cms-type-ebook:      #A03020; --cms-type-ebook-bg:    #FAEEE9;
            --cms-type-sermon:     #7A4A1A; --cms-type-sermon-bg:   #FFF3E0;
            --cms-type-page:       #3A5A7A; --cms-type-page-bg:     #EEF3FA;
            --cms-type-fem:        #3A7A5A; --cms-type-fem-bg:      #EAF5EE;
            --cms-type-ks:         #5A5A8A; --cms-type-ks-bg:       #EEEEFF;

            --cms-success:    #2B7A5E; --cms-success-bg: rgba(43,122,94,0.10);
            --cms-warning:    #D4A757; --cms-warning-bg: rgba(212,167,87,0.12);
            --cms-error:      #C4553A; --cms-error-bg:   rgba(196,85,58,0.10);
            --cms-info:       #1A6BAA; --cms-info-bg:    rgba(26,107,170,0.10);

            --cms-table-header-bg: #F4F2ED;
            --cms-table-row-hover: #F8F6F2;
            --cms-table-border:    #E5E3DD;
            --cms-table-stripe:    #FAFAF8;

            --cms-input-bg:              #FFFFFF;
            --cms-input-border:          #D0CEC8;
            --cms-input-border-hover:    #B8B6B0;
            --cms-input-border-focus:    #D4A757;
            --cms-input-text:            #1A1916;
            --cms-input-placeholder:     #C5C3BC;
            --cms-input-disabled-bg:     #F4F2ED;

            --cms-editor-bg:      #FFFFFF;
            --cms-editor-border:  #E5E3DD;
            --cms-editor-toolbar: #F4F2ED;
            --cms-editor-text:    #1A1916;
            --cms-editor-preview: #FAFAF8;

            --cms-btn-primary-bg:       #D4A757;
            --cms-btn-primary-text:     #0E0E10;
            --cms-btn-secondary-bg:     #FFFFFF;
            --cms-btn-secondary-border: #D0CEC8;
            --cms-btn-secondary-text:   #5A5850;
            --cms-btn-danger-bg:        #C4553A;
            --cms-btn-danger-text:      #FFFFFF;
            --cms-btn-ghost-text:       #9A9590;

            --cms-log-debug:    #9A9590; --cms-log-debug-bg:    rgba(154,149,144,0.08);
            --cms-log-info:     #1A6BAA; --cms-log-info-bg:     rgba(26,107,170,0.08);
            --cms-log-warning:  #D4A757; --cms-log-warning-bg:  rgba(212,167,87,0.10);
            --cms-log-error:    #C4553A; --cms-log-error-bg:    rgba(196,85,58,0.10);
            --cms-log-critical: #7A1A0A; --cms-log-critical-bg: rgba(122,26,10,0.10);
        }

        [data-cms][data-theme="dark"] {
            --cms-topbar-bg:       #B88A3A;
            --cms-topbar-text:     #0E0E10;
            --cms-topbar-badge-bg: rgba(0,0,0,0.20);
            --cms-topbar-badge-text: #0E0E10;

            --cms-sidebar-bg:      #1A1A1E;
            --cms-sidebar-border:  #2A2A30;
            --cms-sidebar-link:    #6A6A72;
            --cms-sidebar-link-active: #F5F0E8;
            --cms-sidebar-active-bar:  #D4A757;
            --cms-sidebar-active-bg:   #242428;

            --cms-bg-page:         #0E0E10;
            --cms-bg-surface:      #1A1A1E;
            --cms-bg-surface-2:    #242428;
            --cms-bg-hover:        #2A2A30;

            --cms-border:          #2A2A30;
            --cms-border-strong:   #3A3A42;
            --cms-border-focus:    #D4A757;

            --cms-text-primary:    #F5F0E8;
            --cms-text-secondary:  #A8A39C;
            --cms-text-muted:      #6A6A72;
            --cms-text-faint:      #3A3A42;
            --cms-text-on-accent:  #0E0E10;

            --cms-accent:          #D4A757;
            --cms-accent-hover:    #E3B040;
            --cms-accent-tint:     rgba(212,167,87,0.10);
            --cms-accent-border:   rgba(212,167,87,0.25);

            --cms-status-published:    #4A9B7F;
            --cms-status-draft:        #6A6A72;
            --cms-status-private:      #D4A757;
            --cms-status-archived:     #4A4A52;
            --cms-status-published-bg: rgba(74,155,127,0.12);
            --cms-status-draft-bg:     rgba(106,106,114,0.12);
            --cms-status-private-bg:   rgba(212,167,87,0.12);
            --cms-status-archived-bg:  rgba(74,74,82,0.12);

            --cms-type-article:    #5AAAE0; --cms-type-article-bg:  rgba(90,170,224,0.12);
            --cms-type-project:    #D4A757; --cms-type-project-bg:  rgba(212,167,87,0.12);
            --cms-type-tool:       #4A9B7F; --cms-type-tool-bg:     rgba(74,155,127,0.12);
            --cms-type-notebook:   #A07ADA; --cms-type-notebook-bg: rgba(160,122,218,0.12);
            --cms-type-ebook:      #C4553A; --cms-type-ebook-bg:    rgba(196,85,58,0.12);
            --cms-type-sermon:     #D4A757; --cms-type-sermon-bg:   rgba(212,167,87,0.10);
            --cms-type-page:       #7AAAD0; --cms-type-page-bg:     rgba(122,170,208,0.12);
            --cms-type-fem:        #4AB07A; --cms-type-fem-bg:      rgba(74,176,122,0.12);
            --cms-type-ks:         #8A8ADA; --cms-type-ks-bg:       rgba(138,138,218,0.12);

            --cms-success:    #4A9B7F; --cms-success-bg: rgba(74,155,127,0.12);
            --cms-warning:    #D4A757; --cms-warning-bg: rgba(212,167,87,0.12);
            --cms-error:      #C4553A; --cms-error-bg:   rgba(196,85,58,0.12);
            --cms-info:       #5AAAE0; --cms-info-bg:    rgba(90,170,224,0.12);

            --cms-table-header-bg: #1A1A1E;
            --cms-table-row-hover: #1E1E22;
            --cms-table-border:    #2A2A30;
            --cms-table-stripe:    #0E0E10;

            --cms-input-bg:           #1A1A1E;
            --cms-input-border:       #2A2A30;
            --cms-input-border-hover: #3A3A42;
            --cms-input-border-focus: #D4A757;
            --cms-input-text:         #F5F0E8;
            --cms-input-placeholder:  #3A3A42;
            --cms-input-disabled-bg:  #242428;

            --cms-editor-bg:      #1A1A1E;
            --cms-editor-border:  #2A2A30;
            --cms-editor-toolbar: #242428;
            --cms-editor-text:    #F5F0E8;
            --cms-editor-preview: #0E0E10;

            --cms-btn-primary-bg:       #D4A757;
            --cms-btn-primary-text:     #0E0E10;
            --cms-btn-secondary-bg:     #1A1A1E;
            --cms-btn-secondary-border: #2A2A30;
            --cms-btn-secondary-text:   #A8A39C;
            --cms-btn-danger-bg:        #C4553A;
            --cms-btn-danger-text:      #F5F0E8;
            --cms-btn-ghost-text:       #6A6A72;

            --cms-log-debug:    #6A6A72; --cms-log-debug-bg:    rgba(106,106,114,0.08);
            --cms-log-info:     #5AAAE0; --cms-log-info-bg:     rgba(90,170,224,0.08);
            --cms-log-warning:  #D4A757; --cms-log-warning-bg:  rgba(212,167,87,0.10);
            --cms-log-error:    #C4553A; --cms-log-error-bg:    rgba(196,85,58,0.10);
            --cms-log-critical: #FF6B4A; --cms-log-critical-bg: rgba(255,107,74,0.12);
        }

        html, body { height: 100%; }

        body {
            background: var(--cms-bg-page);
            color: var(--cms-text-primary);
            font-family: var(--cms-font-ui);
            font-size: 13px;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
        }

        a { color: inherit; text-decoration: none; }

        /* ── TOPBAR ───────────────────────────────────────────────── */
        .cms-topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            height: var(--cms-topbar-height);
            background: var(--cms-topbar-bg);
            display: flex; align-items: center;
            padding: 0 16px; gap: 12px;
        }
        .cms-topbar-logo {
            font-family: var(--cms-font-mono); font-size: 11px; font-weight: 500;
            color: var(--cms-topbar-text); white-space: nowrap;
        }
        .cms-topbar-badge {
            background: var(--cms-topbar-badge-bg);
            color: var(--cms-topbar-badge-text);
            font-family: var(--cms-font-mono); font-size: 9px;
            padding: 1px 6px; border-radius: 3px;
            letter-spacing: 0.06em;
        }
        .cms-topbar-spacer { flex: 1; }
        .cms-topbar-actions { display: flex; align-items: center; gap: 10px; }
        .cms-topbar-btn {
            background: var(--cms-topbar-badge-bg);
            border: none; cursor: pointer;
            color: var(--cms-topbar-text);
            font-family: var(--cms-font-ui); font-size: 12px;
            padding: 4px 10px; border-radius: 4px;
            display: flex; align-items: center; gap: 5px;
        }
        .cms-topbar-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            background: var(--cms-topbar-badge-bg);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--cms-font-mono); font-size: 10px;
            color: var(--cms-topbar-text); cursor: pointer;
        }

        /* ── SHELL ────────────────────────────────────────────────── */
        .cms-shell {
            display: flex;
            margin-top: var(--cms-topbar-height);
            min-height: calc(100vh - var(--cms-topbar-height));
        }

        /* ── SIDEBAR ──────────────────────────────────────────────── */
        .cms-sidebar {
            width: var(--cms-sidebar-width);
            min-width: var(--cms-sidebar-width);
            background: var(--cms-sidebar-bg);
            border-right: 0.5px solid var(--cms-sidebar-border);
            display: flex; flex-direction: column;
            position: sticky; top: var(--cms-topbar-height);
            height: calc(100vh - var(--cms-topbar-height));
            overflow-y: auto;
        }
        .cms-sidebar-section { padding: 16px 0 8px; }
        .cms-sidebar-section-label {
            font-family: var(--cms-font-mono); font-size: 9px;
            color: var(--cms-text-faint); letter-spacing: 0.10em;
            text-transform: uppercase;
            padding: 0 14px 6px;
            display: flex; align-items: center; gap: 6px;
        }
        .cms-sidebar-section-dot {
            width: 5px; height: 5px; border-radius: 50%;
            flex-shrink: 0;
        }
        .cms-nav-item {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 14px;
            font-family: var(--cms-font-ui); font-size: 13px;
            color: var(--cms-sidebar-link);
            cursor: pointer;
            transition: background var(--dur-fast), color var(--dur-fast);
            border-left: 2px solid transparent;
            position: relative;
        }
        .cms-nav-item:hover {
            background: var(--cms-bg-hover);
            color: var(--cms-sidebar-link-active);
        }
        .cms-nav-item.active {
            background: var(--cms-sidebar-active-bg);
            color: var(--cms-sidebar-link-active);
            border-left-color: var(--cms-sidebar-active-bar);
            font-weight: 500;
        }
        .cms-nav-icon { font-size: 14px; width: 16px; text-align: center; flex-shrink: 0; }
        .cms-nav-badge {
            margin-left: auto;
            font-family: var(--cms-font-mono); font-size: 9px;
            color: var(--cms-text-muted);
            background: var(--cms-bg-surface-2);
            padding: 1px 5px; border-radius: 3px;
        }

        /* ── CONTENT ──────────────────────────────────────────────── */
        .cms-content {
            flex: 1; overflow: auto;
            padding: var(--cms-content-pad);
            display: flex; flex-direction: column; gap: 20px;
        }

        /* ── SHARED COMPONENTS ────────────────────────────────────── */
        .cms-page-header {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            flex-wrap: wrap;
        }
        .cms-page-title {
            font-family: var(--cms-font-ui); font-size: 20px; font-weight: 600;
            color: var(--cms-text-primary); line-height: 1.2;
        }
        .cms-page-sub {
            font-size: 12px; color: var(--cms-text-muted); margin-top: 2px;
        }
        .cms-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn {
            font-family: var(--cms-font-ui); font-size: 13px; font-weight: 500;
            border-radius: var(--cms-radius-btn); cursor: pointer;
            padding: 7px 14px; border: none;
            display: inline-flex; align-items: center; gap: 5px;
            transition: background var(--dur-fast), border-color var(--dur-fast);
            white-space: nowrap;
        }
        .btn-primary { background: var(--cms-btn-primary-bg); color: var(--cms-btn-primary-text); }
        .btn-primary:hover { background: var(--cms-accent-hover); }
        .btn-secondary {
            background: var(--cms-btn-secondary-bg); color: var(--cms-btn-secondary-text);
            border: 1px solid var(--cms-btn-secondary-border);
        }
        .btn-secondary:hover { border-color: var(--cms-border-strong); }
        .btn-danger { background: var(--cms-btn-danger-bg); color: var(--cms-btn-danger-text); }
        .btn-ghost { background: none; color: var(--cms-btn-ghost-text); }
        .btn-ghost:hover { color: var(--cms-text-secondary); }
        .btn-sm { font-size: 12px; padding: 5px 10px; }

        .cms-card {
            background: var(--cms-bg-surface);
            border: 1px solid var(--cms-border);
            border-radius: var(--cms-radius-card);
        }

        .status-chip {
            font-family: var(--cms-font-mono); font-size: 11px;
            border-radius: var(--cms-radius-badge); padding: 2px 7px;
            display: inline-block;
        }
        .status-published { color: var(--cms-status-published); background: var(--cms-status-published-bg); }
        .status-draft     { color: var(--cms-status-draft);     background: var(--cms-status-draft-bg); }
        .status-private   { color: var(--cms-status-private);   background: var(--cms-status-private-bg); }
        .status-archived  { color: var(--cms-status-archived);  background: var(--cms-status-archived-bg); }

        .type-badge {
            font-family: var(--cms-font-mono); font-size: 11px;
            border-radius: var(--cms-radius-badge); padding: 2px 7px;
            display: inline-block;
        }
        .type-article  { color: var(--cms-type-article);  background: var(--cms-type-article-bg); }
        .type-project  { color: var(--cms-type-project);  background: var(--cms-type-project-bg); }
        .type-tool     { color: var(--cms-type-tool);     background: var(--cms-type-tool-bg); }
        .type-notebook { color: var(--cms-type-notebook); background: var(--cms-type-notebook-bg); }
        .type-ebook    { color: var(--cms-type-ebook);    background: var(--cms-type-ebook-bg); }
        .type-sermon   { color: var(--cms-type-sermon);   background: var(--cms-type-sermon-bg); }
        .type-page     { color: var(--cms-type-page);     background: var(--cms-type-page-bg); }
        .type-fem      { color: var(--cms-type-fem);      background: var(--cms-type-fem-bg); }
        .type-ks       { color: var(--cms-type-ks);       background: var(--cms-type-ks-bg); }

        .cms-input {
            background: var(--cms-input-bg);
            border: 1px solid var(--cms-input-border);
            border-radius: var(--cms-radius-input);
            color: var(--cms-input-text);
            font-family: var(--cms-font-ui); font-size: 13px;
            padding: 7px 10px; outline: none; width: 100%;
            transition: border-color var(--dur-fast);
        }
        .cms-input::placeholder { color: var(--cms-input-placeholder); }
        .cms-input:focus { border-color: var(--cms-input-border-focus); outline: 2px solid var(--cms-accent-tint); outline-offset: 2px; }
        .cms-select {
            appearance: none;
            background: var(--cms-input-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239A9590'/%3E%3C/svg%3E") no-repeat right 10px center;
            padding-right: 28px;
        }
        .form-label {
            font-family: var(--cms-font-ui); font-size: 12px; font-weight: 500;
            color: var(--cms-text-secondary); display: block;
            margin-bottom: var(--cms-field-label-gap);
        }
        .form-hint {
            font-size: 11px; color: var(--cms-text-muted); margin-top: 4px;
        }

        /* ── DATA TABLE ───────────────────────────────────────────── */
        .cms-table-wrap { overflow-x: auto; }
        .cms-table {
            width: 100%; border-collapse: collapse;
        }
        .cms-table th {
            background: var(--cms-table-header-bg);
            height: var(--cms-table-header-height);
            padding: 0 14px;
            font-family: var(--cms-font-mono); font-size: 10px; font-weight: 400;
            color: var(--cms-text-muted);
            text-align: left; text-transform: uppercase; letter-spacing: 0.06em;
            border-bottom: 1px solid var(--cms-table-border);
            white-space: nowrap;
        }
        .cms-table td {
            height: var(--cms-table-row-height);
            padding: 0 14px;
            font-size: 13px;
            border-bottom: 0.5px solid var(--cms-table-border);
            vertical-align: middle;
        }
        .cms-table tr:hover td { background: var(--cms-table-row-hover); }
        .cms-table tr:nth-child(even) td { background: var(--cms-table-stripe); }
        .cms-table tr:nth-child(even):hover td { background: var(--cms-table-row-hover); }

        .table-actions { display: flex; gap: 6px; align-items: center; }
        .tbl-link {
            font-size: 12px; color: var(--cms-text-muted); cursor: pointer;
            transition: color var(--dur-fast);
        }
        .tbl-link:hover { color: var(--cms-text-primary); }
        .tbl-link.danger:hover { color: var(--cms-error); }

        /* ── TOAST ────────────────────────────────────────────────── */
        .cms-toast-stack {
            position: fixed; bottom: 20px; right: 20px; z-index: 500;
            display: flex; flex-direction: column; gap: 8px; align-items: flex-end;
        }
        .cms-toast {
            background: var(--cms-bg-surface);
            border: 1px solid var(--cms-border);
            border-radius: var(--cms-radius-toast);
            padding: 10px 14px 10px 12px;
            font-size: 12px; color: var(--cms-text-secondary);
            display: flex; align-items: center; gap: 10px;
            min-width: 240px; max-width: 320px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .cms-toast-bar { width: 3px; height: 100%; border-radius: 2px; align-self: stretch; flex-shrink: 0; }
        .toast-success .cms-toast-bar { background: var(--cms-success); }
        .toast-warning .cms-toast-bar { background: var(--cms-warning); }
        .toast-error   .cms-toast-bar { background: var(--cms-error); }
        .toast-info    .cms-toast-bar { background: var(--cms-info); }
        .cms-toast-msg { flex: 1; line-height: 1.4; }
        .cms-toast-close {
            background: none; border: none; cursor: pointer;
            color: var(--cms-text-faint); font-size: 14px; line-height: 1;
        }

        @media (max-width: 768px) {
            .cms-sidebar { display: none; }
            .cms-content { padding: 16px; }
        }
    </style>
    @stack('head')
</head>
<body>

<!-- TOPBAR -->
<header class="cms-topbar">
    <span class="cms-topbar-logo">dav/devs</span>
    <span class="cms-topbar-badge">cms</span>
    <div class="cms-topbar-spacer"></div>
    <div class="cms-topbar-actions">
        <a href="/"><button class="cms-topbar-btn">↗ view site</button></a>
        <button class="cms-topbar-btn">◐</button>
        <div class="cms-topbar-avatar">DL</div>
    </div>
</header>

<div class="cms-shell">

    <!-- SIDEBAR -->
    <nav class="cms-sidebar">
        <div class="cms-sidebar-section">
            <div class="cms-sidebar-section-label">
                <span class="cms-sidebar-section-dot" style="background:#D4A757;"></span>
                content
            </div>
            <a href="/static/panel-dashboard" class="cms-nav-item @yield('nav-dashboard')">
                <span class="cms-nav-icon">⊞</span> Dashboard
            </a>
            <a href="/static/panel-posts" class="cms-nav-item @yield('nav-posts')">
                <span class="cms-nav-icon">✦</span> All Posts
                <span class="cms-nav-badge">94</span>
            </a>
            <a href="/static/panel-post-new" class="cms-nav-item @yield('nav-new-post')">
                <span class="cms-nav-icon">＋</span> New Post
            </a>
        </div>

        <div class="cms-sidebar-section">
            <div class="cms-sidebar-section-label">
                <span class="cms-sidebar-section-dot" style="background:#4A9B7F;"></span>
                media
            </div>
            <a href="/static/panel-images" class="cms-nav-item @yield('nav-images')">
                <span class="cms-nav-icon">⊟</span> Images
            </a>
            <a href="#" class="cms-nav-item">
                <span class="cms-nav-icon">▶</span> YouTube Embeds
            </a>
            <a href="#" class="cms-nav-item">
                <span class="cms-nav-icon">⊕</span> Links
            </a>
        </div>

        <div class="cms-sidebar-section">
            <div class="cms-sidebar-section-label">
                <span class="cms-sidebar-section-dot" style="background:#9A9590;"></span>
                system
            </div>
            <a href="#" class="cms-nav-item">
                <span class="cms-nav-icon">◎</span> Jokes
            </a>
            <a href="/static/panel-logs" class="cms-nav-item @yield('nav-logs')">
                <span class="cms-nav-icon">≡</span> Logs
            </a>
            <a href="/static/panel-settings" class="cms-nav-item @yield('nav-settings')">
                <span class="cms-nav-icon">⚙</span> Settings
            </a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="cms-content">
        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>
