<!DOCTYPE html>
<html lang="en" data-cms data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Save Recovery Codes — ~/dav/devs cms</title>
    @vite(['resources/css/cms.css', 'resources/js/cms.js'])
</head>
<body style="background:var(--cms-bg-page);color:var(--cms-text-primary);font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;">
    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:32px;width:100%;max-width:440px;">
        <div style="text-align:center;margin-bottom:24px;">
            <span style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-accent);display:block;margin-bottom:8px;">~/dav/devs cms</span>
            <h1 style="font-size:20px;font-weight:600;margin:0;">Save Your Recovery Codes</h1>
            <p style="font-size:13px;color:var(--cms-warning);margin:8px 0 0;">Store these somewhere safe. Each code can only be used once.</p>
        </div>
        <div style="background:var(--cms-bg-surface-2);border:1px solid var(--cms-border);border-radius:8px;padding:16px;margin-bottom:20px;">
            @foreach($codes as $code)
            <div style="font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:500;letter-spacing:0.08em;padding:4px 0;color:var(--cms-text-primary);">{{ $code }}</div>
            @endforeach
        </div>
        <a href="{{ route('panel.dashboard') }}"
           style="display:block;text-align:center;background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border-radius:5px;padding:9px;font-size:13px;font-weight:500;text-decoration:none;">
            I've saved these codes — continue
        </a>
    </div>
</body>
</html>
