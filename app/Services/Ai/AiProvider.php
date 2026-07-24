<?php

namespace App\Services\Ai;

interface AiProvider
{
    /**
     * Generate a title suggestion from a topic/prompt.
     *
     * @return array{title: string, tokens: int}
     */
    public function generateTitle(string $topic, string $type): array;

    /**
     * Generate an excerpt from a title.
     *
     * @return array{excerpt: string, tokens: int}
     */
    public function generateExcerpt(string $title, string $type): array;

    /**
     * Generate an excerpt + body draft from a title/type/tags brief.
     * An optional $instructions string lets the user guide the AI.
     *
     * @return array{excerpt: string, body: string, tokens: int}
     */
    public function generateContent(string $title, string $type, array $tags = [], string $instructions = ''): array;

    /**
     * Audit existing content and return structured suggestions.
     *
     * @return array{suggestions: array<int, array{category: string, note: string}>, tokens: int}
     */
    public function auditContent(string $title, string $body): array;

    /**
     * Generate platform-specific social media post content.
     *
     * @param  string[]  $platforms
     * @return array{platforms: array<string, string>, tokens: int}
     */
    public function generateBroadcastContent(string $title, string $excerpt, string $url, array $platforms): array;

    /**
     * Sanity-check and auto-correct a social media post.
     *
     * @return array{corrected: string, changed: bool, tokens: int}
     */
    public function sanitizeBroadcast(string $platform, string $text): array;
}
