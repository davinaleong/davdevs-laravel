@extends('static.layouts.auth')

@section('title', 'Save Recovery Codes — Dav/Devs')

@push('head')
<style>
    .auth-card { max-width: 400px; }
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
            <div class="step-dot done">✓</div>
            <span class="step-label">set up 2FA</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-dot done">✓</div>
            <span class="step-label">verify</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-dot active">4</div>
            <span class="step-label active">save codes</span>
        </div>
    </div>

    <h1 class="auth-card-title">Save recovery codes</h1>
    <p class="auth-card-sub">Store these somewhere safe — a password manager or printed paper. Each code works once.</p>

    <div class="auth-notice auth-notice-error">
        You won't be able to see these again after leaving this page.
    </div>

    <div class="recovery-grid">
        <div class="recovery-code">A3F7-K9PM-2QXR</div>
        <div class="recovery-code">BN84-ZTJW-7HCV</div>
        <div class="recovery-code">CX52-DRMY-4LQS</div>
        <div class="recovery-code">DG19-WPKN-8FBT</div>
        <div class="recovery-code">EH63-YZMX-5JVU</div>
        <div class="recovery-code">FK47-CQLN-1RSP</div>
        <div class="recovery-code">GM28-TBHW-6XDA</div>
        <div class="recovery-code">HJ91-VSEZ-3KNY</div>
    </div>

    <button class="btn-ghost" style="margin-bottom:8px;margin-top:0;">download as .txt</button>
    <button class="btn-primary" style="margin-top:0;">i've saved these codes →</button>
</div>
@endsection
