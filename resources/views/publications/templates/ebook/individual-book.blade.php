@php
    $hasVariants = $publication->variants->isNotEmpty();
    $storeUrl = $publication->store?->ls_store_url;
    $priceDisplay = $publication->store?->price_display;
    $comingSoonNote = $publication->getMeta('store_note');
    $available = $hasVariants || filled($storeUrl);
    $themes = json_decode($publication->getMeta('themes') ?? '[]', true) ?: [];
    $quote = json_decode($publication->getMeta('quote') ?? 'null', true);
    $backImage = $publication->images->first();
@endphp
<!DOCTYPE html>
<html lang="en">
@include('publications.templates.ebook.partials.head')
<body class="pub-book">
    {{-- Nav --}}
    <header class="pub-nav">
        <span class="pub-nav-title">{{ $publication->title }}</span>
        @if($available)
            <a href="#get-the-book" class="pub-btn pub-btn-outline pub-btn-sm">{{ $hasVariants ? 'Choose Your Version' : 'Get the Book' }}</a>
        @else
            <span class="pub-btn pub-btn-coming-soon pub-btn-sm">Coming Soon</span>
        @endif
    </header>

    <main>
        {{-- Hero --}}
        <section class="px-6 py-20">
            <div class="max-w-5xl mx-auto grid gap-16 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="pub-label">An E-Book by Davina Leong</p>
                    <h1 class="pub-heading text-4xl lg:text-6xl mb-4">{{ $publication->title }}</h1>
                    @if($publication->tagline)
                    <p class="pub-heading text-xl mb-6 pub-accent" style="font-weight: 500;">{{ $publication->tagline }}</p>
                    @endif
                    @if($publication->excerpt)
                    <p class="pub-prose text-lg mb-8">{{ $publication->excerpt }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-4">
                        @if($available)
                            <a href="#get-the-book" class="pub-btn pub-btn-primary">
                                {{ $hasVariants ? 'Choose Your Version' : 'Get the Book — '.$priceDisplay }}
                            </a>
                        @else
                            <span class="pub-btn pub-btn-coming-soon">Coming Soon</span>
                        @endif
                        <a href="#about" class="pub-prose">+ Read more</a>
                    </div>
                </div>

                @if($publication->coverImage)
                <div class="flex justify-center lg:justify-end">
                    <img src="{{ $publication->coverImage->responsiveUrl(640) }}" alt="{{ $publication->title }} — Cover" class="pub-cover-img">
                </div>
                @endif
            </div>
        </section>

        {{-- About --}}
        <section id="about" class="px-6 py-20">
            <div class="max-w-5xl mx-auto">
                <hr class="pub-divider mb-16">
                <div class="grid gap-12 lg:grid-cols-2">
                    @if($publication->excerpt)
                    <div class="pub-card pub-prose">
                        <p>{{ $publication->excerpt }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="pub-label">About the Book</p>
                        <h2 class="pub-heading text-3xl mb-6">{{ $publication->tagline ?: 'About '.$publication->title }}</h2>
                        <div class="pub-prose">@markdown($publication->body)</div>

                        @if($quote)
                        <blockquote class="pub-quote pub-prose">
                            <p>&ldquo;{{ $quote['text'] }}&rdquo;</p>
                            @if(!empty($quote['citation']))<cite>— {{ $quote['citation'] }}</cite>@endif
                        </blockquote>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Themes --}}
        @if(!empty($themes))
        <section class="px-6 py-20">
            <div class="max-w-5xl mx-auto">
                <hr class="pub-divider mb-16">
                <p class="pub-label text-center">What's Inside</p>
                <h2 class="pub-heading text-3xl lg:text-4xl mb-12 text-center">Themes woven through every page</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($themes as $theme)
                    <div class="pub-card">
                        <span class="pub-accent text-2xl" aria-hidden="true">{{ $theme['icon'] ?? '✦' }}</span>
                        <h3 class="pub-prose font-semibold text-lg mt-3 mb-2" style="opacity: 1;">{{ $theme['title'] ?? '' }}</h3>
                        <p class="pub-prose">{{ $theme['body'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- CTA / Pricing --}}
        <section id="get-the-book" class="px-6 py-24">
            <div class="max-w-3xl mx-auto text-center">
                <hr class="pub-divider mb-16">
                <p class="pub-label">Get Your Copy</p>
                <h2 class="pub-heading text-3xl lg:text-4xl mb-10">{{ $hasVariants ? 'Choose Your Version' : 'Get Your Copy' }}</h2>

                @if($available)
                    @if($hasVariants)
                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach($publication->variants as $variant)
                        <div class="pub-card">
                            <h3 class="pub-prose font-semibold text-lg mb-2" style="opacity: 1;">{{ $variant->name }}</h3>
                            <div class="pub-heading text-2xl mb-4 pub-accent">{{ $variant->price_display }}</div>
                            <a href="{{ $variant->ls_product_id ? \Illuminate\Support\Str::finish($storeUrl ?? '#', '/').$variant->ls_product_id : '#' }}" class="pub-btn pub-btn-primary">
                                Get This Version
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <a href="{{ $storeUrl }}" class="pub-btn pub-btn-primary pub-btn-lg">Get the Book — {{ $priceDisplay }}</a>
                    @endif
                @else
                    <div>
                        <span class="pub-btn pub-btn-coming-soon"><span class="pub-badge-dot"></span>Coming Soon</span>
                        @if($comingSoonNote)<p class="pub-prose mt-4">{{ $comingSoonNote }}</p>@endif
                    </div>
                @endif
            </div>
        </section>

        {{-- Back cover --}}
        @if($backImage)
        <section class="px-6 py-20">
            <div class="max-w-5xl mx-auto flex justify-center">
                <img src="{{ $backImage->responsiveUrl(480) }}" alt="{{ $publication->title }} — Back Cover" class="pub-cover-img" style="opacity: 0.85;">
            </div>
        </section>
        @endif

        {{-- Optional interactive extra --}}
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
