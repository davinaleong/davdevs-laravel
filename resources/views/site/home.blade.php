<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dav/Devs</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--bg-page);color:var(--text-primary);font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;">
    <div style="text-align:center;">
        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(32px,5vw,48px);font-weight:800;letter-spacing:-0.02em;color:var(--text-primary);">
            ~/dav/<span style="color:var(--accent);">devs</span> _
        </h1>
        <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);margin-top:12px;">coming soon — site under construction</p>
        <a href="{{ route('login') }}" style="display:inline-block;margin-top:24px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--accent);text-decoration:none;border:1px solid var(--accent-border);padding:6px 14px;border-radius:5px;">cms →</a>
    </div>
</body>
</html>
