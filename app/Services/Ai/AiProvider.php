<?php

namespace App\Services\Ai;

interface AiProvider
{
    /**
     * Generate an excerpt + body draft from a title/type/tags brief.
     *
     * @return array{excerpt: string, body: string, tokens: int}
     */
    public function generateContent(string $title, string $type, array $tags = []): array;

    /**
     * Audit existing content and return structured suggestions.
     *
     * @return array{suggestions: array<int, array{category: string, note: string}>, tokens: int}
     */
    public function auditContent(string $title, string $body): array;
}
