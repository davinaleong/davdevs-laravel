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

    public function generateContent(string $title, string $type, array $tags = []): array
    {
        $prompt = 'Write a short excerpt (1-2 sentences) and a Markdown body draft (3-5 paragraphs) '.
            "for a {$type} titled \"{$title}\"".
            (count($tags) ? ' covering: '.implode(', ', $tags) : '').
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
