<?php

namespace App\Services\Broadcast;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Broadcast to Instagram using the Instagram Graph API.
 *
 * Note: the Instagram Graph API only supports media posts (photo/video/carousel),
 * not text-only posts. This implementation posts the URL as a caption-only image
 * post using the OG image of the content, falling back to a text + link caption
 * if no image is available.
 *
 * Required settings:
 *   broadcast_instagram_user_id  – Instagram Business/Creator User ID
 *   broadcast_instagram_token    – Access Token (instagram_basic + instagram_content_publish)
 */
class InstagramBroadcast implements BroadcastPlatform
{
    public function __construct(
        protected string $userId,
        protected string $token,
    ) {}

    public function post(string $text, string $url, ?string $imageUrl = null): array
    {
        if (! $imageUrl) {
            throw new RuntimeException(
                'Instagram requires an image. Set an OG image on the entry/publication before broadcasting.'
            );
        }

        $caption = $text."\n\n".$url;

        // Step 1 – create media container
        $createResponse = Http::withToken($this->token)
            ->timeout(15)
            ->post("https://graph.facebook.com/v19.0/{$this->userId}/media", [
                'image_url' => $imageUrl,
                'caption' => $caption,
            ]);

        if ($createResponse->failed()) {
            throw new RuntimeException('Instagram create container error: '.$createResponse->body());
        }

        $containerId = $createResponse->json('id');

        // Step 2 – publish
        $publishResponse = Http::withToken($this->token)
            ->timeout(15)
            ->post("https://graph.facebook.com/v19.0/{$this->userId}/media_publish", [
                'creation_id' => $containerId,
            ]);

        if ($publishResponse->failed()) {
            throw new RuntimeException('Instagram publish error: '.$publishResponse->body());
        }

        $postId = $publishResponse->json('id');

        return ['post_url' => $postId ? "https://www.instagram.com/p/{$postId}/" : null];
    }
}
