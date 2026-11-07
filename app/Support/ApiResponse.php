<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function error(
        string $message,
        int $status = 400,
        ?string $code = null,
        array $errors = [],
    ): JsonResponse {
        $payload = ['message' => $message];

        if ($code !== null) {
            $payload['code'] = $code;
        }

        if ($errors !== []) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
