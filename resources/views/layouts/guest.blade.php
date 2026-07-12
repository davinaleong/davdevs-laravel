<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-cms data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dav/Devs') }}</title>
    @php
        $faviconImageId =
            \Illuminate\Support\Facades\Cache::remember('settings', 3600, function () {
                return \App\Models\Setting::all()->mapWithKeys(fn($s) => [$s->key => $s->getTypedValue()])->all();
            })['favicon_image_id'] ?? null;
        $faviconImage = $faviconImageId
            ? \Illuminate\Support\Facades\Cache::remember(
                "favicon_image_{$faviconImageId}",
                3600,
                fn() => (fn($img) => $img ? ['url' => $img->url, 'format' => $img->format] : null)(
                    \App\Models\Image::find($faviconImageId),
                ),
            )
            : null;
        $faviconIcoUrl = $faviconImage ? preg_replace('#/upload/#', '/upload/f_ico/', $faviconImage['url'], 1) : null;
        $faviconPngUrl = $faviconImage ? preg_replace('#/upload/#', '/upload/f_png/', $faviconImage['url'], 1) : null;
        $faviconSvgUrl = $faviconImage && $faviconImage['format'] === 'svg' ? $faviconImage['url'] : null;
    @endphp
    @if ($faviconImage)
        @if ($faviconSvgUrl)
            <link rel="icon" type="image/svg+xml" href="{{ $faviconSvgUrl }}">
        @endif
        <link rel="icon" type="image/png" href="{{ $faviconPngUrl }}">
        <link rel="icon" type="image/x-icon" href="{{ $faviconIcoUrl }}">
    @else
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/png" href="/favicon.png">
    @endif

    @vite(['resources/css/cms.css'])
</head>

<body data-cms data-theme="light"
    style="margin:0;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 16px;background:var(--cms-bg-page);font-family:'Inter',sans-serif;">
    <a href="/"
        style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cms-text-muted);text-decoration:none;letter-spacing:0.04em;margin-bottom:20px;">
        ~/dav<span style="color:var(--cms-accent);">/</span>devs cms
    </a>

    <div
        style="width:100%;max-width:400px;background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:28px;">
        {{ $slot }}
    </div>
</body>

</html>
