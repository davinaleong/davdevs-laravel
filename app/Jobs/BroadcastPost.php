<?php

namespace App\Jobs;

use App\Models\Entry;
use App\Models\Publication;
use App\Models\Setting;
use App\Models\SocialBroadcast;
use App\Services\Broadcast\FacebookBroadcast;
use App\Services\Broadcast\InstagramBroadcast;
use App\Services\Broadcast\LinkedInBroadcast;
use App\Services\Broadcast\ThreadsBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class BroadcastPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string[] */
    public array $platforms;

    public function __construct(
        public Entry|Publication $broadcastable,
        array $platforms,
    ) {
        $this->platforms = $platforms;
    }

    public function handle(): void
    {
        $settings = Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();

        $title = $this->broadcastable->title;
        $excerpt = $this->broadcastable->excerpt ?? '';
        $slug = $this->broadcastable->slug;

        // Build canonical URL based on model type
        $url = $this->broadcastable instanceof Entry
            ? route('site.show', [$this->broadcastable->contentType->slug, $slug])
            : route('site.ebooks.show', $slug);

        $text = $title.($excerpt ? "\n\n".$excerpt : '');

        // OG image URL (Entry uses ogImage, Publication uses ogImage ?? coverImage)
        $imageUrl = $this->broadcastable instanceof Entry
            ? $this->broadcastable->ogImage?->url
            : ($this->broadcastable->ogImage?->url ?? $this->broadcastable->coverImage?->url);

        foreach ($this->platforms as $platform) {
            $broadcast = SocialBroadcast::create([
                'broadcastable_type' => get_class($this->broadcastable),
                'broadcastable_id' => $this->broadcastable->id,
                'platform' => $platform,
                'status' => 'pending',
            ]);

            try {
                $driver = $this->makeDriver($platform, $settings);
                $result = $driver->post($text, $url, $imageUrl);
                $broadcast->update(['status' => 'sent', 'post_url' => $result['post_url']]);
            } catch (Throwable $e) {
                $broadcast->update(['status' => 'failed', 'error' => $e->getMessage()]);
            }
        }
    }

    protected function makeDriver(string $platform, array $settings): \App\Services\Broadcast\BroadcastPlatform
    {
        return match ($platform) {
            'linkedin' => new LinkedInBroadcast(
                $settings['broadcast_linkedin_token'] ?? '',
                $settings['broadcast_linkedin_urn'] ?? '',
            ),
            'facebook' => new FacebookBroadcast(
                $settings['broadcast_facebook_page_id'] ?? '',
                $settings['broadcast_facebook_token'] ?? '',
            ),
            'instagram' => new InstagramBroadcast(
                $settings['broadcast_instagram_user_id'] ?? '',
                $settings['broadcast_instagram_token'] ?? '',
            ),
            'threads' => new ThreadsBroadcast(
                $settings['broadcast_threads_user_id'] ?? '',
                $settings['broadcast_threads_token'] ?? '',
            ),
            default => throw new \InvalidArgumentException("Unknown broadcast platform: {$platform}"),
        };
    }
}
