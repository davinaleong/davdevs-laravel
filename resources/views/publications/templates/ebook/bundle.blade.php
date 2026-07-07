@php
    $storeUrl = $publication->store?->ls_store_url;
    $priceDisplay = $publication->store?->price_display;
    $comingSoonNote = $publication->getMeta('store_note');
    $available = filled($storeUrl);
    $individualTotal = $publication->getMeta('individual_total');
    $savings = $publication->getMeta('savings');
    $quote = json_decode($publication->getMeta('quote') ?? 'null', true);
    $memberCount = $publication->bundleMembers->count();
@endphp
<!DOCTYPE html>
<html lang="en">
@include('publications.templates.ebook.partials.head')
<body class="pub-book">
    {{-- Nav --}}
    <header class="pub-nav">
        <span class="pub-nav-title">{{ $publication->title }}</span>
        @if($available)
            <a href="#get-the-bundle" class="pub-btn pub-btn-outline pub-btn-sm">Get the Bundle</a>
        @else
            <span class="pub-btn pub-btn-coming-soon pub-btn-sm">Coming Soon</span>
        @endif
    </header>

    <main>
        {{-- Hero --}}
        <section class="px-6 py-20">
            <div class="max-w-5xl mx-auto grid gap-16 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="pub-label">A Gift Bundle — {{ $memberCount }} E-Book{{ $memberCount === 1 ? '' : 's' }}</p>
                    <h1 class="pub-heading text-4xl lg:text-6xl mb-4">{{ $publication->title }}</h1>
                    @if($publication->tagline)
                    <p class="pub-heading text-xl mb-6 pub-accent" style="font-weight: 500;">{{ $publication->tagline }}</p>
                    @endif
                    @if($publication->excerpt)
                    <p class="pub-prose text-lg mb-8">{{ $publication->excerpt }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-4">
                        @if($available)
                            <a href="#get-the-bundle" class="pub-btn pub-btn-primary">Get the Bundle — {{ $priceDisplay }}</a>
                        @else
                            <span class="pub-btn pub-btn-coming-soon">Coming Soon</span>
                        @endif
                        <a href="#whats-inside" class="pub-prose">+ What's inside</a>
                    </div>
                </div>

                @if($publication->coverImage)
                <div class="flex justify-center lg:justify-end">
                    <img src="{{ $publication->coverImage->responsiveUrl(640) }}" alt="{{ $publication->title }} — Bundle" class="pub-cover-img">
                </div>
                @endif
            </div>
        </section>

        {{-- What's Inside --}}
        <section id="whats-inside" class="px-6 py-20">
            <div class="max-w-6xl mx-auto">
                <hr class="pub-divider mb-16">
                <div class="text-center mb-12">
                    <p class="pub-label">What's Inside</p>
                    <h2 class="pub-heading text-3xl lg:text-4xl">{{ $memberCount }} book{{ $memberCount === 1 ? '' : 's' }}, one gift</h2>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($publication->bundleMembers as $i => $member)
                    <a href="{{ route('site.ebooks.show', $member->slug) }}" class="pub-card" style="text-decoration: none; color: inherit; display: block;">
                        @if($member->coverImage)
                        <img src="{{ $member->coverImage->responsiveUrl(320) }}" alt="{{ $member->title }} — Cover" class="w-full mb-4" style="box-shadow: 0 12px 32px rgba(0,0,0,0.4);">
                        @endif
                        <span class="pub-label" style="margin-bottom: 4px;">Book {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="pub-heading text-xl mb-1">{{ $member->title }}</h3>
                        @if($member->tagline)<p class="pub-prose text-sm mb-2 pub-accent">{{ $member->tagline }}</p>@endif
                        @if($member->excerpt)<p class="pub-prose text-sm mb-3">{{ $member->excerpt }}</p>@endif
                        @if($member->store?->price_display)<span class="pub-prose text-sm" style="opacity: 0.6;">Individually {{ $member->store->price_display }}</span>@endif
                    </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Narrative --}}
        @if($publication->body || $quote)
        <section class="px-6 py-20">
            <div class="max-w-3xl mx-auto text-center">
                <hr class="pub-divider mb-16">
                <p class="pub-label">The Heart Behind It</p>
                <div class="pub-prose">@markdown($publication->body)</div>
                @if($quote)
                <blockquote class="pub-quote pub-prose" style="text-align: left; display: inline-block;">
                    <p>&ldquo;{{ $quote['text'] }}&rdquo;</p>
                    @if(!empty($quote['citation']))<cite>— {{ $quote['citation'] }}</cite>@endif
                </blockquote>
                @endif
            </div>
        </section>
        @endif

        {{-- CTA / Pricing --}}
        <section id="get-the-bundle" class="px-6 py-24">
            <div class="max-w-3xl mx-auto text-center">
                <hr class="pub-divider mb-16">
                <p class="pub-label">Get the Bundle</p>
                <h2 class="pub-heading text-3xl lg:text-4xl mb-6">{{ $memberCount }} books. One gift.</h2>

                @if($available)
                    <div class="pub-heading text-4xl mb-2 pub-accent">{{ $priceDisplay }}</div>
                    @if($individualTotal || $savings)
                    <p class="pub-prose mb-8">
                        @if($individualTotal)<span style="text-decoration: line-through; opacity: 0.5;">{{ $individualTotal }} individually</span>@endif
                        @if($savings)<span class="pub-accent"> · Save {{ $savings }}</span>@endif
                    </p>
                    @endif
                    <a href="{{ $storeUrl }}" class="pub-btn pub-btn-primary pub-btn-lg">Get the Bundle — {{ $priceDisplay }}</a>
                @else
                    <div>
                        <span class="pub-btn pub-btn-coming-soon"><span class="pub-badge-dot"></span>Coming Soon</span>
                        @if($comingSoonNote)<p class="pub-prose mt-4">{{ $comingSoonNote }}</p>@endif
                    </div>
                @endif
            </div>
        </section>

        {{-- Optional interactive extra (e.g. a bonus quiz between sections) --}}
        @if($publication->reactComponent)
        <section class="px-6 py-12">
            <div class="max-w-5xl mx-auto" data-react-component="{{ $publication->reactComponent->slug }}"></div>
        </section>
        @vite('resources/js/tool-loader.jsx')
        @endif
    </main>

    <footer class="pub-footer">
        <hr class="pub-divider mb-10">
        <p>&copy; {{ date('Y') }} Davina Leong. All rights reserved.</p>
        <p class="mt-2"><a href="{{ route('site.ebooks') }}">← All E-Books</a></p>
    </footer>
</body>
</html>
