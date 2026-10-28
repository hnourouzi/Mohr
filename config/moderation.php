<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Moderation billing (integer tomans, no fractional units)
    |--------------------------------------------------------------------------
    */
    'cost_per_request' => (int) env('MODERATION_COST_PER_REQUEST', 1000),

    /*
    |--------------------------------------------------------------------------
    | OpenRouter / AI moderation
    |--------------------------------------------------------------------------
    */
    'openrouter' => [
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENROUTER_MODEL'),
        'timeout' => 30,
        'http_retries' => 2,
        'http_retry_sleep_ms' => 500,
        'max_tokens' => 1024,
        'max_tokens_ceiling' => 4096,
        'invalid_response_retries' => 1,
        'reasoning_effort' => env('OPENROUTER_REASONING_EFFORT'),
    ],

    'ai_job' => [
        'tries' => (int) env('MODERATION_AI_JOB_TRIES', 3),
        'backoff' => [10, 30, 60],
    ],

];
