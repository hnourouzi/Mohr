<?php

namespace App\Services\AiModeration;

use App\DTO\AiModeration\AiModerationResult;
use App\Services\AiModeration\AiModerationServiceInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiModerationService implements AiModerationServiceInterface
{
    public function moderate(string $content): AiModerationResult
    {
        $response = Http::withToken(config('services.openai.api_key'))
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.openai.com/v1/moderations', [
                'model' => 'omni-moderation-latest',
                'input' => $content,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'خطا در ارتباط با OpenAI: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $result = $response->json('results.0');
        
        return new AiModerationResult(
            flagged: $result['flagged'] ?? false,
            categories: array_keys(
                array_filter($result['categories'] ?? [])
            ),
            rawResponse: $response->json(),
        );
    }
}