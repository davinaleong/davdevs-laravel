{{--
    Shared pricing widget — reused by both individual-book and bundle templates.
    A publication's pricing shape (single price vs. multiple purchase variants) is
    completely independent of whether it's a bundle (groups other publications) —
    this partial only ever looks at $publication->variants / ->store, never ->bundle,
    so either template can render either pricing shape.

    Optional: $buttonLabel — text before the em dash on the single-price button
    (defaults to "Get This").
--}}
@php
    $lemonSqueezyCheckoutBase = 'https://davinaleong.lemonsqueezy.com/checkout/buy';
    $hasVariants = $publication->variants->isNotEmpty();
    $storeUrl = $publication->store?->ls_store_url;
    $priceDisplay = $publication->store?->price_display;
    $comingSoonNote = $publication->getMeta('store_note');
    $available = $hasVariants || filled($storeUrl);
@endphp

@if($available)
    @if($hasVariants)
    <div class="grid gap-6 sm:grid-cols-2">
        @foreach($publication->variants as $variant)
        <div class="pub-card">
            <h3 class="pub-prose font-semibold text-lg mb-2" style="opacity: 1;">{{ $variant->name }}</h3>
            <div class="pub-heading text-2xl mb-4 pub-accent">{{ $variant->price_display }}</div>
            <a href="{{ $variant->ls_product_id ? $lemonSqueezyCheckoutBase.'/'.$variant->ls_product_id : '#' }}" class="pub-btn pub-btn-primary">
                Get This Version
            </a>
        </div>
        @endforeach
    </div>
    @else
    <a href="{{ $storeUrl }}" class="pub-btn pub-btn-primary pub-btn-lg">{{ $buttonLabel ?? 'Get This' }} — {{ $priceDisplay }}</a>
    @endif
@else
    <div>
        <span class="pub-btn pub-btn-coming-soon"><span class="pub-badge-dot"></span>Coming Soon</span>
        @if($comingSoonNote)<p class="pub-prose mt-4">{{ $comingSoonNote }}</p>@endif
    </div>
@endif
