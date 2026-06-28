@extends('static.layouts.auth')

@section('title', 'Reset Password — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Forgot password?</h1>
    <p class="auth-card-sub">Enter your email and we'll send a reset link if an account exists.</p>

    <form>
        <div class="form-group">
            <label class="form-label">Email address</label>
            <input type="email" class="form-input" placeholder="you@example.com">
        </div>

        <button type="submit" class="btn-primary">send reset link →</button>
        <a href="/static/auth-login"><button type="button" class="btn-ghost">← back to sign in</button></a>
    </form>
</div>
@endsection
