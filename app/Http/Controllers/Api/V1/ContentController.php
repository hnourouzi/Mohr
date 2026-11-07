<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Content\RetryModerationAction;
use App\Actions\Content\SubmitContentAction;
use App\Enums\ContentStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexContentsRequest;
use App\Http\Requests\StoreContentRequest;
use App\Http\Resources\ContentResource;
use App\Http\Resources\ModerationLogResource;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(IndexContentsRequest $request): JsonResponse
    {
        $perPage = (int) ($request->validated('per_page') ?? 15);

        $contents = $request->user()
            ->contents()
            ->when($request->validated('status'), fn ($q) => $q->where('status', $request->validated('status')))
            ->when($request->validated('channel'), fn ($q) => $q->where('channel', $request->validated('channel')))
            ->latest()
            ->paginate($perPage);

        return ContentResource::collection($contents)->response();
    }

    public function store(StoreContentRequest $request, SubmitContentAction $action): JsonResponse
    {
        $content = $action->execute(
            user: $request->user(),
            body: $request->validated('body'),
            channel: $request->validated('channel'),
        );

        return (new ContentResource($content))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Content $content): JsonResponse
    {
        $this->authorize('view', $content);

        $content->load('logs');

        return (new ContentResource($content))->response();
    }

    public function logs(Request $request, Content $content): JsonResponse
    {
        $this->authorize('view', $content);

        $logs = $content->logs()->latest()->get();

        return response()->json([
            'data' => ModerationLogResource::collection($logs),
        ]);
    }

    public function retry(Request $request, Content $content, RetryModerationAction $action): JsonResponse
    {
        $this->authorize('view', $content);

        if (! $content->isRetryable()) {
            throw new InvalidStatusTransitionException($content->status, ContentStatus::Processing);
        }

        $action->execute($content);

        return response()->json([
            'message' => 'درخواست بررسی مجدد در صف قرار گرفت.',
            'content' => ContentResource::make($content->fresh()),
        ], 202);
    }
}
