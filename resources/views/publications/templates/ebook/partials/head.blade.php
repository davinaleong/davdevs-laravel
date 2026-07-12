@php
    $pubFontPrimary = $publication->getMeta('design_font_primary') ?: 'Inter';
    $pubFontSecondary = $publication->getMeta('design_font_secondary') ?: 'Cormorant Garamond';
    $pubColorPrimary = $publication->getMeta('design_color_primary') ?: '#0E0E10';
    $pubColorAccent = $publication->getMeta('design_color_accent') ?: '#D4A757';
    $pubFontFamilies = collect([$pubFontPrimary, $pubFontSecondary])->unique()->map(fn ($f) => str_replace(' ', '+', $f).':wght@400;600;700');

    $faviconImageId = $publication->getMeta('favicon_image_id')
        ?: (\Illuminate\Support\Facades\Cache::remember('settings', 3600, function () {
            return \App\Models\Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        })['favicon_image_id'] ?? null);
    $faviconImage = $faviconImageId
        ? \Illuminate\Support\Facades\Cache::remember(
            "favicon_image_{$faviconImageId}",
            3600,
            fn () => (fn ($img) => $img ? ['url' => $img->url, 'format' => $img->format] : null)(
                \App\Models\Image::find($faviconImageId),
            ),
        )
        : null;
    $faviconIcoUrl = $faviconImage ? preg_replace('#/upload/#', '/upload/f_ico/', $faviconImage['url'], 1) : null;
    $faviconPngUrl = $faviconImage ? preg_replace('#/upload/#', '/upload/f_png/', $faviconImage['url'], 1) : null;
    $faviconSvgUrl = $faviconImage && $faviconImage['format'] === 'svg' ? $faviconImage['url'] : null;
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $publication->title }}</title>
    <meta name="description" content="{{ $publication->tagline ?: $publication->excerpt }}">
    @if($publication->ogImage)
    <meta property="og:image" content="{{ $publication->ogImage->url }}">
    @elseif($publication->coverImage)
    <meta property="og:image" content="{{ $publication->coverImage->url }}">
    @endif
    <meta property="og:title" content="{{ $publication->title }}">
    <meta property="og:description" content="{{ $publication->tagline ?: $publication->excerpt }}">

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $pubFontFamilies->implode('&family=') }}&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
    @include('publications.templates.ebook.partials.styles')

    <style>
        :root {
            --pub-color-primary: {{ $pubColorPrimary }};
            --pub-color-accent: {{ $pubColorAccent }};
            --pub-font-primary: '{{ $pubFontPrimary }}', sans-serif;
            --pub-font-secondary: '{{ $pubFontSecondary }}', serif;
        }
    </style>
</head>
