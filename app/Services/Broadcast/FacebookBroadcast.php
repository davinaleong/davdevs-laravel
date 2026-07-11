<?php

namespace App\Services\Broadcast;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Broadcast to a Facebook Page using the Graph API.
 *
 * Required settings:
 *   broadcast_facebook_page_id    – Facebook Page ID
 *   broadcast_facebook_token      – Page Access Token (publish_pages + pages_manage_posts)
 */
class FacebookBroadcast implements BroadcastPlatform
{
    public function __construct(
        protected string $pageId,
        protected string $token,
    ) {}

    public function post(string $text, string $url, ?string $imageUrl = null): array
    {
        $payload = ['message' => $text, 'link' => $url];

        $response = Http::withToken($this->token)
            ->timeout(15)
            ->post("https://graph.facebook.com/v19.0/{$this->pageId}/feed", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Facebook API error: '.$response->body());
        }

        $postId = $response->json('id');

        return ['post_url' => $postId ? "https://www.facebook.com/{$postId}" : null];
    }
}
