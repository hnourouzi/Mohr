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
        'model' => env('OPENROUTER_MODEL', 'openrouter/free'),
        'timeout' => (int) env('OPENROUTER_TIMEOUT', 30),
        'max_tokens' => (int) env('OPENROUTER_MAX_TOKENS', 256),
        'http_retries' => (int) env('OPENROUTER_HTTP_RETRIES', 2),
        'http_retry_sleep_ms' => (int) env('OPENROUTER_HTTP_RETRY_SLEEP_MS', 500),
    ],

    'ai_job' => [
        'tries' => (int) env('MODERATION_AI_JOB_TRIES', 3),
        'backoff' => [10, 30, 60],
    ],

];
