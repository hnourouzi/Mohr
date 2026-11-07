<?php

namespace App\DTO\AiModeration;

use App\Enums\ModerationVerdict;

readonly class AiModerationResult
{
    /**
     * @param  list<string>  $categories
     * @param  array<string, mixed>  $rawResponse
     */
    public function __construct(
        public ModerationVerdict $verdict,
        public array $categories,
        public array $rawResponse,
        public ?string $reason = null,
    ) {}

    public function sanitizedRawResponse(): array
    {
        return [
            'id' => data_get($this->rawResponse, 'id'),
            'model' => data_get($this->rawResponse, 'model'),
            'finish_reason' => data_get($this->rawResponse, 'choices.0.finish_reason'),
            'content' => data_get($this->rawResponse, 'choices.0.message.content'),
            'usage' => data_get($this->rawResponse, 'usage'),
        ];
    }
}