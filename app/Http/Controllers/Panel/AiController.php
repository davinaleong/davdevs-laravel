<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Http\Request;
use Throwable;

class AiController extends Controller
{
    public function generateTitle(Request $request)
    {
        $data = $request->validate([
            'topic' => 'required|string|max:500',
            'type' => 'required|string|max:100',
        ]);

        $start = microtime(true);

        try {
            $provider = AiProviderFactory::make();
            $result = $provider->generateTitle($data['topic'], $data['type']);

            $this->logCall('generate-title', $result['tokens'], $start);

            return response()->json(['title' => $result['title']]);
        } catch (Throwable $e) {
            $this->logCall('generate-title', 0, $start, $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function generateExcerpt(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
        ]);

        $start = microtime(true);

        try {
            $provider = AiProviderFactory::make();
            $result = $provider->generateExcerpt($data['title'], $data['type']);

            $this->logCall('generate-excerpt', $result['tokens'], $start);

            return response()->json(['excerpt' => $result['excerpt']]);
        } catch (Throwable $e) {
            $this->logCall('generate-excerpt', 0, $start, $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|string|max:100',
            'tags' => 'nullable|array',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $start = microtime(true);

        try {
            $provider = AiProviderFactory::make();
            $result = $provider->generateContent(
                $data['title'],
                $data['type'],
                $data['tags'] ?? [],
                $data['instructions'] ?? '',
            );

            $this->logCall('generate', $result['tokens'], $start);

            return response()->json(['excerpt' => $result['excerpt'], 'body' => $result['body']]);
        } catch (Throwable $e) {
            $this->logCall('generate', 0, $start, $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function audit(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'body' => 'required|string',
        ]);

        $start = microtime(true);

        try {
            $provider = AiProviderFactory::make();
            $result = $provider->auditContent($data['title'], $data['body']);

            $this->logCall('audit', $result['tokens'], $start);

            return response()->json(['suggestions' => $result['suggestions']]);
        } catch (Throwable $e) {
            $this->logCall('audit', 0, $start, $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    protected function logCall(string $action, int $tokens, float $start, ?string $error = null): void
    {
        $durationMs = (int) round((microtime(true) - $start) * 1000);

        ActivityLog::create([
            'channel' => 'ai',
            'level' => $error ? 'error' : 'info',
            'message' => $error ? "AI {$action} failed: {$error}" : "AI {$action} completed ({$tokens} tokens)",
            'context' => ['action' => $action, 'tokens' => $tokens, 'error' => $error],
            'duration_ms' => min($durationMs, 65535),
        ]);
    }
}
