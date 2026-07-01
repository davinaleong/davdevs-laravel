<!DOCTYPE html>
<html lang="en" data-cms data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Verification — ~/dav/devs cms</title>
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
</head>
<body style="background:var(--cms-bg-page);color:var(--cms-text-primary);font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;">
    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:32px;width:100%;max-width:380px;">
        <div style="text-align:center;margin-bottom:24px;">
            <span style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-accent);display:block;margin-bottom:8px;">~/dav/devs cms</span>
            <h1 style="font-size:20px;font-weight:600;margin:0;">Two-Factor Authentication</h1>
            <p style="font-size:13px;color:var(--cms-text-muted);margin:8px 0 0;">Enter the 6-digit code from your authenticator app.</p>
        </div>
        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <input type="text" name="code" maxlength="6" pattern="\d{6}" autocomplete="one-time-code" autofocus
                       placeholder="000000"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:9px 12px;font-family:'JetBrains Mono',monospace;font-size:24px;letter-spacing:0.25em;text-align:center;color:var(--cms-input-text);">
                @error('code')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" style="width:100%;background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:9px;font-size:13px;font-weight:500;cursor:pointer;">Verify</button>
        </form>
        <div style="text-align:center;margin-top:16px;">
            <a href="{{ route('2fa.recovery') }}" style="font-size:12px;color:var(--cms-text-muted);text-decoration:none;">Use a recovery code instead</a>
        </div>
    </div>
</body>
</html>
