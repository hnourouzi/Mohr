<?php

namespace App\DTO\AiModeration;

readonly class AiModerationResult
{
    public function __construct(
        public bool $flagged,
        public array $categories,
        public array $rawResponse,
        public ?string $reason = null,
    ) {}

    /**
     * Strip message payloads before persisting on the content record.
     */
    public function sanitizedRawResponse(): array
    {
        $response = $this->rawResponse;

        if (isset($response['choices']) && is_array($response['choices'])) {
            foreach ($response['choices'] as $index => $choice) {
                if (isset($choice['message']['content'])) {
                    unset($response['choices'][$index]['message']['content']);
                }
            }
        }

        return $response;
    }
}
