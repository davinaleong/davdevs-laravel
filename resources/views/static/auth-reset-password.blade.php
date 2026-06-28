@extends('static.layouts.auth')

@section('title', 'Set New Password — Dav/Devs')

@section('content')
<a href="/" class="auth-wordmark">dav<span>/</span>devs</a>

<div class="auth-card">
    <h1 class="auth-card-title">Set new password</h1>
    <p class="auth-card-sub">Choose a strong password — at least 12 characters.</p>

    <form>
        <div class="form-group">
            <label class="form-label">New password</label>
            <div class="password-wrapper">
                <input type="password" class="form-input" placeholder="min. 12 characters" value="C0rr3ct-H0rse!">
                <button type="button" class="password-toggle">hide</button>
            </div>
            <div class="strength-bar">
                <div class="strength-fill strong"></div>
            </div>
            <span class="strength-label strong">strong</span>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm new password</label>
            <div class="password-wrapper">
                <input type="password" class="form-input" placeholder="repeat password" value="C0rr3ct-H0rse!">
                <button type="button" class="password-toggle">hide</button>
            </div>
        </div>

        <button type="submit" class="btn-primary">update password →</button>
    </form>
</div>
@endsection
