<?php

namespace App\Services\Broadcast;

/**
 * Contract for a social-media broadcast platform.
 */
interface BroadcastPlatform
{
    /**
     * Post a message to the platform.
     *
     * @param  string  $text     The text content of the post.
     * @param  string  $url      The canonical URL to share.
     * @param  string|null  $imageUrl  Optional image URL (used where supported).
     * @return array{post_url: string|null}
     *
     * @throws \RuntimeException on API failure
     */
    public function post(string $text, string $url, ?string $imageUrl = null): array;
}
