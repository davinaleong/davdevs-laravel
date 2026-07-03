<?php

namespace App\Services\Ai;

use App\Models\Setting;

class AiProviderFactory
{
    public static function make(): AiProvider
    {
        $apiKey = Setting::where('key', 'ai_api_key')->value('value');
        $model = Setting::where('key', 'ai_model')->value('value') ?: 'gpt-4o';

        abort_unless($apiKey, 422, 'No AI provider configured. Add an API key in Settings > AI Provider.');

        return new OpenAiProvider(decrypt($apiKey), $model);
    }
}
