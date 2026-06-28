@extends('static.layouts.auth')

@section('title', 'Sign In — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Sign in</h1>
    <p class="auth-card-sub">Welcome back. Enter your credentials to continue.</p>

    <form>
        <div class="form-group">
            <label class="form-label">Email address</label>
            <input type="email" class="form-input" placeholder="you@example.com" value="davina@gracesoft.dev">
        </div>

        <div class="form-group">
            <label class="form-label" style="display:flex;justify-content:space-between;">
                Password
                <a href="/static/auth-forgot-password" style="color:var(--accent);font-size:9px;letter-spacing:var(--tracking-mono-xs);">forgot?</a>
            </label>
            <div class="password-wrapper">
                <input type="password" class="form-input" placeholder="••••••••" value="password">
                <button type="button" class="password-toggle">show</button>
            </div>
        </div>

        <button type="submit" class="btn-primary">sign in →</button>
    </form>
</div>

<div class="auth-footer">
    Don't have an account? <a href="/static/auth-register">request access</a>
</div>
@endsection
