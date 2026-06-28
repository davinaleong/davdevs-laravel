@extends('static.layouts.auth')

@section('title', 'Two-Factor Challenge — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Two-factor auth</h1>
    <p class="auth-card-sub">Enter the 6-digit code from your authenticator app.</p>

    <form>
        <div class="otp-group">
            <input type="text" class="otp-input filled" maxlength="1" value="4">
            <input type="text" class="otp-input filled" maxlength="1" value="8">
            <input type="text" class="otp-input filled" maxlength="1" value="2">
            <input type="text" class="otp-input" maxlength="1" placeholder="·" style="border-color:var(--input-border-focus);">
            <input type="text" class="otp-input" maxlength="1" placeholder="·">
            <input type="text" class="otp-input" maxlength="1" placeholder="·">
        </div>

        <button type="submit" class="btn-primary">verify →</button>
    </form>

    <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <span class="auth-divider-label">lost access to your app?</span>
        <div class="auth-divider-line"></div>
    </div>

    <a href="/static/auth-2fa-recovery"><button class="btn-ghost" style="margin-top:0;">use a recovery code</button></a>
</div>

<div class="auth-footer">
    <a href="/static/auth-login">← sign in with a different account</a>
</div>
@endsection
