<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-cms data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dav/Devs') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/png" href="/favicon.png">

        @vite(['resources/css/cms.css'])
    </head>
    <body data-cms data-theme="light" style="margin:0;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 16px;background:var(--cms-bg-page);font-family:'Inter',sans-serif;">
        <a href="/" style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cms-text-muted);text-decoration:none;letter-spacing:0.04em;margin-bottom:20px;">
            ~/dav<span style="color:var(--cms-accent);">/</span>devs cms
        </a>

        <div style="width:100%;max-width:400px;background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:28px;">
            {{ $slot }}
        </div>
    </body>
</html>
