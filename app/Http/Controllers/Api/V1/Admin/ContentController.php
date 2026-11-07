<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminIndexContentsRequest;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    public function index(AdminIndexContentsRequest $request): JsonResponse
    {
        $perPage = (int) ($request->validated('per_page') ?? 20);

        $contents = Content::query()
            ->with('user')
            ->when($request->validated('status'), fn ($q) => $q->where('status', $request->validated('status')))
            ->latest()
            ->paginate($perPage);

        return ContentResource::collection($contents)->response();
    }
}
