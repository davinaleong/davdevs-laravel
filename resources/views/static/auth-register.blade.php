@extends('static.layouts.auth')

@section('title', 'Create Account — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Create account</h1>
    <p class="auth-card-sub">Fill in the details below to get started.</p>

    <form>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">First name</label>
                <input type="text" class="form-input" placeholder="Davina">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Last name</label>
                <input type="text" class="form-input" placeholder="Leong">
            </div>
        </div>

        <div class="form-group" style="margin-top:14px;">
            <label class="form-label">Email address</label>
            <input type="email" class="form-input" placeholder="you@example.com">
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-input" placeholder="min. 12 characters">
                <button type="button" class="password-toggle">show</button>
            </div>
            <div class="strength-bar">
                <div class="strength-fill fair" style="width:50%;"></div>
            </div>
            <span class="strength-label fair">fair — add symbols or numbers</span>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm password</label>
            <div class="password-wrapper">
                <input type="password" class="form-input" placeholder="repeat password">
                <button type="button" class="password-toggle">show</button>
            </div>
        </div>

        <button type="submit" class="btn-primary">create account →</button>
    </form>
</div>

<div class="auth-footer">
    Already have an account? <a href="/static/auth-login">sign in</a>
</div>
@endsection
