@extends('static.layouts.auth')

@section('title', 'Check Your Email — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Check your email</h1>
    <p class="auth-card-sub">If an account exists for that address, a reset link is on its way.</p>

    <div class="auth-notice auth-notice-success" style="margin-top:4px;">
        Reset link sent to <strong style="color:var(--text-primary);">davina@gracesoft.dev</strong>
    </div>

    <p style="font-size:11px;color:var(--text-muted);margin-bottom:16px;line-height:1.6;">
        The link expires in <strong style="color:var(--text-secondary);">60 minutes</strong>.
        Check your spam folder if it doesn't arrive within a couple of minutes.
    </p>

    <a href="/static/auth-login"><button class="btn-ghost" style="margin-top:0;">← back to sign in</button></a>

    <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <span class="auth-divider-label">didn't receive it?</span>
        <div class="auth-divider-line"></div>
    </div>

    <button class="btn-ghost" style="margin-top:0;">resend link</button>
</div>
@endsection
