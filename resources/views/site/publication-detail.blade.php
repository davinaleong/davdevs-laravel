@php
$jsonLd = json_encode([
    '@context'    => 'https://schema.org',
    '@type'       => 'Book',
    'name'        => $publication->title,
    'description' => $publication->excerpt,
    'image'       => $publication->coverImage?->url,
    'author'      => ['@type' => 'Person', 'name' => 'Davina Leong'],
    'offers'      => $publication->store ? [
        '@type'         => 'Offer',
        'price'         => $publication->store->price_display,
        'priceCurrency' => $publication->store->currency,
        'url'           => $publication->store->ls_store_url,
    ] : null,
]);
@endphp
<x-site-layout :title="$publication->title" :description="$publication->excerpt" :og-image="$publication->coverImage?->url" :json-ld="$jsonLd">
    <article style="max-width:680px;margin:0 auto;padding:48px 36px;">
        @if($publication->coverImage)<img src="{{ $publication->coverImage->url }}" style="width:100%;border-radius:10px;margin-bottom:24px;">@endif
        <h1 style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:var(--text-primary);margin:0 0 8px;">{{ $publication->title }}</h1>
        @if($publication->tagline)<p style="font-family:'Inter',sans-serif;font-size:15px;color:var(--text-secondary);margin:0 0 24px;">{{ $publication->tagline }}</p>@endif

        <div class="prose" style="font-family:'Lora',serif;font-size:14px;line-height:1.8;color:var(--text-body);margin-bottom:32px;">@markdown($publication->body)</div>

        @if($publication->store?->ls_store_url)
        <a href="{{ $publication->store->ls_store_url }}" target="_blank" rel="noopener noreferrer"
           style="display:inline-block;background:var(--accent);color:var(--bg-page);padding:12px 24px;border-radius:5px;text-decoration:none;font-family:'Inter',sans-serif;font-size:14px;font-weight:600;">
            Get it — {{ $publication->store->price_display }}
        </a>
        @endif

        @if($publication->bundle && $publication->bundleMembers->isNotEmpty())
        <div style="margin-top:32px;">
            <h2 style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--text-primary);">Included in this bundle</h2>
            <ul>
                @foreach($publication->bundleMembers as $member)
                <li><a href="{{ route('site.ebooks.show', $member->slug) }}" style="color:var(--accent);">{{ $member->title }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
    </article>
</x-site-layout>
