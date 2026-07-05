<!DOCTYPE html>
<html lang="en" data-cms data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Setup — ~/dav/devs cms</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" href="/favicon.png">
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
</head>
<body style="background:var(--cms-bg-page);color:var(--cms-text-primary);font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;">
    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:32px;width:100%;max-width:440px;">
        <div style="text-align:center;margin-bottom:24px;">
            <span style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-accent);display:block;margin-bottom:8px;">~/dav/devs cms</span>
            <h1 style="font-size:20px;font-weight:600;margin:0;">Set up Two-Factor Authentication</h1>
            <p style="font-size:13px;color:var(--cms-text-muted);margin:8px 0 0;">Scan the QR code with your authenticator app, then enter the 6-digit code to confirm.</p>
        </div>

        <div style="background:var(--cms-bg-surface-2);border:1px solid var(--cms-border);border-radius:8px;padding:16px;text-align:center;margin-bottom:20px;">
            <div style="display:inline-block;width:180px;height:180px;background:#FFFFFF;border-radius:6px;padding:10px;box-sizing:border-box;">
                {!! $qrCodeInline !!}
            </div>
        </div>

        <details style="margin-bottom:20px;">
            <summary style="font-size:12px;color:var(--cms-text-muted);cursor:pointer;">Can't scan? Enter key manually</summary>
            <code style="font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:0.12em;background:var(--cms-bg-surface-2);padding:8px 12px;border-radius:5px;display:block;margin-top:8px;word-break:break-all;">{{ $secret }}</code>
        </details>

        <form method="POST" action="{{ route('2fa.setup.store') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">6-digit code</label>
                <input type="text" name="code" maxlength="6" pattern="\d{6}" autocomplete="one-time-code" autofocus
                       placeholder="000000"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:9px 12px;font-family:'JetBrains Mono',monospace;font-size:20px;letter-spacing:0.2em;text-align:center;color:var(--cms-input-text);">
                @error('code')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" style="width:100%;background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:9px;font-size:13px;font-weight:500;cursor:pointer;">Confirm & Enable 2FA</button>
        </form>
    </div>
</body>
</html>
