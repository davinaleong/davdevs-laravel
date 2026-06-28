@extends('static.layouts.auth')

@section('title', 'Recovery Code — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Use recovery code</h1>
    <p class="auth-card-sub">Enter one of your 8 single-use recovery codes. It will be consumed after use.</p>

    <div class="auth-notice" style="margin-top:4px;">
        Each code can only be used once. Generate new codes after recovery.
    </div>

    <form>
        <div class="form-group">
            <label class="form-label">Recovery code</label>
            <input type="text" class="form-input" placeholder="XXXX-XXXX-XXXX" style="font-family:var(--font-mono);letter-spacing:0.06em;">
        </div>

        <button type="submit" class="btn-primary">verify →</button>
        <a href="/static/auth-2fa-challenge"><button type="button" class="btn-ghost">← back to authenticator code</button></a>
    </form>
</div>
@endsection
