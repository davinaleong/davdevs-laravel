@php
$jsonLdBase = [
    '@context'      => 'https://schema.org',
    'headline'      => $entry->title,
    'description'   => $entry->excerpt,
    'datePublished' => $entry->published_at?->toIso8601String(),
    'dateModified'  => $entry->updated_at->toIso8601String(),
    'image'         => $entry->ogImage?->url,
    'author'        => ['@type' => 'Person', 'name' => 'Davina Leong'],
];
$jsonLd = match ($entry->contentType->slug) {
    'tool'   => json_encode(array_merge($jsonLdBase, ['@type' => 'SoftwareApplication', 'applicationCategory' => 'DeveloperApplication'])),
    'sermon' => json_encode(array_merge($jsonLdBase, [
        '@type'       => 'VideoObject',
        'name'        => $entry->title,
        'uploadDate'  => $entry->published_at?->toIso8601String(),
        'thumbnailUrl'=> $entry->videoEmbeds->first()?->thumbnail_url,
        'embedUrl'    => $entry->videoEmbeds->first() ? "https://www.youtube-nocookie.com/embed/{$entry->videoEmbeds->first()->video_id}" : null,
    ])),
    default  => json_encode(array_merge($jsonLdBase, ['@type' => 'Article'])),
};
@endphp
<x-site-layout :title="$entry->title" :description="$entry->excerpt" :og-image="$entry->ogImage?->url" :json-ld="$jsonLd">
    <article style="max-width:680px;margin:0 auto;padding:48px 36px;">
        <div style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-faint);margin-bottom:12px;">
            {{ $entry->contentType->name }} @if($entry->category) · {{ $entry->category->name }} @endif · {{ $entry->published_at?->format('M j, Y') }} · {{ $entry->read_time }} min read
        </div>
        <h1 style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:var(--text-primary);margin:0 0 20px;line-height:1.2;">{{ $entry->title }}</h1>

        @if($entry->tags->isNotEmpty())
        <div style="display:flex;gap:6px;margin-bottom:24px;flex-wrap:wrap;">
            @foreach($entry->tags as $tag)
            <span style="font-family:'JetBrains Mono',monospace;font-size:8px;padding:1px 5px;border-radius:2px;color:var(--accent);background:var(--accent-tint);border:1px solid var(--accent-border);">{{ $tag->name }}</span>
            @endforeach
        </div>
        @endif

        @if($entry->contentType->react_island && $entry->reactComponent)
        <div data-react-component="{{ $entry->reactComponent->slug }}" style="margin-bottom:32px;"></div>
        @vite('resources/js/tool-loader.jsx')
        @endif

        <div class="prose" style="font-family:'Lora',serif;font-size:14px;line-height:1.8;color:var(--text-body);">@markdown($entry->body)</div>

        @if($entry->videoEmbeds->isNotEmpty())
        <div style="margin-top:32px;display:flex;flex-direction:column;gap:16px;">
            @foreach($entry->videoEmbeds as $video)
            <div>
                <div style="aspect-ratio:16/9;background:var(--bg-surface-1);border-radius:8px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;"
                     x-data="{ loaded: false }">
                    <img x-show="!loaded" src="{{ $video->thumbnail_url }}" style="width:100%;height:100%;object-fit:cover;">
                    <button x-show="!loaded" @click="loaded = true" style="position:absolute;inset:0;background:rgba(0,0,0,0.3);border:none;cursor:pointer;color:#fff;font-size:40px;">▶</button>
                    <iframe x-show="loaded" x-cloak :src="loaded ? 'https://www.youtube-nocookie.com/embed/{{ $video->video_id }}' : ''" style="width:100%;height:100%;border:none;" allowfullscreen></iframe>
                </div>
                <p style="font-family:'Inter',sans-serif;font-size:12px;color:var(--text-secondary);margin-top:8px;">{{ $video->title }}</p>
            </div>
            @endforeach
        </div>
        @endif

        @if($entry->images->isNotEmpty())
        <div style="margin-top:32px;display:flex;flex-direction:column;gap:16px;">
            @foreach($entry->images as $image)
            <figure style="margin:0;">
                <img src="{{ $image->responsiveUrl(680) }}" alt="{{ $image->alt }}" style="width:100%;border-radius:8px;">
                @if($image->pivot->caption_override ?? $image->caption)
                <figcaption style="font-family:'Inter',sans-serif;font-size:11px;color:var(--text-muted);margin-top:6px;">{{ $image->pivot->caption_override ?: $image->caption }}</figcaption>
                @endif
            </figure>
            @endforeach
        </div>
        @endif

        {{-- Like button --}}
        <div style="margin-top:32px;display:flex;align-items:center;gap:8px;"
             x-data="{ liked: false, count: {{ $entry->reactions()->count() }} }"
             x-init="liked = document.cookie.includes('reaction_token')">
            <button @click="
                fetch('{{ route('api.reactions.toggle', ['entry', $entry->id]) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
                }).then(r => r.json()).then(d => { liked = d.liked; count = d.count })
            " style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <span :style="liked ? 'color:var(--like-icon-liked)' : 'color:var(--like-icon)'" style="font-size:16px;">♥</span>
                <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--like-count);" x-text="count"></span>
            </button>
        </div>

        {{-- Share links --}}
        @php $shareUrl = url()->current(); @endphp
        <div style="margin-top:16px;display:flex;gap:10px;">
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-muted);text-decoration:none;">LinkedIn</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener noreferrer" style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-muted);text-decoration:none;">Facebook</a>
            <a href="https://www.threads.net/intent/post?text={{ urlencode($entry->title.' '.$shareUrl) }}" target="_blank" rel="noopener noreferrer" style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-muted);text-decoration:none;">Threads</a>
            <button onclick="navigator.clipboard.writeText('{{ $shareUrl }}')" style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--text-muted);background:none;border:none;cursor:pointer;">Copy link</button>
        </div>
    </article>

    <script>
        window.addEventListener('scroll', () => {
            const article = document.querySelector('article');
            if (!article) return;
            const total = article.scrollHeight - window.innerHeight;
            const scrolled = Math.min(100, Math.max(0, (window.scrollY / total) * 100));
            document.getElementById('reading-progress').style.width = scrolled + '%';
        });
    </script>
</x-site-layout>
