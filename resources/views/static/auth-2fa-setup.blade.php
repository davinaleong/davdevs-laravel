@extends('static.layouts.auth')

@section('title', 'Set Up Two-Factor Auth — Dav/Devs')

@push('head')
<style>
    .auth-card { max-width: 400px; }
    .secret-key {
        background: var(--bg-surface-2);
        border: 0.5px solid var(--border-default);
        border-radius: var(--radius-btn);
        padding: 10px 14px;
        font-family: var(--font-mono); font-size: 11px;
        color: var(--text-secondary); letter-spacing: 0.08em;
        text-align: center; word-break: break-all;
        margin-bottom: 16px;
        display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .copy-btn {
        font-family: var(--font-mono); font-size: 8px;
        color: var(--accent); background: none; border: none;
        cursor: pointer; white-space: nowrap; letter-spacing: var(--tracking-mono-xs);
        flex-shrink: 0;
    }
    .copy-btn:hover { color: var(--accent-hover); }
</style>
@endpush

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <!-- Step indicator -->
    <div class="step-indicator">
        <div class="step">
            <div class="step-dot done">✓</div>
            <span class="step-label">sign in</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-dot active">2</div>
            <span class="step-label active">set up 2FA</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-dot">3</div>
            <span class="step-label">verify</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-dot">4</div>
            <span class="step-label">save codes</span>
        </div>
    </div>

    <h1 class="auth-card-title">Set up authenticator</h1>
    <p class="auth-card-sub">Scan this QR code with your authenticator app — Google Authenticator, Authy, or 1Password work great.</p>

    <!-- QR code (representative pattern) -->
    <div class="qr-wrapper">
        <div class="qr-box" id="qr">
            <!-- Row 1 -->
            <div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div>
            <!-- Row 2 -->
            <div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell w"></div><div class="qr-cell"></div>
            <!-- Row 3 -->
            <div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div>
            <!-- Row 4 -->
            <div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div>
            <!-- Row 5 -->
            <div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell"></div>
            <!-- Row 6 -->
            <div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell w"></div><div class="qr-cell"></div><div class="qr-cell w"></div><div class="qr-cell w"></div><div class="qr-cell"></div>
            <!-- Row 7 -->
            <div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div><div class="qr-cell"></div>
        </div>
        <p class="qr-label">Scan with your authenticator app</p>
    </div>

    <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <span class="auth-divider-label">or enter key manually</span>
        <div class="auth-divider-line"></div>
    </div>

    <div class="secret-key">
        <span>JBSW Y3DP EHPK 3PXP</span>
        <button class="copy-btn">copy</button>
    </div>

    <a href="/static/auth-2fa-challenge"><button class="btn-primary">i've scanned it →</button></a>
</div>
@endsection
