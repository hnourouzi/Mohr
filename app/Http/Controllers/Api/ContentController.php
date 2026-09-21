<?php

namespace App\Http\Controllers\Api;

use App\Actions\Content\SubmitContentAction;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentRequest;
use App\Http\Resources\ContentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contents = $request->user()
            ->contents()
            ->latest()
            ->paginate(10);

        return ContentResource::collection($contents)->response();
    }

    public function store(StoreContentRequest $request, SubmitContentAction $action): JsonResponse
    {
        try {
            $content = $action->execute(
                user: $request->user(),
                body: $request->validated('body'),
                channel: $request->validated('channel'),
            );
        } catch (InsufficientBalanceException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new ContentResource($content))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, \App\Models\Content $content): JsonResponse
    {
        $this->authorize('view', $content);

        $content->load('logs');

        return (new ContentResource($content))->response();
    }
}