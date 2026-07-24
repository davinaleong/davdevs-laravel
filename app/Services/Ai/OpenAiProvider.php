<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProvider implements AiProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $model = 'gpt-4o',
    ) {}

    public function generateTitle(string $topic, string $type): array
    {
        $prompt = "Suggest a concise, compelling title for a {$type} about \"{$topic}\". ".
            'Respond as JSON: {"title": "..."}';

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? ['title' => $result['content']];

        return [
            'title' => $decoded['title'] ?? '',
            'tokens' => $result['tokens'],
        ];
    }

    public function generateExcerpt(string $title, string $type): array
    {
        $prompt = "Write a one-sentence excerpt (max 160 characters) for a {$type} titled \"{$title}\". ".
            'Respond as JSON: {"excerpt": "..."}';

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? ['excerpt' => $result['content']];

        return [
            'excerpt' => $decoded['excerpt'] ?? '',
            'tokens' => $result['tokens'],
        ];
    }

    public function generateContent(string $title, string $type, array $tags = [], string $instructions = ''): array
    {
        $extra = $instructions ? "\nAdditional instructions: {$instructions}" : '';
        $prompt = 'Write a short excerpt (1-2 sentences) and a Markdown body draft (3-5 paragraphs) '.
            "for a {$type} titled \"{$title}\"".
            (count($tags) ? ' covering: '.implode(', ', $tags) : '').
            $extra.
            '. Respond as JSON: {"excerpt": "...", "body": "..."}';

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? ['excerpt' => '', 'body' => $result['content']];

        return [
            'excerpt' => $decoded['excerpt'] ?? '',
            'body' => $decoded['body'] ?? '',
            'tokens' => $result['tokens'],
        ];
    }

    public function auditContent(string $title, string $body): array
    {
        $prompt = "Audit the following post for clarity, SEO, and tone. Title: \"{$title}\". Body:\n\n{$body}\n\n".
            'Respond as JSON: {"suggestions": [{"category": "clarity|seo|tone", "note": "..."}]}';

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? ['suggestions' => []];

        return [
            'suggestions' => $decoded['suggestions'] ?? [],
            'tokens' => $result['tokens'],
        ];
    }

    public function generateBroadcastContent(string $title, string $excerpt, string $url, array $platforms): array
    {
        $platformGuidelines = [
            'linkedin'  => 'professional tone, 150-300 words, end with the link',
            'facebook'  => 'conversational and friendly, 100-200 words, end with the link',
            'instagram' => 'engaging with relevant hashtags, 100-150 words, end with the link',
            'threads'   => 'concise and punchy, under 100 words, end with the link',
        ];
        $guidelines = collect($platforms)
            ->map(fn ($p) => "- {$p}: ".($platformGuidelines[$p] ?? 'concise post, include the link'))
            ->implode("\n");

        $prompt = 'Generate social media post content for these platforms: '.implode(', ', $platforms)."\n".
            "Title: \"{$title}\". Excerpt: \"{$excerpt}\". Link: {$url}\n".
            "Platform guidelines:\n{$guidelines}\n".
            'Respond as JSON with only the requested platform keys: '.
            json_encode(array_fill_keys($platforms, '...'));

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? [];

        return [
            'platforms' => array_intersect_key($decoded, array_flip($platforms)),
            'tokens'    => $result['tokens'],
        ];
    }

    public function sanitizeBroadcast(string $platform, string $text): array
    {
        $prompt = "You are a social media proofreader. Check the following {$platform} post for grammar errors, ".
            'spelling mistakes, and inappropriate or offensive content. '.
            "If corrections are needed, return the corrected version with 'changed' set to true. ".
            "If it is already correct and appropriate, return it unchanged with 'changed' set to false.\n\n".
            "Post:\n{$text}\n\n".
            'Respond as JSON only: {"corrected": "...", "changed": true|false}';

        $result = $this->chat($prompt);
        $decoded = json_decode($result['content'], true) ?? ['corrected' => $text, 'changed' => false];

        return [
            'corrected' => $decoded['corrected'] ?? $text,
            'changed'   => (bool) ($decoded['changed'] ?? false),
            'tokens'    => $result['tokens'],
        ];
    }

    /**
     * @return array{content: string, tokens: int}
     */
    protected function chat(string $prompt): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a concise technical writing assistant. Always respond with valid JSON only, no markdown fences.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI request failed: '.$response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'tokens' => $data['usage']['total_tokens'] ?? 0,
        ];
    }
}
