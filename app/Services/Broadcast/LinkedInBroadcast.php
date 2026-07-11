<?php

namespace App\Services\Broadcast;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Broadcast to a LinkedIn personal profile using the UGC Posts API.
 *
 * Required settings:
 *   broadcast_linkedin_token  – OAuth2 access token (must have w_member_social scope)
 *   broadcast_linkedin_urn    – LinkedIn member URN, e.g. "urn:li:person:XXXXX"
 */
class LinkedInBroadcast implements BroadcastPlatform
{
    public function __construct(
        protected string $token,
        protected string $authorUrn,
    ) {}

    public function post(string $text, string $url, ?string $imageUrl = null): array
    {
        $body = [
            'author' => $this->authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => ['text' => $text],
                    'shareMediaCategory' => 'ARTICLE',
                    'media' => [[
                        'status' => 'READY',
                        'originalUrl' => $url,
                    ]],
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        $response = Http::withToken($this->token)
            ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
            ->timeout(15)
            ->post('https://api.linkedin.com/v2/ugcPosts', $body);

        if ($response->failed()) {
            throw new RuntimeException('LinkedIn API error: '.$response->body());
        }

        $postId = $response->header('x-restli-id') ?: ($response->json('id') ?? null);

        return ['post_url' => $postId ? "https://www.linkedin.com/feed/update/{$postId}" : null];
    }
}
