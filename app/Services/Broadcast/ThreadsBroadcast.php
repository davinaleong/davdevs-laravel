<?php

namespace App\Services\Broadcast;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Broadcast to Threads using the Meta Threads Graph API.
 *
 * Required settings:
 *   broadcast_threads_user_id  – Threads User ID
 *   broadcast_threads_token    – Access Token (threads_basic + threads_content_publish)
 *
 * Flow: create container → publish container
 */
class ThreadsBroadcast implements BroadcastPlatform
{
    public function __construct(
        protected string $userId,
        protected string $token,
    ) {}

    public function post(string $text, string $url, ?string $imageUrl = null): array
    {
        $content = $text."\n\n".$url;

        // Step 1 – create a media container
        $createResponse = Http::withToken($this->token)
            ->timeout(15)
            ->post("https://graph.threads.net/v1.0/{$this->userId}/threads", [
                'media_type' => 'TEXT',
                'text' => $content,
            ]);

        if ($createResponse->failed()) {
            throw new RuntimeException('Threads create container error: '.$createResponse->body());
        }

        $containerId = $createResponse->json('id');

        // Step 2 – publish the container
        $publishResponse = Http::withToken($this->token)
            ->timeout(15)
            ->post("https://graph.threads.net/v1.0/{$this->userId}/threads_publish", [
                'creation_id' => $containerId,
            ]);

        if ($publishResponse->failed()) {
            throw new RuntimeException('Threads publish error: '.$publishResponse->body());
        }

        $postId = $publishResponse->json('id');

        return ['post_url' => $postId ? "https://www.threads.net/@{$this->userId}/post/{$postId}" : null];
    }
}
